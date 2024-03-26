<?php

declare(strict_types=1);

namespace App\Domain\Bill\Model;

use App\Domain\ParticipantGroup\Model\ParticipantId;

/**
 * Represents that the participant should pay some part for himself or for another participant.
 * If participant pays for himself, in this case $itemPayer and $itemUser will be the same.
 * The exact amount for payment is calculated depending on other Payments in the BillItem.
 */
readonly class SplitOperation
{
    public ParticipantId $itemPayer; // participant who should pay some amount for the Item

    public ParticipantId $itemUser; // participant the Item has been bought for

    public function __construct(ParticipantId $itemPayer, ParticipantId $itemUser)
    {
        $this->itemPayer = $itemPayer;
        $this->itemUser = $itemUser;
    }
}
