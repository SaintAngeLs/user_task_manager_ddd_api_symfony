<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Mutation\User;

use App\Application\User\UseCase\ImportUsersFromJSONPlaceholderUseCase;

final class ImportUsersMutation
{
    public function __construct(
        private readonly ImportUsersFromJSONPlaceholderUseCase $useCase,
    ) {
    }

    /**
     * @return array{
     *     success: bool,
     *     importedCount: int,
     *     users: list<\App\Domain\User\Entity\User>
     * }
     */
    public function __invoke(): array
    {
        return $this->useCase->execute();
    }
}
