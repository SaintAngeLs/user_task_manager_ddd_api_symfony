<?php

declare(strict_types=1);

namespace App\Application\User\UseCase;

use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserId;
use RuntimeException;

final class PromoteUserToAdminUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
    ) {
    }

    public function execute(string $userId): object
    {
        $user = $this->repository->findById(UserId::fromString($userId));

        if ($user === null) {
            throw new RuntimeException(sprintf('User with id "%s" not found.', $userId));
        }

        $user->promoteToAdmin();
        $this->repository->save($user);

        return $user;
    }
}