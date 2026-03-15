<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Query\User;

use App\Application\User\UseCase\GetImportedUsersUseCase;

final class ImportedUsersQuery
{
    public function __construct(
        private readonly GetImportedUsersUseCase $useCase,
    ) {
    }

    public function __invoke(): array
    {
        return $this->useCase->execute();
    }
}