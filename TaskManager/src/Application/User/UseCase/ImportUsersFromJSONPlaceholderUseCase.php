<?php

declare(strict_types=1);

namespace App\Application\User\UseCase;

use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\External\JSONPlaceholder\JSONPlaceholderClient;

final class ImportUsersFromJSONPlaceholderUseCase
{
    public function __construct(
        private readonly JSONPlaceholderClient $client,
        private readonly UserRepositoryInterface $repository,
    ) {}

    public function execute(): array
    {
        $rawUsers = $this->client->fetchUsers();
        $importedUsers = [];

        foreach ($rawUsers as $data) {
            $existing = $this->repository->findById(UserId::fromInt($data['id']));
            if ($existing !== null) {
                continue;
            }

            $user = new User(
                id: UserId::fromInt($data['id']),
                name: $data['name'],
                username: $data['username'],
                email: $data['email'],
            );

            $this->repository->save($user);
            $importedUsers[] = $user;
        }

        return [
            'success' => true,
            'importedCount' => count($importedUsers),
            'users' => $importedUsers,
        ];
    }
}