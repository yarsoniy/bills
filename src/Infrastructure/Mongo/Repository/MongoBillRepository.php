<?php

declare(strict_types=1);

namespace App\Infrastructure\Mongo\Repository;

use App\Domain\Bill\Exception\BillNotFoundException;
use App\Domain\Bill\Model\Bill;
use App\Domain\Bill\Model\BillId;
use App\Domain\Bill\Model\BillItemId;
use App\Domain\Bill\Service\BillRepositoryInterface;
use App\Domain\ParticipantGroup\Model\ParticipantGroupId;
use App\Infrastructure\Mongo\Mapper\Bill\BillMapper;
use App\Infrastructure\Mongo\MongoConnection;
use App\Infrastructure\Uuid\UuidServiceInterface;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;

class MongoBillRepository implements BillRepositoryInterface
{
    public function __construct(
        readonly private UuidServiceInterface $uuidService,
        readonly private MongoConnection $connection,
        readonly private BillMapper $mapper
    ) {
    }

    private function getCollection(): Collection
    {
        return $this->connection->getClient()
            ->selectDatabase('bills_db')
            ->selectCollection('bills');
    }

    public function nextId(): BillId
    {
        return new BillId($this->uuidService->generate());
    }

    public function nextItemId(): BillItemId
    {
        return new BillItemId($this->uuidService->generate());
    }

    public function save(Bill $bill): void
    {
        $bson = $this->mapper->toBson($bill);
        $this->getCollection()->updateOne(
            ['_id' => $bill->getId()->id],
            ['$set' => $bson],
            ['upsert' => true]
        );
    }

    public function findById(BillId $id): ?Bill
    {
        /** @var BSONDocument $bson */
        $bson = $this->getCollection()->findOne(['_id' => $id->id]);

        return $this->mapper->fromBson($bson);
    }

    public function getById(BillId $id): Bill
    {
        $result = $this->findById($id);
        if (!$result) {
            throw BillNotFoundException::withId($id);
        }

        return $result;
    }

    public function findByParticipantGroup(ParticipantGroupId $groupId): array
    {
        $bsonBills = $this->getCollection()->find(
            ['groupId' => $groupId->id],
            ['sort' => ['createdAt' => -1]]
        )->toArray();

        return $this->mapper->arrayFromBson($bsonBills);
    }

    public function getByItemId(BillItemId $itemId): Bill
    {
        // TODO: Implement getByItemId() method.
    }
}
