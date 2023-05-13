<?php

declare(strict_types=1);

namespace App\Infrastructure\Mongo;

use MongoDB\Client;
use MongoDB\Database;
use MongoDB\Driver\ServerApi;

class MongoConnection
{
    private string $url;

    private ?Client $client = null;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getClient(): Client
    {
        if (!$this->client) {
            $apiVersion = new ServerApi((string) ServerApi::V1);
            $this->client = new Client(
                $this->url,
                [],
                ['serverApi' => $apiVersion]
            );
        }

        return $this->client;
    }

    public function selectDatabase(string $db): Database
    {
        return $this->selectDatabase($db);
    }
}
