<?php

declare(strict_types=1);

namespace App\Domain\Bill\Exception;

use App\Domain\Bill\Model\BillId;

class BillNotFoundException extends \DomainException
{
    public static function withId(BillId $id): self
    {
        return new self('Bill not found with id: '.$id->id);
    }
}
