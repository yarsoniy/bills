<?php

declare(strict_types=1);

namespace App\Infrastructure\Mongo\Mapper\Bill;

use App\Domain\Bill\Model\BillItem;
use App\Domain\Bill\Model\BillItemId;
use App\Domain\Bill\View\BillItemView;
use App\Infrastructure\Mongo\Mapper\CollectionMapperTrait;
use App\Infrastructure\Mongo\Mapper\DateTime\DateTimeMapper;
use App\Infrastructure\Mongo\Mapper\Money\MoneyMapper;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;

class BillItemMapper
{
    use CollectionMapperTrait;

    public function __construct(
        readonly private DateTimeMapper $dateTimeMapper,
        readonly private MoneyMapper $moneyMapper,
        readonly private PaymentMapper $paymentMapper
    ) {
    }

    public function toBson(?BillItemView $object): ?BSONDocument
    {
        if (!$object) {
            return null;
        }

        return new BSONDocument([
            '_id' => $object->getId()->id,
            'createdAt' => $this->dateTimeMapper->toBson($object->getCreatedAt()),
            'title' => $object->getTitle(),
            'cost' => $this->moneyMapper->toBson($object->getCost()),
            'payments' => $this->paymentMapper->arrayToBson($object->getPayments()),
        ]);
    }

    public function fromBson(?BSONDocument $bson): ?BillItem
    {
        if (!$bson) {
            return null;
        }

        return new BillItem(
            new BillItemId($bson['_id']),
            $this->dateTimeMapper->fromBson($bson['createdAt'] ?? null),
            $bson['title'] ?? null,
            $this->moneyMapper->fromBson($bson['cost'] ?? null),
            $this->paymentMapper->arrayFromBson($bson['payments'] ?? new BSONArray())
        );
    }
}
