<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

final class UserId
{
    private string $value;

    private function __construct(string $value)
    {
        if (empty($value)) {
            throw new InvalidArgumentException('UserId cannot be empty.');
        }
        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function fromInt(int $value): self
    {
        return new self((string) $value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}