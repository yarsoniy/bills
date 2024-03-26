<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Bill\Model;

use App\Domain\Bill\Model\SplitAgreement;
use App\Domain\Bill\Model\SplitRule;
use App\Domain\Money\Model\Money;
use App\Domain\ParticipantGroup\Model\ParticipantId;
use App\Tests\UnitTester;
use Codeception\Test\Unit;

class BillTest extends Unit
{
    protected UnitTester $tester;

    public function testCalculateTotalBreakdown()
    {
        $pA = new ParticipantId('participant-A');
        $pB = new ParticipantId('participant-B');
        $pC = new ParticipantId('participant-C');
        $pD = new ParticipantId('participant-D');

        // all pay equally
        $item1 = $this->tester->createBillItem([
            'id' => 'beer',
            'cost' => 400,
            'agreement' => new SplitAgreement([
                new SplitRule([$pA], [$pA]),
                new SplitRule([$pB], [$pB]),
                new SplitRule([$pC], [$pC]),
                new SplitRule([$pD], [$pD]),
            ]),
        ]);

        // all pay for D
        $item2 = $this->tester->createBillItem([
            'id' => 'pizza',
            'cost' => 300,
            'agreement' => new SplitAgreement([
                new SplitRule([$pA], [$pA]),
                new SplitRule([$pB], [$pB]),
                new SplitRule([$pC], [$pC]),
                new SplitRule([$pA, $pB, $pC], [$pD]),
            ]),
        ]);

        // A doesn't buy, all pay for D
        $item3 = $this->tester->createBillItem([
            'id' => 'salad',
            'cost' => 300,
            'agreement' => new SplitAgreement([
                new SplitRule([$pB], [$pB]),
                new SplitRule([$pC], [$pC]),
                new SplitRule([$pA, $pB, $pC], [$pD]),
            ]),
        ]);
        $item4 = $this->tester->createBillItem([
            'id' => 'meat',
            'cost' => 400,
            'agreement' => new SplitAgreement([
                new SplitRule([$pA], [$pA]),
                new SplitRule([$pB], [$pB]),
                new SplitRule([$pC], [$pC, $pD]),
            ]),
        ]);

        $bill = $this->tester->createBill();
        $bill->addItem($item1);
        $bill->addItem($item2);
        $bill->addItem($item3);
        $bill->addItem($item4);

        $expected = $this->tester->createMoneyBreakdown([
            'participant-A' => 333.34,
            'participant-B' => 433.33,
            'participant-C' => 533.33,
            'participant-D' => 100,
        ]);

        $actual = $bill->calculateTotalBreakdown();
        $this->assertEquals($bill->calculateTotalCost(), $actual->sum());

        $this->assertEquals($expected, $actual);
    }

    public function testAddItem()
    {
        $bill = $this->tester->createBill();
        $this->assertEquals(0, $bill->getCount());

        $bill->addItem($this->tester->createBillItem([
            'id' => 'item-1',
            'cost' => 100.12,
        ]));
        $this->assertEquals(1, $bill->getCount());

        $bill->addItem($this->tester->createBillItem([
            'id' => 'item-2',
            'cost' => 200.15,
        ]));
        $this->assertEquals(2, $bill->getCount());
    }

    public function testCalculateTotalCost()
    {
        $billItem1 = $this->tester->createBillItem([
            'id' => 'item-1',
            'cost' => 100.12,
        ]);
        $billItem2 = $this->tester->createBillItem([
            'id' => 'item-2',
            'cost' => 200.15,
        ]);
        $billItem3 = $this->tester->createBillItem([
            'id' => 'item-3',
            'cost' => 333.01,
        ]);
        $discount = $this->tester->createBillItem([
            'id' => 'discount',
            'cost' => -100.20,
        ]);

        $bill = $this->tester->createBill();
        $bill->addItem($billItem1);
        $bill->addItem($billItem2);
        $bill->addItem($billItem3);
        $bill->addItem($discount);

        $expected = new Money(533.08);
        $actual = $bill->calculateTotalCost();

        $this->assertEqualsWithDelta($expected, $actual, 0.01);
    }
}
