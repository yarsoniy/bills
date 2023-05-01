<?php

declare(strict_types=1);

namespace App\Domain\Bill\Exception;

use App\Domain\Bill\Model\BillItemId;

class BillItemNotFoundException extends \DomainException
{
    public static function withId(BillItemId $id): self
    {
        return new self('Bill Item not found with id: '.$id->id);
    }
}
