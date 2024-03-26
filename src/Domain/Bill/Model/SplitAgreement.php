<?php

declare(strict_types=1);

namespace App\Domain\Bill\Model;

use App\Domain\ParticipantGroup\Model\ParticipantId;

readonly class SplitAgreement
{
    public static function createEqual(array $participantIds): self
    {
        return new self(array_map(fn (ParticipantId $pId) => new SplitRule([$pId], [$pId]), $participantIds));
    }

    public function __construct(
        /** @var SplitRule[] */
        public array $rules
    ) {
    }

    /**
     * @return SplitOperation[]
     */
    public function getOperations(): array
    {
        $result = [];
        foreach ($this->rules as $rule) {
            $result = [...$result, ...$rule->getOperations()];
        }

        return $result;
    }
}
