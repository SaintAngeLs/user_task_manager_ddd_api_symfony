<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Mutation\User;

use App\Application\User\UseCase\LoginUserUseCase;
use App\Infrastructure\Security\TokenService;
use Overblog\GraphQLBundle\Definition\Argument;

final class LoginUserMutation
{
    public function __construct(
        private readonly LoginUserUseCase $useCase,
        private readonly TokenService $tokenService,
    ) {
    }

    /**
     * @return array{
     *     token: string,
     *     user: \App\Domain\User\Entity\User
     * }
     */
    public function __invoke(Argument $args): array
    {
        $user = $this->useCase->execute($args['username']);
        $token = $this->tokenService->createToken($user->getId()->getValue());

        return [
            'token' => $token,
            'user' => $user,
        ];
    }
}
