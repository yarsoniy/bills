<?php

declare(strict_types=1);

namespace App\Infrastructure\Mongo\Mapper\Bill;

use App\Domain\Bill\Model\Payment;
use App\Domain\ParticipantGroup\Model\ParticipantId;
use App\Infrastructure\Mongo\Mapper\CollectionMapperTrait;
use MongoDB\Model\BSONDocument;

class PaymentMapper
{
    use CollectionMapperTrait;

    public function toBson(?Payment $object): ?BSONDocument
    {
        if (!$object) {
            return null;
        }

        return new BSONDocument([
            'itemPayer' => $object->itemPayer->id,
            'itemUser' => $object->itemUser->id,
        ]);
    }

    public function fromBson(?BSONDocument $bson): ?Payment
    {
        if (!$bson) {
            return null;
        }

        return new Payment(
            new ParticipantId($bson['itemPayer']),
            new ParticipantId($bson['itemUser']),
        );
    }
}
