<?php

declare(strict_types=1);

namespace App\Infrastructure\Mongo\Mapper\DateTime;

use MongoDB\BSON\UTCDateTime;

class DateTimeMapper
{
    public function toBson(?\DateTimeImmutable $d): ?UTCDateTime
    {
        return $d ? new UTCDateTime($d) : null;
    }

    public function fromBson(?UTCDateTime $d): ?\DateTimeImmutable
    {
        return $d ? \DateTimeImmutable::createFromMutable($d->toDateTime()) : null;
    }
}
