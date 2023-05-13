<?php

declare(strict_types=1);

namespace App\Infrastructure\Mongo\Repository;

use App\Domain\ParticipantGroup\Exception\ParticipantGroupNotFoundException;
use App\Domain\ParticipantGroup\Model\ParticipantGroup;
use App\Domain\ParticipantGroup\Model\ParticipantGroupId;
use App\Domain\ParticipantGroup\Model\ParticipantId;
use App\Domain\ParticipantGroup\Service\ParticipantGroupRepositoryInterface;
use App\Infrastructure\Mongo\Mapper\ParticipantGroup\ParticipantGroupMapper;
use App\Infrastructure\Mongo\MongoConnection;
use App\Infrastructure\Uuid\UuidServiceInterface;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;

class MongoParticipantGroupRepository implements ParticipantGroupRepositoryInterface
{
    public function __construct(
        readonly private UuidServiceInterface $uuidService,
        readonly private MongoConnection $connection,
        readonly private ParticipantGroupMapper $mapper
    ) {
    }

    private function getCollection(): Collection
    {
        return $this->connection->getClient()
            ->selectDatabase('bills_db')
            ->selectCollection('participant_groups');
    }

    public function nextId(): ParticipantGroupId
    {
        return new ParticipantGroupId($this->uuidService->generate());
    }

    public function nextParticipantId(): ParticipantId
    {
        return new ParticipantId($this->uuidService->generate());
    }

    public function save(ParticipantGroup $group): void
    {
        $bson = $this->mapper->toBson($group);

        $this->getCollection()->updateOne(
            ['_id' => $group->getId()->id],
            ['$set' => $bson],
            ['upsert' => true]
        );
    }

    public function findById(ParticipantGroupId $id): ?ParticipantGroup
    {
        /** @var BSONDocument $bson */
        $bson = $this->getCollection()->findOne(['_id' => $id->id]);

        return $this->mapper->fromBson($bson);
    }

    public function getById(ParticipantGroupId $id): ParticipantGroup
    {
        $result = $this->findById($id);
        if (!$result) {
            throw ParticipantGroupNotFoundException::withId($id);
        }

        return $result;
    }
}
