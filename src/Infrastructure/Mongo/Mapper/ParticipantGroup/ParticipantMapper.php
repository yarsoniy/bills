<?php

declare(strict_types=1);

namespace App\Infrastructure\Mongo\Mapper\ParticipantGroup;

use App\Domain\ParticipantGroup\Model\Participant;
use App\Domain\ParticipantGroup\Model\ParticipantId;
use App\Domain\ParticipantGroup\View\ParticipantView;
use App\Infrastructure\Mongo\Mapper\CollectionMapperTrait;
use App\Infrastructure\Mongo\Mapper\DateTime\DateTimeMapper;
use MongoDB\Model\BSONDocument;

class ParticipantMapper
{
    use CollectionMapperTrait;

    public function __construct(
        private DateTimeMapper $dateTimeMapper
    ) {
    }

    public function toBson(?ParticipantView $object): ?BSONDocument
    {
        if (!$object) {
            return null;
        }

        return new BSONDocument([
            '_id' => $object->getId()->id,
            'name' => $object->getName(),
            'createdAt' => $this->dateTimeMapper->toBson($object->getCreatedAt()),
        ]);
    }

    public function fromBson(?BSONDocument $bson): ?Participant
    {
        if (!$bson) {
            return null;
        }

        return new Participant(
            new ParticipantId($bson['_id']),
            $bson['name'] ?? null,
            $this->dateTimeMapper->fromBson($bson['createdAt'] ?? null)
        );
    }
}
