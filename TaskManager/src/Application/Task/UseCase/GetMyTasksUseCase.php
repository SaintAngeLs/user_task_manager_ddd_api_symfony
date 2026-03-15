<?php

declare(strict_types=1);

namespace App\Application\Task\UseCase;

use App\Domain\Task\Repository\TaskRepositoryInterface;
use App\Domain\Task\Entity\Task;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\Security\AuthorizationService;

final class GetMyTasksUseCase
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
        $user = $this->authorizationService->requireCurrentUser();

        return $this->taskRepository->findByUser(
            UserId::fromString($user->getId()->getValue())
        );
    }
}