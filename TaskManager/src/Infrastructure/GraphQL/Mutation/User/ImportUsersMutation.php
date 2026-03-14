<?php

namespace App\Infrastructure\GraphQL\Mutation\User;

use App\Application\User\UseCase\ImportUsersFromJSONPlaceholderUseCase;

final class ImportUsersMutation
{
    public function __construct(
        private readonly ImportUsersFromJSONPlaceholderUseCase $useCase
    ) {
    }

    public function __invoke(): array
    {
        return $this->useCase->execute();
    }
}