<?php

declare(strict_types=1);

namespace App\Infrastructure\Mongo\Mapper\Money;

use App\Domain\Money\Model\MoneyBreakdown;
use MongoDB\Model\BSONDocument;

class MoneyBreakdownMapper
{
    public function __construct(
        readonly private MoneyMapper $moneyMapper
    ) {
    }

    public function toBson(?MoneyBreakdown $object): ?BSONDocument
    {
        if (!$object) {
            return null;
        }

        return new BSONDocument([
            'items' => $this->moneyMapper->assocToBson($object->getItems()),
        ]);
    }

    public function fromBson(?BSONDocument $bson): ?MoneyBreakdown
    {
        if (!$bson) {
            return null;
        }

        return new MoneyBreakdown(
            $this->moneyMapper->assocFromBson($bson['items'] ?? new BSONDocument())
        );
    }
}
