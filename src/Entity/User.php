<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private $username;
    private $password;
    private $roles;
    private $dn; // Distinguished Name

    public function __construct(string $username, ?string $password, array $roles, string $dn)
    {
        $this->username = $username;
        $this->password = $password;
        $this->roles = $roles;
        $this->dn = $dn;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getSalt(): ?string
    {
        // Not needed for LDAP
        return null;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getDn(): string
    {
        return $this->dn;
    }
}

