<?php
declare(strict_types=1);

namespace App\Identifier;

use App\Service\NewLoveFirestoreRepository;
use Authentication\Identifier\AbstractIdentifier;
use Authentication\Identifier\IdentifierInterface;
use Authentication\PasswordHasher\DefaultPasswordHasher;

class FirestoreUserIdentifier extends AbstractIdentifier
{
    protected $_defaultConfig = [
        'fields' => [
            IdentifierInterface::CREDENTIAL_USERNAME => 'email',
            IdentifierInterface::CREDENTIAL_PASSWORD => 'password',
        ],
    ];

    private NewLoveFirestoreRepository $repository;

    public function __construct(array $config = [], ?NewLoveFirestoreRepository $repository = null)
    {
        parent::__construct($config);

        $this->repository = $repository ?? new NewLoveFirestoreRepository();
    }

    public function identify(array $credentials)
    {
        $usernameField = $this->getConfig('fields.' . IdentifierInterface::CREDENTIAL_USERNAME);
        $passwordField = $this->getConfig('fields.' . IdentifierInterface::CREDENTIAL_PASSWORD);

        $email = trim((string)($credentials[IdentifierInterface::CREDENTIAL_USERNAME] ?? $credentials[$usernameField] ?? ''));
        $password = (string)($credentials[IdentifierInterface::CREDENTIAL_PASSWORD] ?? $credentials[$passwordField] ?? '');

        if ($email === '' || $password === '') {
            $this->_errors[] = 'Email and password are required.';

            return null;
        }

        $user = $this->repository->userByEmail($email);
        if ($user === null || empty($user['password'])) {
            $this->_errors[] = 'User was not found.';

            return null;
        }

        $hasher = new DefaultPasswordHasher();
        if (!$hasher->check($password, (string)$user['password'])) {
            $this->_errors[] = 'Password did not match.';

            return null;
        }

        unset($user['password'], $user['nonce'], $user['nonce_expiry']);

        return $user;
    }
}
