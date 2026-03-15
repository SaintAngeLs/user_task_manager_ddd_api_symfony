<?php

declare(strict_types=1);

namespace App\Application\User\UseCase;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;

final class GetImportedUsersUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
    ) {
    }

    /**
     * @return User[]
     */
    public function execute(): array
    {
        return $this->repository->findAll();
    }
}