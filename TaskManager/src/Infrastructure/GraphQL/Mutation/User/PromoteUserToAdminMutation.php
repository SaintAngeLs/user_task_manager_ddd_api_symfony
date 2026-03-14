<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Mutation\User;

use App\Application\User\UseCase\PromoteUserToAdminUseCase;
use Overblog\GraphQLBundle\Definition\Argument;

final class PromoteUserToAdminMutation
{
    public function __construct(
        private readonly PromoteUserToAdminUseCase $useCase,
    ) {
    }

    public function __invoke(Argument $args): object
    {
        return $this->useCase->execute($args['userId']);
    }
}