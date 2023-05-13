<?php

declare(strict_types=1);

namespace App\Infrastructure\Mongo\Mapper\Money;

use App\Domain\Money\Model\Money;
use App\Infrastructure\Mongo\Mapper\CollectionMapperTrait;
use MongoDB\Model\BSONDocument;

class MoneyMapper
{
    use CollectionMapperTrait;

    public function toBson(?Money $object): ?BSONDocument
    {
        if (!$object) {
            return null;
        }

        return new BSONDocument(['value' => $object->value]);
    }

    public function fromBson(?BSONDocument $bson): ?Money
    {
        if (!$bson) {
            return null;
        }

        $value = (float) ($bson['value'] ?? 0);

        return new Money($value);
    }
}
