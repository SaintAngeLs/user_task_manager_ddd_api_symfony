<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Task;

use App\Application\Task\Factory\TaskFactory;
use App\Domain\Task\Entity\Task as DomainTask;
use App\Domain\Task\ValueObject\TaskStatus;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Doctrine ORM mapping for the Task aggregate.
 */
#[ORM\Entity]
#[ORM\Table(name: 'tasks')]
class TaskDoctrineEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 255)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'string', length: 50)]
    private string $status;

    #[ORM\Column(name: 'assigned_user_id', type: 'string', length: 255)]
    private string $assignedUserId;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $title,
        string $description,
        string $status,
        string $assignedUserId,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
        $this->assignedUserId = $assignedUserId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function fromDomain(DomainTask $task): self
    {
        return new self(
            id: $task->getId()->getValue(),
            title: $task->getTitle(),
            description: $task->getDescription(),
            status: $task->getStatus()->getValue(),
            assignedUserId: $task->getAssignedUserId()->getValue(),
            createdAt: $task->getCreatedAt(),
            updatedAt: $task->getUpdatedAt(),
        );
    }

    public function toDomain(TaskFactory $factory): DomainTask
    {
        return $factory->reconstitute(
            id: $this->id,
            title: $this->title,
            description: $this->description,
            status: $this->status,
            assignedUserId: $this->assignedUserId,
            createdAt: $this->createdAt,
        );
    }

    public function update(DomainTask $task): void
    {
        $this->title = $task->getTitle();
        $this->description = $task->getDescription();
        $this->status = $task->getStatus()->getValue();
        $this->assignedUserId = $task->getAssignedUserId()->getValue();
        $this->updatedAt = $task->getUpdatedAt();
    }

    public function getId(): string
    {
        return $this->id;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getAssignedUserId(): string
    {
        return $this->assignedUserId;
    }
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}