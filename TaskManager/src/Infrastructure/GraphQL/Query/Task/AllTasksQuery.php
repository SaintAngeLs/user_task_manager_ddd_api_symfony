<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Query\Task;

use App\Domain\Task\Repository\TaskRepositoryInterface;
use App\Infrastructure\Security\AuthorizationService;

final class AllTasksQuery
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly AuthorizationService $authorizationService,
    ) {
    }

    public function __invoke(): array
    {
        $this->authorizationService->requireAdmin();

        return $this->taskRepository->findAll();
    }
}