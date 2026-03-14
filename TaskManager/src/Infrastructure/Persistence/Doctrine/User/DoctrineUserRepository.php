<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\User;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserId;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineUserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function save(User $user): void
    {
        $existing = $this->em->find(UserDoctrineEntity::class, $user->getId()->getValue());

        if ($existing instanceof UserDoctrineEntity) {
            $existing->update($user);
        } else {
            $this->em->persist(UserDoctrineEntity::fromDomain($user));
        }

        $this->em->flush();
    }

    public function findById(UserId $id): ?User
    {
        $entity = $this->em->find(UserDoctrineEntity::class, $id->getValue());

        return $entity?->toDomain();
    }

    public function findByUsername(string $username): ?User
    {
        $entity = $this->em
            ->getRepository(UserDoctrineEntity::class)
            ->findOneBy(['username' => $username]);

        return $entity?->toDomain();
    }

    public function findByEmail(string $email): ?User
    {
        $entity = $this->em
            ->getRepository(UserDoctrineEntity::class)
            ->findOneBy(['email' => $email]);

        return $entity?->toDomain();
    }

    public function findAll(): array
    {
        return array_map(
            fn(UserDoctrineEntity $e) => $e->toDomain(),
            $this->em->getRepository(UserDoctrineEntity::class)->findAll()
        );
    }
}