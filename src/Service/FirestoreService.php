<?php
declare(strict_types=1);

namespace App\Service;

use Cake\Core\Configure;
use RuntimeException;

class FirestoreService
{
    private string $projectId;

    private string $database;

    private string $accessToken;

    private array $serviceAccount;

    private ?string $generatedAccessToken = null;

    private int $generatedAccessTokenExpiresAt = 0;

    public function __construct(?array $config = null)
    {
        $config = $config ?? (array)Configure::read('Firebase');

        $this->projectId = (string)($config['projectId'] ?? '');
        $this->database = (string)($config['database'] ?? '(default)');
        $this->accessToken = (string)($config['accessToken'] ?? '');
        $this->serviceAccount = $this->loadServiceAccount($config);
    }

    public function isConfigured(): bool
    {
        return $this->projectId !== '' && ($this->accessToken !== '' || $this->serviceAccount !== []);
    }

    public function listDocuments(string $collection): array
    {
        $documents = [];
        $pageToken = null;

        do {
            $query = ['pageSize' => '300'];

            if ($pageToken !== null) {
                $query['pageToken'] = $pageToken;
            }

            $response = $this->request('GET', $this->documentsUrl($collection, $query));

            foreach (($response['documents'] ?? []) as $document) {
                $documents[] = $this->decodeDocument($document);
            }

            $pageToken = $response['nextPageToken'] ?? null;
        } while ($pageToken !== null);

        return $documents;
    }

    public function getDocument(string $collection, string $documentId): ?array
    {
        $response = $this->request(
            'GET',
            $this->documentsUrl($collection . '/' . $documentId),
            null,
            true
        );

        if ($response === null) {
            return null;
        }

        return $this->decodeDocument($response);
    }

    public function setDocument(string $collection, string $documentId, array $data): array
    {
        $response = $this->request(
            'PATCH',
            $this->documentsUrl($collection . '/' . $documentId),
            ['fields' => $this->encodeMapFields($data)]
        );

        return $this->decodeDocument((array)$response);
    }

    public function deleteDocument(string $collection, string $documentId): void
    {
        $this->request('DELETE', $this->documentsUrl($collection . '/' . $documentId));
    }

    private function loadServiceAccount(array $config): array
    {
        $json = trim((string)($config['serviceAccountJson'] ?? ''));
        $path = trim((string)($config['serviceAccountPath'] ?? ''));

        if ($json === '' && $path !== '') {
            if (!is_file($path)) {
                throw new RuntimeException("Firebase service account file was not found: {$path}");
            }

            $json = (string)file_get_contents($path);
        }

        if ($json === '') {
            return [];
        }

        $serviceAccount = json_decode($json, true);

        if (!is_array($serviceAccount)) {
            throw new RuntimeException('Firebase service account JSON is not valid.');
        }

        foreach (['client_email', 'private_key', 'token_uri'] as $key) {
            if (empty($serviceAccount[$key])) {
                throw new RuntimeException("Firebase service account JSON is missing {$key}.");
            }
        }

        return $serviceAccount;
    }

    private function documentsUrl(string $path, array $query = []): string
    {
        $encodedPath = implode('/', array_map('rawurlencode', explode('/', $path)));
        $url = sprintf(
            'https://firestore.googleapis.com/v1/projects/%s/databases/%s/documents/%s',
            rawurlencode($this->projectId),
            rawurlencode($this->database),
            $encodedPath
        );

        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    private function request(string $method, string $url, ?array $payload = null, bool $allowNotFound = false): ?array
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('Firebase is not configured.');
        }

        $handle = curl_init($url);
        $headers = [
            'Authorization: Bearer ' . $this->getAccessToken(),
            'Content-Type: application/json',
        ];

        curl_setopt_array($handle, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
        ]);

        if ($payload !== null) {
            curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_SLASHES));
        }

        $response = curl_exec($handle);
        $statusCode = (int)curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $error = curl_error($handle);
        curl_close($handle);

        if ($allowNotFound && $statusCode === 404) {
            return null;
        }

        if ($response === false || $statusCode < 200 || $statusCode >= 300) {
            throw new RuntimeException("Firestore request failed with HTTP {$statusCode}: {$error} {$response}");
        }

        if (trim((string)$response) === '') {
            return [];
        }

        $decoded = json_decode((string)$response, true);

        if (!is_array($decoded)) {
            throw new RuntimeException('Firestore returned invalid JSON.');
        }

        return $decoded;
    }

    private function getAccessToken(): string
    {
        if ($this->accessToken !== '') {
            return $this->accessToken;
        }

        if ($this->generatedAccessToken !== null && $this->generatedAccessTokenExpiresAt > time() + 60) {
            return $this->generatedAccessToken;
        }

        $this->refreshServiceAccountAccessToken();

        return (string)$this->generatedAccessToken;
    }

    private function refreshServiceAccountAccessToken(): void
    {
        $now = time();
        $tokenUri = (string)$this->serviceAccount['token_uri'];
        $assertion = $this->createJwtAssertion($now);

        $handle = curl_init($tokenUri);
        curl_setopt_array($handle, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $assertion,
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($handle);
        $statusCode = (int)curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $error = curl_error($handle);
        curl_close($handle);

        if ($response === false || $statusCode < 200 || $statusCode >= 300) {
            throw new RuntimeException("Could not get Firebase access token: {$error} {$response}");
        }

        $decoded = json_decode((string)$response, true);

        if (!is_array($decoded) || empty($decoded['access_token'])) {
            throw new RuntimeException('Firebase token response is not valid.');
        }

        $this->generatedAccessToken = (string)$decoded['access_token'];
        $this->generatedAccessTokenExpiresAt = $now + (int)($decoded['expires_in'] ?? 3600);
    }

    private function createJwtAssertion(int $now): string
    {
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];
        $payload = [
            'iss' => (string)$this->serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/datastore',
            'aud' => (string)$this->serviceAccount['token_uri'],
            'iat' => $now,
            'exp' => $now + 3600,
        ];
        $signingInput = $this->base64UrlEncode(json_encode($header)) . '.' .
            $this->base64UrlEncode(json_encode($payload));

        $success = openssl_sign(
            $signingInput,
            $signature,
            (string)$this->serviceAccount['private_key'],
            OPENSSL_ALGO_SHA256
        );

        if (!$success) {
            throw new RuntimeException('Could not sign Firebase service account JWT.');
        }

        return $signingInput . '.' . $this->base64UrlEncode($signature);
    }

    private function decodeDocument(array $document): array
    {
        $data = $this->decodeFields((array)($document['fields'] ?? []));
        $name = (string)($document['name'] ?? '');
        $documentId = $name !== '' ? substr($name, (int)strrpos($name, '/') + 1) : '';

        if (!array_key_exists('id', $data) && $documentId !== '') {
            $data['id'] = $documentId;
        }

        $data['_document_id'] = $documentId;

        return $data;
    }

    private function decodeFields(array $fields): array
    {
        $data = [];

        foreach ($fields as $key => $value) {
            $data[$key] = $this->decodeValue((array)$value);
        }

        return $data;
    }

    private function encodeMapFields(array $data): array
    {
        $fields = [];

        foreach ($data as $key => $value) {
            $fields[(string)$key] = $this->encodeValue($value);
        }

        return $fields;
    }

    private function encodeValue($value): array
    {
        if ($value === null || $value === '') {
            return ['nullValue' => null];
        }

        if (is_bool($value)) {
            return ['booleanValue' => $value];
        }

        if (is_int($value)) {
            return ['integerValue' => (string)$value];
        }

        if (is_float($value)) {
            return ['doubleValue' => $value];
        }

        if (is_array($value)) {
            if ($this->isList($value)) {
                return [
                    'arrayValue' => [
                        'values' => array_map([$this, 'encodeValue'], $value),
                    ],
                ];
            }

            return [
                'mapValue' => [
                    'fields' => $this->encodeMapFields($value),
                ],
            ];
        }

        return ['stringValue' => (string)$value];
    }

    private function decodeValue(array $value)
    {
        if (array_key_exists('nullValue', $value)) {
            return null;
        }

        if (array_key_exists('booleanValue', $value)) {
            return (bool)$value['booleanValue'];
        }

        if (array_key_exists('integerValue', $value)) {
            return (int)$value['integerValue'];
        }

        if (array_key_exists('doubleValue', $value)) {
            return (float)$value['doubleValue'];
        }

        if (array_key_exists('stringValue', $value)) {
            return (string)$value['stringValue'];
        }

        if (array_key_exists('timestampValue', $value)) {
            return (string)$value['timestampValue'];
        }

        if (array_key_exists('arrayValue', $value)) {
            $items = [];
            foreach (($value['arrayValue']['values'] ?? []) as $item) {
                $items[] = $this->decodeValue((array)$item);
            }

            return $items;
        }

        if (array_key_exists('mapValue', $value)) {
            return $this->decodeFields((array)($value['mapValue']['fields'] ?? []));
        }

        return null;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function isList(array $value): bool
    {
        if ($value === []) {
            return true;
        }

        return array_keys($value) === range(0, count($value) - 1);
    }
}
