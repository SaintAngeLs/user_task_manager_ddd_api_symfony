<?php

declare(strict_types=1);

namespace App\Tests\Application\Task\Factory;

use App\Application\Task\Factory\TaskFactory;
use App\Domain\Task\Entity\Task;
use App\Domain\Task\ValueObject\TaskStatus;
use PHPUnit\Framework\TestCase;

final class TaskFactoryTest extends TestCase
{
    public function testCreateReturnsTaskInstance(): void
    {
        $factory = new TaskFactory();

        $task = $factory->create(
            title: 'Prepare docs',
            description: 'Write API documentation',
            assignedUserId: '1',
        );

        self::assertInstanceOf(Task::class, $task);
        self::assertSame('Prepare docs', $task->getTitle());
        self::assertSame('Write API documentation', $task->getDescription());
        self::assertSame('1', $task->getAssignedUserId()->getValue());
        self::assertSame(TaskStatus::TODO, $task->getStatus()->getValue());
    }

    public function testCreateTrimsInputValues(): void
    {
        $factory = new TaskFactory();

        $task = $factory->create(
            title: '  Prepare docs  ',
            description: '  Write API documentation  ',
            assignedUserId: '1',
        );

        self::assertSame('Prepare docs', $task->getTitle());
        self::assertSame('Write API documentation', $task->getDescription());
    }

    public function testCreateThrowsExceptionWhenTitleIsEmpty(): void
    {
        $factory = new TaskFactory();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Task title cannot be empty.');

        $factory->create(
            title: '   ',
            description: 'desc',
            assignedUserId: '1',
        );
    }

    public function testCreateThrowsExceptionWhenAssignedUserIdIsEmpty(): void
    {
        $factory = new TaskFactory();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('AssignedUserId cannot be empty.');

        $factory->create(
            title: 'Task',
            description: 'desc',
            assignedUserId: '   ',
        );
    }

    public function testCreateUsesProvidedStatus(): void
    {
        $factory = new TaskFactory();

        $task = $factory->create(
            title: 'Prepare docs',
            description: 'Write API documentation',
            assignedUserId: '1',
            status: TaskStatus::DONE,
        );

        self::assertSame(TaskStatus::DONE, $task->getStatus()->getValue());
    }
}
