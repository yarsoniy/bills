<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Bill\Model;

use App\Domain\Bill\Model\Bill;
use App\Domain\Bill\Model\BillItem;
use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;
use App\Domain\Participant\ParticipantId;
use Codeception\Test\Unit;

class BillTest extends Unit
{
    public function testCalculateTotalBreakdown()
    {
        $pA = new ParticipantId('participant-A');
        $pB = new ParticipantId('participant-B');
        $pC = new ParticipantId('participant-C');
        $pD = new ParticipantId('participant-D');

        // all pay equally
        $item1 = new BillItem('beer', new Money(400));
        $item1->addPaymentDirection($pA, $pA);
        $item1->addPaymentDirection($pB, $pB);
        $item1->addPaymentDirection($pC, $pC);
        $item1->addPaymentDirection($pD, $pD);

        // all pay for D
        $item2 = new BillItem('pizza', new Money(300));
        $item2->addPaymentDirection($pA, $pA);
        $item2->addPaymentDirection($pB, $pB);
        $item2->addPaymentDirection($pC, $pC);
        $item2->addPaymentDirection($pA, $pD);
        $item2->addPaymentDirection($pB, $pD);
        $item2->addPaymentDirection($pC, $pD);

        // A doesn't buy, all pay for D
        $item3 = new BillItem('salad', new Money(300));
        $item3->addPaymentDirection($pB, $pB);
        $item3->addPaymentDirection($pC, $pC);
        $item3->addPaymentDirection($pA, $pD);
        $item3->addPaymentDirection($pB, $pD);
        $item3->addPaymentDirection($pC, $pD);

        $item4 = new BillItem('meat', new Money(400));
        $item4->addPaymentDirection($pA, $pA);
        $item4->addPaymentDirection($pB, $pB);
        $item4->addPaymentDirection($pC, $pC);
        $item4->addPaymentDirection($pC, $pD);

        $bill = new Bill();
        $bill->addItem($item1);
        $bill->addItem($item2);
        $bill->addItem($item3);
        $bill->addItem($item4);

        $expected = new MoneyBreakdown(
            [
                'participant-A' => new Money(333.34),
                'participant-B' => new Money(433.33),
                'participant-C' => new Money(533.33),
                'participant-D' => new Money(100),
            ]
        );

        $actual = $bill->calculateTotalBreakdown();
        $this->assertEquals($bill->calculateTotal(), $actual->sum());

        $this->assertEquals($expected, $actual);
    }

    public function testAddItem()
    {
        $bill = new Bill();
        $this->assertEquals(0, $bill->getCount());

        $bill->addItem(new BillItem('item-1', new Money(100.12)));
        $this->assertEquals(1, $bill->getCount());

        $bill->addItem(new BillItem('item-2', new Money(200.15)));
        $this->assertEquals(2, $bill->getCount());
    }

    public function testCalculateTotal()
    {
        $billItem1 = new BillItem('item-1', new Money(100.12));
        $billItem2 = new BillItem('item-2', new Money(200.15));
        $billItem3 = new BillItem('item-3', new Money(333.01));
        $discount = new BillItem('discount', new Money(-100.20));

        $bill = new Bill();
        $bill->addItem($billItem1);
        $bill->addItem($billItem2);
        $bill->addItem($billItem3);
        $bill->addItem($discount);

        $expected = new Money(533.08);
        $actual = $bill->calculateTotal();

        $this->assertEqualsWithDelta($expected, $actual, 0.01);
    }
}
