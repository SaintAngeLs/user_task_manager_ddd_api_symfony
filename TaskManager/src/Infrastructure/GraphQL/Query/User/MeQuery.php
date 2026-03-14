<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Query\User;

use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\Security\TokenService;
use Symfony\Component\HttpFoundation\RequestStack;

final class MeQuery
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RequestStack $requestStack,
        private readonly TokenService $tokenService,
    ) {
    }

    public function __invoke(): ?object
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            return null;
        }

        $authorization = $request->headers->get('Authorization');

        if (!$authorization || !str_starts_with($authorization, 'Bearer ')) {
            return null;
        }

        $token = substr($authorization, 7);
        $userId = $this->tokenService->extractUserId($token);

        if ($userId === null) {
            return null;
        }

        return $this->userRepository->findById(UserId::fromString($userId));
    }
}