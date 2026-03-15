<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Query\Task;

use App\Application\Task\UseCase\GetMyTasksUseCase;

final class MyTasksQuery
{
    public function __construct(
        private readonly GetMyTasksUseCase $useCase,
    ) {
    }

    /**
     * @return list<\App\Domain\Task\Entity\Task>
     */
    public function __invoke(): array
    {
        return $this->useCase->execute();
    }
}
