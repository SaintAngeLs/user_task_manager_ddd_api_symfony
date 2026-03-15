<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Query\User;

use App\Infrastructure\Security\AuthorizationService;

final class MeQuery
{
    public function __construct(
        private readonly AuthorizationService $authorizationService,
    ) {
    }

    public function __invoke(): ?object
    {
        return $this->authorizationService->getCurrentUser();
    }
}
