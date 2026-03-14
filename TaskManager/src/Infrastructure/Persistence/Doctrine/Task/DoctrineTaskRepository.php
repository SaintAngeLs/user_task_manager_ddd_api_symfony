<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Task;

use App\Application\Task\Factory\TaskFactory;
use App\Domain\Task\Entity\Task;
use App\Domain\Task\Repository\TaskRepositoryInterface;
use App\Domain\Task\ValueObject\TaskId;
use App\Domain\User\ValueObject\UserId;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrineTaskRepository implements TaskRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly TaskFactory $factory,
    ) {
    }

    public function save(Task $task): void
    {
        $existing = $this->em->find(TaskDoctrineEntity::class, $task->getId()->getValue());

        if ($existing instanceof TaskDoctrineEntity) {
            $existing->update($task);
        } else {
            $this->em->persist(TaskDoctrineEntity::fromDomain($task));
        }

        $this->em->flush();
    }

    public function findById(TaskId $id): ?Task
    {
        $entity = $this->em->find(TaskDoctrineEntity::class, $id->getValue());

        return $entity?->toDomain($this->factory);
    }

    public function findAll(): array
    {
        return array_map(
            fn(TaskDoctrineEntity $e) => $e->toDomain($this->factory),
            $this->em->getRepository(TaskDoctrineEntity::class)->findAll()
        );
    }

    public function findByUser(UserId $userId): array
    {
        $entities = $this->em
            ->getRepository(TaskDoctrineEntity::class)
            ->findBy(['assignedUserId' => $userId->getValue()]);

        return array_map(
            fn(TaskDoctrineEntity $e) => $e->toDomain($this->factory),
            $entities
        );
    }
}