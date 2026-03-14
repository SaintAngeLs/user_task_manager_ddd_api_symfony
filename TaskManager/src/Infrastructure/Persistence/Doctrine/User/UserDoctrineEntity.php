<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\User;

use App\Domain\User\Entity\User as DomainUser;
use App\Domain\User\ValueObject\UserId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class UserDoctrineEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 255)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $username;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isAdmin = false;

    public function __construct(
        string $id,
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

    public static function fromDomain(DomainUser $user): self
    {
        return new self(
            id: $user->getId()->getValue(),
            name: $user->getName(),
            username: $user->getUsername(),
            email: $user->getEmail(),
            isAdmin: $user->isAdmin(),
        );
    }

    public function toDomain(): DomainUser
    {
        return new DomainUser(
            id: UserId::fromString($this->id),
            name: $this->name,
            username: $this->username,
            email: $this->email,
            isAdmin: $this->isAdmin,
        );
    }

    public function update(DomainUser $user): void
    {
        $this->name = $user->getName();
        $this->username = $user->getUsername();
        $this->email = $user->getEmail();
        $this->isAdmin = $user->isAdmin();
    }

    public function getId(): string
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
}