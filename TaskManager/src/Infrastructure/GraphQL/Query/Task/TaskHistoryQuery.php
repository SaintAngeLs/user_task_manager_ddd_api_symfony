<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Query\Task;

use App\Application\Task\UseCase\GetTaskHistoryUseCase;
use Overblog\GraphQLBundle\Definition\Argument;

final class TaskHistoryQuery
{
    public function __construct(
        private readonly GetTaskHistoryUseCase $useCase,
    ) {
    }

    public function __invoke(Argument $args): array
    {
        return $this->useCase->execute($args['taskId']);
    }
}