<?php

declare(strict_types=1);

namespace App\Application\Task\UseCase;

use App\Application\Task\DTO\CreateTaskInput;
use App\Application\Task\Factory\TaskFactory;
use App\Domain\Task\Entity\Task;
use App\Domain\Task\Repository\TaskRepositoryInterface;
use App\Infrastructure\Security\AuthorizationService;

final class CreateTaskUseCase
{
    public function __construct(
        private readonly TaskFactory $taskFactory,
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly AuthorizationService $authorizationService,
    ) {
    }

    public function execute(CreateTaskInput $input): Task
    {
        $this->authorizationService->requireAdmin();

        $task = $this->taskFactory->create(
            title: $input->title,
            description: $input->description,
            assignedUserId: $input->assignedUserId,
        );

        $this->taskRepository->save($task);

        return $task;
    }
}