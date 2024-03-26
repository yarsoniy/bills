<?php

declare(strict_types=1);

namespace App\Controller\BillItem\DTOMapper;

use App\Controller\BillItem\DTO\BillItemDTO;
use App\Controller\Money\DTOMapper\MoneyBreakdownMapper;
use App\Domain\Bill\View\BillItemView;

class BillItemMapper
{
    public function __construct(
        readonly private MoneyBreakdownMapper $breakdownMapper,
        readonly private SplitAgreementMapper $agreementMapper,
    ) {
    }

    public function toDTO(BillItemView $item): BillItemDTO
    {
        return new BillItemDTO(
            $item->getId()->id,
            $item->getTitle(),
            $item->getCreatedAt(),
            $item->getCost()->round()->value,
            $this->breakdownMapper->toDTO($item->calculateBreakdown()->round()),
            $this->agreementMapper->toDTO($item->getAgreement())
        );
    }

    public function toDTOPreview(BillItemView $item): BillItemDTO
    {
        return new BillItemDTO(
            $item->getId()->id,
            $item->getTitle(),
            $item->getCreatedAt(),
            $item->getCost()->round()->value,
            null,
            null
        );
    }

    public function manyToDTOPreview(array $items): array
    {
        $result = array_map(fn (BillItemView $object) => $this->toDTOPreview($object), $items);

        return array_values($result);
    }
}
