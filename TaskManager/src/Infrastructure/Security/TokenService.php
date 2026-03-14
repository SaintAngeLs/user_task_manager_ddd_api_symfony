<?php

declare(strict_types=1);

namespace App\Infrastructure\Security;

final class TokenService
{
    public function __construct(
        private readonly string $appSecret,
    ) {
    }

    public function createToken(string $userId): string
    {
        $payload = base64_encode(json_encode([
            'userId' => $userId,
            'iat' => time(),
        ], JSON_THROW_ON_ERROR));

        $signature = hash_hmac('sha256', $payload, $this->appSecret);

        return $payload.'.'.$signature;
    }

    public function extractUserId(string $token): ?string
    {
        $parts = explode('.', $token, 2);

        if (count($parts) !== 2) {
            return null;
        }

        [$payload, $signature] = $parts;

        $expectedSignature = hash_hmac('sha256', $payload, $this->appSecret);

        if (!hash_equals($expectedSignature, $signature)) {
            return null;
        }

        $decoded = json_decode(base64_decode($payload, true) ?: '', true);

        if (!is_array($decoded) || !isset($decoded['userId'])) {
            return null;
        }

        return (string) $decoded['userId'];
    }
}