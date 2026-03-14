<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Query\Task;

use App\Domain\Task\Repository\TaskRepositoryInterface;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\Security\AuthorizationService;

final class MyTasksQuery
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly AuthorizationService $authorizationService,
    ) {
    }

    public function __invoke(): array
    {
        $user = $this->authorizationService->requireCurrentUser();

        return $this->taskRepository->findByUser(
            UserId::fromString($user->getId()->getValue())
        );
    }
}