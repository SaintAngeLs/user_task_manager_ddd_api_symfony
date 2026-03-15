<?php

declare(strict_types=1);

namespace App\Application\User\UseCase;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;

final class LoginUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
    ) {
    }

    public function execute(string $username): User
    {
        $user = $this->repository->findByUsername($username);

        if (null === $user) {
            throw new \RuntimeException("User '$username' not found.");
        }

        return $user;
    }
}
