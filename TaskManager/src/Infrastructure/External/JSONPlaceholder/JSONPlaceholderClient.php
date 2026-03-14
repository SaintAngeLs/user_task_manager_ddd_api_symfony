<?php

declare(strict_types=1);

namespace App\Infrastructure\External\JSONPlaceholder;

use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class JSONPlaceholderClient
{
    private const BASE_URL = 'https://jsonplaceholder.typicode.com';

    public function __construct(private readonly HttpClientInterface $httpClient) {}

    /**
     * Fetches all users from JSONPlaceholder.
     *
     * @return array<int, array<string, mixed>>
     */
    public function fetchUsers(): array
    {
        $response = $this->httpClient->request('GET', self::BASE_URL . '/users');

        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException(
                sprintf('JSONPlaceholder returned unexpected status %d.', $response->getStatusCode())
            );
        }

        return $response->toArray();
    }
}