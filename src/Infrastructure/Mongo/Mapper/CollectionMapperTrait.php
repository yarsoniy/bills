<?php

declare(strict_types=1);

namespace App\Infrastructure\Mongo\Mapper;

use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;

trait CollectionMapperTrait
{
    public function arrayToBson(array $items): BSONArray
    {
        $data = array_map(fn ($item) => $this->toBson($item), $items);

        return new BSONArray($data);
    }

    public function arrayFromBson(BSONArray|array $items): array
    {
        $result = [];
        foreach ($items as $key => $item) {
            $result[$key] = $this->fromBson($item);
        }

        return $result;
    }

    public function assocToBson(array $items): BSONDocument
    {
        $data = array_map(fn ($item) => $this->toBson($item), $items);

        return new BSONDocument($data);
    }

    public function assocFromBson(BSONDocument $items): array
    {
        $result = [];
        foreach ($items as $key => $item) {
            $result[$key] = $this->fromBson($item);
        }

        return $result;
    }
}
