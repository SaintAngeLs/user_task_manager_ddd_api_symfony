<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Mutation\Task;

use App\Application\Task\UseCase\UpdateTaskStatusUseCase;
use Overblog\GraphQLBundle\Definition\Argument;

final class UpdateTaskStatusMutation
{
    public function __construct(
        private readonly UpdateTaskStatusUseCase $useCase,
    ) {
    }

    public function __invoke(Argument $args): object
    {
        return $this->useCase->execute(
            taskId: $args['taskId'],
            targetStatus: $args['status'],
        );
    }
}
