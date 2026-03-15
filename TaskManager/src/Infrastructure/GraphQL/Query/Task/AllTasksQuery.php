<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Query\Task;

use App\Application\Task\UseCase\GetAllTasksUseCase;

final class AllTasksQuery
{
    public function __construct(
        private readonly GetAllTasksUseCase $useCase,
    ) {
    }

    public function __invoke(): array
    {
        return $this->useCase->execute();
    }
}