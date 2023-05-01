<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Bill\Model\BillId;
use App\Domain\Bill\Model\BillItem;
use App\Domain\Bill\Model\BillItemId;
use App\Domain\Bill\Model\Payment;
use App\Domain\Bill\Service\BillRepositoryInterface;
use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;

class BillItemService
{
    public function __construct(
        readonly private BillRepositoryInterface $repository
    ) {
    }

    public function createItem(BillId $id, string $title, Money $cost): BillItemId
    {
        $bill = $this->repository->getById($id);

        $itemId = $this->repository->nextItemId();
        $bill->addItem(new BillItem($itemId));
        $bill->setItemTitle($itemId, $title);
        $bill->setItemCost($itemId, $cost);

        return $itemId;
    }

    /**
     * @param Payment[] $payments
     */
    public function setPayments(BillItemId $itemId, array $payments): void
    {
        $bill = $this->repository->getByItemId($itemId);
        $bill->setItemPayments($itemId, $payments);
    }

    public function calculateBreakdown(BillItemId $itemId): MoneyBreakdown
    {
        $bill = $this->repository->getByItemId($itemId);

        return $bill->calculateItemBreakdown($itemId);
    }
}
