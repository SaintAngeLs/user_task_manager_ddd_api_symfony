<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\User\ValueObject\UserId;

class User
{
    private UserId $id;
    private string $name;
    private string $username;
    private string $email;
    private bool $isAdmin;

    public function __construct(
        UserId $id,
        string $name,
        string $username,
        string $email,
        bool $isAdmin = false,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->username = $username;
        $this->email = $email;
        $this->isAdmin = $isAdmin;
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function promoteToAdmin(): void
    {
        $this->isAdmin = true;
    }
}