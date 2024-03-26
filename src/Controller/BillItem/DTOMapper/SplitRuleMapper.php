<?php

declare(strict_types=1);

namespace App\Controller\BillItem\DTOMapper;

use App\Controller\BillItem\DTO\SplitRuleDTO;
use App\Domain\Bill\Model\SplitRule;
use App\Domain\ParticipantGroup\Model\ParticipantId;

class SplitRuleMapper
{
    /**
     * @param SplitRule[] $objects
     *
     * @return SplitRuleDTO[]
     */
    public function manyToDTO(array $objects): array
    {
        return array_map(fn (SplitRule $o) => $this->toDTO($o), $objects);
    }

    /**
     * @param SplitRuleDTO[] $DTOs
     *
     * @return SplitRule[]
     */
    public function manyFromDTO(array $DTOs): array
    {
        return array_map(fn (SplitRuleDTO $dto) => $this->fromDTO($dto), $DTOs);
    }

    public function toDTO(?SplitRule $object): ?SplitRuleDTO
    {
        if (!$object) {
            return null;
        }

        return new SplitRuleDTO(
            ParticipantId::toArray($object->itemPayers),
            ParticipantId::toArray($object->itemUsers),
        );
    }

    public function fromDTO(?SplitRuleDTO $dto): ?SplitRule
    {
        if (!$dto) {
            return null;
        }

        return new SplitRule(
            ParticipantId::fromArray($dto->itemPayers),
            ParticipantId::fromArray($dto->itemUsers),
        );
    }
}
