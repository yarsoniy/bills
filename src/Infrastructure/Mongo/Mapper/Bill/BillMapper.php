<?php

declare(strict_types=1);

namespace App\Infrastructure\Mongo\Mapper\Bill;

use App\Domain\Bill\Model\Bill;
use App\Domain\Bill\Model\BillId;
use App\Domain\ParticipantGroup\Model\ParticipantGroupId;
use App\Infrastructure\Mongo\Mapper\CollectionMapperTrait;
use App\Infrastructure\Mongo\Mapper\DateTime\DateTimeMapper;
use App\Infrastructure\Mongo\Mapper\Money\MoneyBreakdownMapper;
use MongoDB\Model\BSONDocument;

class BillMapper
{
    use CollectionMapperTrait;

    public function __construct(
        readonly private DateTimeMapper $dateTimeMapper,
        readonly private BillItemMapper $billItemMapper,
        readonly private MoneyBreakdownMapper $moneyBreakdownMapper
    ) {
    }

    public function toBson(?Bill $object): ?BSONDocument
    {
        if (!$object) {
            return null;
        }

        return new BSONDocument([
            '_id' => $object->getId()->id,
            'groupId' => $object->getGroupId()->id,
            'createdAt' => $this->dateTimeMapper->toBson($object->getCreatedAt()),
            'title' => $object->getTitle(),
            'items' => $this->billItemMapper->assocToBson($object->getItemsView()),
            'participantDeposits' => $this->moneyBreakdownMapper->toBson($object->getParticipantDeposits()),
        ]);
    }

    public function fromBson(?BSONDocument $bson): ?Bill
    {
        if (!$bson) {
            return null;
        }

        return new Bill(
            new BillId($bson['_id']),
            new ParticipantGroupId($bson['groupId']),
            $this->dateTimeMapper->fromBson($bson['createdAt'] ?? null),
            $bson['title'] ?? null,
            $this->billItemMapper->assocFromBson($bson['items'] ?? new BSONDocument()),
            $this->moneyBreakdownMapper->fromBson($bson['participantDeposits'] ?? null)
        );
    }
}
