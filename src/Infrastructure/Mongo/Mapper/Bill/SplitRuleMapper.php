<?php

declare(strict_types=1);

namespace App\Infrastructure\Mongo\Mapper\Bill;

use App\Domain\Bill\Model\SplitRule;
use App\Domain\ParticipantGroup\Model\ParticipantId;
use App\Infrastructure\Mongo\Mapper\CollectionMapperTrait;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;

class SplitRuleMapper
{
    use CollectionMapperTrait;

    public function toBson(?SplitRule $object): ?BSONDocument
    {
        if (!$object) {
            return null;
        }

        return new BSONDocument([
            'itemPayers' => ParticipantId::toArray($object->itemPayers),
            'itemUsers' => ParticipantId::toArray($object->itemUsers),
        ]);
    }

    public function fromBson(?BSONDocument $bson): ?SplitRule
    {
        if (!$bson) {
            return null;
        }

        return new SplitRule(
            ParticipantId::fromArray($this->fromBsonScalarArray($bson['itemPayers'] ?? [])),
            ParticipantId::fromArray($this->fromBsonScalarArray($bson['itemUsers'] ?? [])),
        );
    }

    private function fromBsonScalarArray(BSONArray|array $array)
    {
        $result = [];
        foreach ($array as $key => $value) {
            $result[$key] = $value;
        }

        return $result;
    }
}
