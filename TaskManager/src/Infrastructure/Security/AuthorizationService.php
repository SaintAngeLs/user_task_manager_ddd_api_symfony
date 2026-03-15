<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserId;
use Symfony\Component\HttpFoundation\RequestStack;

final class AuthorizationService
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly TokenService $tokenService,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function getCurrentUser(): ?User
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return null;
        }

        $authorization = $request->headers->get('Authorization');

        if (!$authorization || !str_starts_with($authorization, 'Bearer ')) {
            return null;
        }

        $token = substr($authorization, 7);
        $userId = $this->tokenService->extractUserId($token);

        if (null === $userId) {
            return null;
        }

        return $this->userRepository->findById(UserId::fromString($userId));
    }

    public function requireCurrentUser(): User
    {
        $user = $this->getCurrentUser();

        if (null === $user) {
            throw new \RuntimeException('Unauthorized.');
        }

        return $user;
    }

    public function requireAdmin(): User
    {
        $user = $this->requireCurrentUser();

        if (!$user->isAdmin()) {
            throw new \RuntimeException('Forbidden. Admin access required.');
        }

        return $user;
    }

    public function canAccessUserTasks(string $userId): bool
    {
        $currentUser = $this->getCurrentUser();

        if (null === $currentUser) {
            return false;
        }

        return $currentUser->isAdmin() || $currentUser->getId()->getValue() === $userId;
    }

    public function canManageTaskAssignedTo(string $assignedUserId): bool
    {
        $currentUser = $this->getCurrentUser();

        if (null === $currentUser) {
            return false;
        }

        return $currentUser->isAdmin() || $currentUser->getId()->getValue() === $assignedUserId;
    }
}
