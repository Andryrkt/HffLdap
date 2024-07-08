<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Psr\Log\LoggerInterface;

class CustomLdapUserProvider implements UserProviderInterface
{
    private $ldap;
    private $baseDn;
    private $searchDn;
    private $searchPassword;
    private $defaultRoles;
    private $uidKey;
    private $logger;

    public function __construct(
        LdapInterface $ldap,
        string $baseDn,
        string $searchDn,
        string $searchPassword,
        array $defaultRoles = ['ROLE_USER'],
        string $uidKey = 'samaccountname',
        LoggerInterface $logger
    ) {
        $this->ldap = $ldap;
        $this->baseDn = $baseDn;
        $this->searchDn = $searchDn;
        $this->searchPassword = $searchPassword;
        $this->defaultRoles = $defaultRoles;
        $this->uidKey = strtolower($uidKey);
        $this->logger = $logger;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->loadUserByUsername($identifier);
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        // Bind to LDAP with search DN and password
        try {
            $this->ldap->bind($this->searchDn, $this->searchPassword);
            $this->logger->info('LDAP bind successful.');
        } catch (\Exception $e) {
            $this->logger->error('LDAP bind failed: ' . $e->getMessage());
            throw new AuthenticationException('Could not bind to LDAP: ' . $e->getMessage());
        }

        // Perform the search to get the DN
        $query = $this->ldap->query($this->baseDn, "({$this->uidKey}=$username)");
        $result = $query->execute();
        $entries = $result->toArray();

        if (count($entries) === 0) {
            $this->logger->error(sprintf('User "%s" not found.', $username));
            throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
        }

        $entry = $entries[0]->getAttributes();
        $entry = array_change_key_case($entry, CASE_LOWER);

        if (!isset($entry[$this->uidKey])) {
            $this->logger->error(sprintf('Attribute "%s" not found for user "%s".', $this->uidKey, $username));
            throw new AuthenticationException(sprintf('Attribute "%s" not found for user "%s".', $this->uidKey, $username));
        }

        $dn = $entries[0]->getDn();
        $this->logger->info(sprintf('User DN: "%s".', $dn));

        return new User(
            $entry[$this->uidKey][0],
            null, // Password is not stored
            $this->defaultRoles,
            $dn
        );
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}
