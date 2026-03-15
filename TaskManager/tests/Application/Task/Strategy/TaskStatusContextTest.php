<?php

declare(strict_types=1);

namespace App\Tests\Application\Task\Strategy;

use App\Application\Task\Factory\TaskFactory;
use App\Application\Task\Strategy\MoveToDoneStrategy;
use App\Application\Task\Strategy\MoveToInProgressStrategy;
use App\Application\Task\Strategy\MoveToTodoStrategy;
use App\Application\Task\Strategy\TaskStatusContext;
use App\Domain\Task\ValueObject\TaskStatus;
use PHPUnit\Framework\TestCase;

final class TaskStatusContextTest extends TestCase
{
    public function testTransitionFromTodoToInProgress(): void
    {
        $factory = new TaskFactory();
        $task = $factory->create('Task', 'Desc', '1');

        $context = new TaskStatusContext([
            new MoveToTodoStrategy(),
            new MoveToInProgressStrategy(),
            new MoveToDoneStrategy(),
        ]);

        $context->transition($task, TaskStatus::inProgress());

        self::assertSame(TaskStatus::IN_PROGRESS, $task->getStatus()->getValue());
    }

    public function testTransitionFromInProgressToDone(): void
    {
        $factory = new TaskFactory();
        $task = $factory->create('Task', 'Desc', '1', TaskStatus::IN_PROGRESS);

        $context = new TaskStatusContext([
            new MoveToTodoStrategy(),
            new MoveToInProgressStrategy(),
            new MoveToDoneStrategy(),
        ]);

        $context->transition($task, TaskStatus::done());

        self::assertSame(TaskStatus::DONE, $task->getStatus()->getValue());
    }

    public function testTransitionFromDoneToTodo(): void
    {
        $factory = new TaskFactory();
        $task = $factory->create('Task', 'Desc', '1', TaskStatus::DONE);

        $context = new TaskStatusContext([
            new MoveToTodoStrategy(),
            new MoveToInProgressStrategy(),
            new MoveToDoneStrategy(),
        ]);

        $context->transition($task, TaskStatus::todo());

        self::assertSame(TaskStatus::TODO, $task->getStatus()->getValue());
    }
}
