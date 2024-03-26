<?php

declare(strict_types=1);

namespace App\Domain\Bill\Model;

use App\Domain\ParticipantGroup\Model\ParticipantId;

readonly class SplitRule
{
    public function __construct(
        /** @var ParticipantId[] */
        public array $itemPayers,   // participants who should pay some amount for the Item

        /** @var ParticipantId[] */
        public array $itemUsers,    // participants the Item has been bought for
    ) {
    }

    /**
     * @return SplitOperation[]
     */
    public function getOperations(): array
    {
        $result = [];
        foreach ($this->itemPayers as $payer) {
            foreach ($this->itemUsers as $user) {
                $result[] = new SplitOperation($payer, $user);
            }
        }

        return $result;
    }
}
