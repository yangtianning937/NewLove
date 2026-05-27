<?php
declare(strict_types=1);

/**
 * Import Firestore-friendly JSON files into Google Cloud Firestore.
 *
 * Required environment variables:
 * - FIREBASE_PROJECT_ID
 * - FIREBASE_ACCESS_TOKEN
 *
 * Optional environment variables:
 * - IMPORT_DIR, default firebase/firestore-data
 * - FIRESTORE_DATABASE, default (default)
 */

$projectId = getenv('FIREBASE_PROJECT_ID') ?: '';
$accessToken = getenv('FIREBASE_ACCESS_TOKEN') ?: '';
$importDir = getenv('IMPORT_DIR') ?: 'firebase/firestore-data';
$database = getenv('FIRESTORE_DATABASE') ?: '(default)';

if ($projectId === '' || $accessToken === '') {
    fwrite(STDERR, "FIREBASE_PROJECT_ID and FIREBASE_ACCESS_TOKEN are required.\n");
    exit(1);
}

$manifestPath = $importDir . DIRECTORY_SEPARATOR . 'manifest.json';

if (!is_file($manifestPath)) {
    fwrite(STDERR, "Manifest file not found: {$manifestPath}\n");
    exit(1);
}

$manifest = json_decode((string)file_get_contents($manifestPath), true);

if (!is_array($manifest) || !isset($manifest['collections']) || !is_array($manifest['collections'])) {
    fwrite(STDERR, "Manifest file is not valid.\n");
    exit(1);
}

$baseUrl = sprintf(
    'https://firestore.googleapis.com/v1/projects/%s/databases/%s/documents',
    rawurlencode($projectId),
    rawurlencode($database)
);

$totalDocuments = 0;

foreach ($manifest['collections'] as $collection) {
    if (!isset($collection['name'], $collection['file'])) {
        continue;
    }

    $collectionName = (string)$collection['name'];
    $filePath = $importDir . DIRECTORY_SEPARATOR . (string)$collection['file'];

    if (!is_file($filePath)) {
        fwrite(STDERR, "Skipping missing file: {$filePath}\n");
        continue;
    }

    $documents = json_decode((string)file_get_contents($filePath), true);

    if (!is_array($documents)) {
        fwrite(STDERR, "Skipping invalid JSON file: {$filePath}\n");
        continue;
    }

    $imported = 0;

    foreach ($documents as $document) {
        if (!isset($document['document_id'], $document['data']) || !is_array($document['data'])) {
            continue;
        }

        $documentId = sanitizeDocumentId((string)$document['document_id']);
        $url = $baseUrl . '/' . rawurlencode($collectionName) . '/' . rawurlencode($documentId);
        $payload = json_encode(['fields' => encodeMapFields($document['data'])], JSON_UNESCAPED_SLASHES);

        requestFirestore($url, $accessToken, $payload);
        $imported++;
    }

    $totalDocuments += $imported;
    echo "Imported {$imported} documents into {$collectionName}\n";
}

echo "Imported {$totalDocuments} documents into project {$projectId}\n";

function sanitizeDocumentId(string $documentId): string
{
    $documentId = trim($documentId);
    $documentId = str_replace('/', '_', $documentId);

    if ($documentId === '' || $documentId === '.' || $documentId === '..') {
        return 'document_' . bin2hex(random_bytes(4));
    }

    return $documentId;
}

function encodeMapFields(array $data): array
{
    $fields = [];

    foreach ($data as $key => $value) {
        $fields[(string)$key] = encodeValue($value);
    }

    return $fields;
}

function encodeValue(mixed $value): array
{
    if ($value === null) {
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
        if (array_is_list($value)) {
            return [
                'arrayValue' => [
                    'values' => array_map('encodeValue', $value),
                ],
            ];
        }

        return [
            'mapValue' => [
                'fields' => encodeMapFields($value),
            ],
        ];
    }

    return ['stringValue' => (string)$value];
}

function requestFirestore(string $url, string $accessToken, string $payload): void
{
    $handle = curl_init($url);

    curl_setopt_array($handle, [
        CURLOPT_CUSTOMREQUEST => 'PATCH',
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ],
    ]);

    $response = curl_exec($handle);
    $statusCode = (int)curl_getinfo($handle, CURLINFO_HTTP_CODE);

    if ($response === false || $statusCode < 200 || $statusCode >= 300) {
        $error = curl_error($handle);
        curl_close($handle);
        fwrite(STDERR, "Firestore request failed with HTTP {$statusCode}: {$error}\n{$response}\n");
        exit(1);
    }

    curl_close($handle);
}
