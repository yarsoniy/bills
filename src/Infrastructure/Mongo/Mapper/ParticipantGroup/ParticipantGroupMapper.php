<?php

declare(strict_types=1);

namespace App\Infrastructure\Mongo\Mapper\ParticipantGroup;

use App\Domain\ParticipantGroup\Model\ParticipantGroup;
use App\Domain\ParticipantGroup\Model\ParticipantGroupId;
use App\Infrastructure\Mongo\Mapper\DateTime\DateTimeMapper;
use MongoDB\Model\BSONDocument;

class ParticipantGroupMapper
{
    public function __construct(
        private DateTimeMapper $dateTimeMapper,
        private ParticipantMapper $participantMapper
    ) {
    }

    public function toBson(?ParticipantGroup $object): ?BSONDocument
    {
        if (!$object) {
            return null;
        }

        return new BSONDocument([
            '_id' => $object->getId()->id,
            'title' => $object->getTitle(),
            'createdAt' => $this->dateTimeMapper->toBson($object->getCreatedAt()),
            'participants' => $this->participantMapper->assocToBson($object->getParticipantsView()),
        ]);
    }

    public function fromBson(?BSONDocument $bson): ?ParticipantGroup
    {
        if (!$bson) {
            return null;
        }

        return new ParticipantGroup(
            new ParticipantGroupId($bson['_id']),
            $bson['title'] ?? null,
            $this->dateTimeMapper->fromBson($bson['createdAt'] ?? null),
            $this->participantMapper->assocFromBson($bson['participants'] ?? new BSONDocument())
        );
    }
}
