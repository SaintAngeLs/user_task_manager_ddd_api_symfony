<?php

namespace App\Infrastructure\GraphQL\Query\User;

use App\Domain\User\Repository\UserRepositoryInterface;


final class ImportedUsersQuery
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function __invoke(): array
    {
        return $this->userRepository->findAll();
    }
}