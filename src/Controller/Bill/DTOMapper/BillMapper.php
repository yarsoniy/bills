<?php

declare(strict_types=1);

namespace App\Controller\Bill\DTOMapper;

use App\Controller\Bill\DTO\BillDTO;
use App\Controller\BillItem\DTOMapper\BillItemMapper;
use App\Controller\Money\DTOMapper\MoneyBreakdownMapper;
use App\Domain\Bill\Model\Bill;

class BillMapper
{
    public function __construct(
        readonly private BillItemMapper $itemResponseMapper,
        readonly private MoneyBreakdownMapper $breakdownMapper
    ) {
    }

    public function toDTO(Bill $bill): BillDTO
    {
        return new BillDTO(
            $bill->getId()->id,
            $bill->getTitle(),
            $bill->getCreatedAt(),
            $this->itemResponseMapper->manyToDTOPreview($bill->getItemsView()),
            $bill->calculateTotalCost()->value,
            $this->breakdownMapper->toDTO($bill->calculateTotalBreakdown())
        );
    }

    /**
     * @param Bill[] $participants
     *
     * @return BillDTO[]
     */
    public function manyToDTO(array $participants): array
    {
        $result = array_map(fn (Bill $object) => $this->toDTO($object), $participants);

        return array_values($result);
    }
}
