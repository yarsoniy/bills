<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Bill\Model;

use App\Domain\Money\Model\Money;
use App\Domain\ParticipantGroup\Model\ParticipantId;
use App\Tests\UnitTester;
use Codeception\Test\Unit;

class BillItemTest extends Unit
{
    protected UnitTester $tester;

    public function testCalculateBreakdownEqual()
    {
        $billItem = $this->tester->createBillItem(['title' => 'Pizza']);
        $billItem->setCost(new Money(400));

        $pA = new ParticipantId('participant-A');
        $pB = new ParticipantId('participant-B');
        $pC = new ParticipantId('participant-C');
        $pD = new ParticipantId('participant-D');
        $billItem->addPayment($pA, $pA);
        $billItem->addPayment($pB, $pB);
        $billItem->addPayment($pC, $pC);
        $billItem->addPayment($pD, $pD);

        $expected = $this->tester->createMoneyBreakdown([
            'participant-A' => 100,
            'participant-B' => 100,
            'participant-C' => 100,
            'participant-D' => 100,
        ]);
        $actual = $billItem->calculateBreakdown();

        $this->assertEquals($expected, $actual);
    }

    public function testCalculateBreakdownAllPayForD()
    {
        $billItem = $this->tester->createBillItem(['title' => 'Pizza']);
        $billItem->setCost(new Money(300));

        $pA = new ParticipantId('participant-A');
        $pB = new ParticipantId('participant-B');
        $pC = new ParticipantId('participant-C');
        $pD = new ParticipantId('participant-D');

        $billItem->addPayment($pA, $pA);
        $billItem->addPayment($pB, $pB);
        $billItem->addPayment($pC, $pC);
        $billItem->addPayment($pA, $pD);
        $billItem->addPayment($pB, $pD);
        $billItem->addPayment($pC, $pD);

        $expected = $this->tester->createMoneyBreakdown([
            'participant-A' => 100,
            'participant-B' => 100,
            'participant-C' => 100,
        ]);
        $actual = $billItem->calculateBreakdown();

        $this->assertEquals($expected, $actual);
    }

    public function testCalculateBreakdownADoesntBuyAllPayForD()
    {
        $billItem = $this->tester->createBillItem(['title' => 'Pizza']);
        $billItem->setCost(new Money(300));

        $pA = new ParticipantId('participant-A');
        $pB = new ParticipantId('participant-B');
        $pC = new ParticipantId('participant-C');
        $pD = new ParticipantId('participant-D');

        $billItem->addPayment($pB, $pB);
        $billItem->addPayment($pC, $pC);
        $billItem->addPayment($pA, $pD);
        $billItem->addPayment($pB, $pD);
        $billItem->addPayment($pC, $pD);

        $expected = $this->tester->createMoneyBreakdown([
            'participant-A' => 33.33,
            'participant-B' => 133.33,
            'participant-C' => 133.33,
        ]);
        $actual = $billItem->calculateBreakdown();

        $this->assertEqualsWithDelta($expected, $actual, 0.01);
    }

    public function testCalculateBreakdownCPayForD()
    {
        $billItem = $this->tester->createBillItem(['title' => 'Pizza']);
        $billItem->setCost(new Money(400));

        $pA = new ParticipantId('participant-A');
        $pB = new ParticipantId('participant-B');
        $pC = new ParticipantId('participant-C');
        $pD = new ParticipantId('participant-D');

        $billItem->addPayment($pA, $pA);
        $billItem->addPayment($pB, $pB);
        $billItem->addPayment($pC, $pC);
        $billItem->addPayment($pC, $pD);

        $expected = $this->tester->createMoneyBreakdown([
            'participant-A' => 100,
            'participant-B' => 100,
            'participant-C' => 200,
        ]);
        $actual = $billItem->calculateBreakdown();

        $this->assertEqualsWithDelta($expected, $actual, 0.01);
    }
}
