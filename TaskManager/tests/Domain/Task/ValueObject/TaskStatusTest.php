<?php

declare(strict_types=1);

namespace App\Tests\Domain\Task\ValueObject;

use App\Domain\Task\ValueObject\TaskStatus;
use PHPUnit\Framework\TestCase;

final class TaskStatusTest extends TestCase
{
    public function testCreatesValidStatuses(): void
    {
        self::assertSame('todo', TaskStatus::todo()->getValue());
        self::assertSame('in_progress', TaskStatus::inProgress()->getValue());
        self::assertSame('done', TaskStatus::done()->getValue());
    }

    public function testThrowsExceptionForInvalidStatus(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        TaskStatus::fromString('blocked');
    }
}
