<?php

declare(strict_types=1);

namespace App\Domain\Bill\Model;

use App\Domain\Participant\ParticipantId;

readonly class PaymentDirection
{
    public ParticipantId $payer;

    public ParticipantId $buyer;

    public function __construct(ParticipantId $payer, ParticipantId $buyer)
    {
        $this->payer = $payer;
        $this->buyer = $buyer;
    }
}
