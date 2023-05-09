<?php

declare(strict_types=1);

namespace App\Tests\Helper\FixtureFactory;

use App\Domain\Bill\Model\Bill;
use App\Domain\Bill\Model\BillId;
use App\Domain\Bill\Model\BillItem;
use App\Domain\Bill\Model\BillItemId;
use App\Domain\Bill\Model\Payment;
use App\Domain\Money\Model\Money;
use App\Domain\ParticipantGroup\Model\ParticipantGroupId;

class BillFixtureFactory
{
    public function createBill(array $params = []): Bill
    {
        $id = $params['id'] ?? new BillId('test-bill');
        $groupId = $params['groupId'] ?? new ParticipantGroupId('test-group');

        return new Bill($id, $groupId);
    }

    public function createBillItem(array $params): BillItem
    {
        $id = $params['id'];
        $item = new BillItem(new BillItemId($id));
        if ($title = $params['title'] ?? null) {
            $item->setTitle($title);
        }
        if ($cost = $params['cost'] ?? null) {
            $item->setCost(new Money($cost));
        }
        foreach ($params['payments'] ?? [] as $paymentParams) {
            $item->addPayment(new Payment(...$paymentParams));
        }

        return $item;
    }
}
