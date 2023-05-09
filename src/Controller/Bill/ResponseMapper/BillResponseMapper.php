<?php

declare(strict_types=1);

namespace App\Controller\Bill\ResponseMapper;

use App\Controller\Bill\Resource\BillResource;
use App\Domain\Bill\Model\Bill;

class BillResponseMapper
{
    public function map(Bill $bill): BillResource
    {
        return new BillResource(
            $bill->getId()->id,
            $bill->getTitle(),
            $bill->getCreatedAt()
        );
    }

    /**
     * @param Bill[] $participants
     *
     * @return BillResource[]
     */
    public function mapMany(array $participants): array
    {
        $result = array_map(fn (Bill $object) => $this->map($object), $participants);

        return array_values($result);
    }
}
