<?php

declare(strict_types=1);

namespace App\Controller\BillItem\DTOMapper;

use App\Controller\BillItem\DTO\SplitAgreementDTO;
use App\Domain\Bill\Model\SplitAgreement;

class SplitAgreementMapper
{
    public function __construct(
        readonly private SplitRuleMapper $ruleMapper
    ) {
    }

    public function toDTO(?SplitAgreement $object): ?SplitAgreementDTO
    {
        if (!$object) {
            return null;
        }

        return new SplitAgreementDTO(
            $this->ruleMapper->manyToDTO($object->rules)
        );
    }

    public function fromDTO(?SplitAgreementDTO $dto): ?SplitAgreement
    {
        if (!$dto) {
            return null;
        }

        return new SplitAgreement(
            $this->ruleMapper->manyFromDTO($dto->rules)
        );
    }
}
