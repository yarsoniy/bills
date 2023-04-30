<?php

declare(strict_types=1);

namespace App\Tests\Helper\FixtureFactory;

use App\Domain\Bill\Model\Bill;
use App\Domain\Bill\Model\BillItem;
use App\Domain\Money\Model\Money;

class BillFixtureFactory
{
    public function createBill(): Bill
    {
        return new Bill();
    }

    public function createBillItem(array $params): BillItem
    {
        $title = $params['title'];
        $cost = $params['cost'] ?? new Money();

        return new BillItem($title, $cost);
    }
}
