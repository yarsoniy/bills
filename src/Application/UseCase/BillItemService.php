<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Bill\Model\BillId;
use App\Domain\Bill\Model\BillItemId;
use App\Domain\Bill\Model\Payment;
use App\Domain\Bill\Service\BillRepositoryInterface;
use App\Domain\Bill\View\BillItemView;
use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;
use App\Domain\ParticipantGroup\Service\ParticipantGroupRepositoryInterface;

class BillItemService
{
    public function __construct(
        readonly private BillRepositoryInterface $billRepository,
        readonly private ParticipantGroupRepositoryInterface $groupRepository,
    ) {
    }

    public function getItem(BillId $billId, BillItemId $itemId): BillItemView
    {
        $bill = $this->billRepository->getById($billId);

        return $bill->getItemView($itemId);
    }

    public function createItem(BillId $id, string $title, Money $cost): BillItemId
    {
        $bill = $this->billRepository->getById($id);
        $group = $this->groupRepository->getById($bill->getGroupId());
        $itemId = $this->billRepository->nextItemId();

        $item = $bill->createItem($itemId, $title, $cost, $group);
        $bill->addItem($item);

        $this->billRepository->save($bill);

        return $itemId;
    }

    /**
     * @param Payment[] $payments
     */
    public function editItem(
        BillId $billId,
        BillItemId $itemId,
        string $title,
        Money $cost,
        array $payments
    ): void {
        $bill = $this->billRepository->getById($billId);
        $bill->setItemTitle($itemId, $title);
        $bill->setItemCost($itemId, $cost);
        $bill->setItemPayments($itemId, $payments);
        $this->billRepository->save($bill);
    }

    public function calculateBreakdown(BillId $billId, BillItemId $itemId): MoneyBreakdown
    {
        $bill = $this->billRepository->getById($billId);

        return $bill->calculateItemBreakdown($itemId);
    }
}
