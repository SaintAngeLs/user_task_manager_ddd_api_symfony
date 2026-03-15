<?php

declare(strict_types=1);

namespace App\Application\Task\UseCase;

use App\Domain\Task\Entity\Task;
use App\Domain\Task\Repository\TaskRepositoryInterface;
use App\Infrastructure\Security\AuthorizationService;

final class GetAllTasksUseCase
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly AuthorizationService $authorizationService,
    ) {
    }

    /**
     * @return Task[]
     */
    public function execute(): array
    {
        $this->authorizationService->requireAdmin();

        return $this->taskRepository->findAll();
    }
}
