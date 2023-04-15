<?php

declare(strict_types=1);

namespace App\Domain\Bill\Model;

use App\Domain\Money\Money;
use App\Domain\Participant\ParticipantId;

class Bill
{
    private BillId $id;

    private string $title;

    /** @var BillItem[] */
    private array $items;

    /**
     * Index by @see ParticipantId.
     *
     * @var Money[]
     */
    private array $deposits;
}
