<?php

declare(strict_types=1);

namespace App\Controller\BillItem\DTOMapper;

use App\Controller\BillItem\DTO\BillItemDTO;
use App\Controller\Money\DTOMapper\MoneyBreakdownMapper;
use App\Domain\Bill\View\BillItemView;

class BillItemMapper
{
    public function __construct(
        readonly private PaymentMapper $paymentMapper,
        readonly private MoneyBreakdownMapper $breakdownMapper
    ) {
    }

    public function toDTO(BillItemView $item): BillItemDTO
    {
        return new BillItemDTO(
            $item->getId()->id,
            $item->getTitle(),
            $item->getCreatedAt(),
            $item->getCost()->round()->value,
            $this->paymentMapper->manyToDTO($item->getPayments()) ?: null,
            $this->breakdownMapper->toDTO($item->calculateBreakdown()->round())
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
