<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Mutation\Task;

use App\Application\Task\DTO\CreateTaskInput;
use App\Application\Task\UseCase\CreateTaskUseCase;
use Overblog\GraphQLBundle\Definition\Argument;

final class CreateTaskMutation
{
    public function __construct(
        private readonly CreateTaskUseCase $useCase,
    ) {
    }

    public function __invoke(Argument $args): object
    {
        return $this->useCase->execute(new CreateTaskInput(
            title: $args['title'],
            description: $args['description'],
            assignedUserId: $args['assignedUserId'],
        ));
    }
}
