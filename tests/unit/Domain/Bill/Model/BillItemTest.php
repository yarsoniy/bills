<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Bill\Model;

use App\Domain\Bill\Model\BillItem;
use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;
use App\Domain\Participant\Model\ParticipantId;
use Codeception\Test\Unit;

class BillItemTest extends Unit
{
    public function testCalculateBreakdownEqual()
    {
        $billItem = new BillItem('Pizza');
        $billItem->setCost(new Money(400));

        $pA = new ParticipantId('participant-A');
        $pB = new ParticipantId('participant-B');
        $pC = new ParticipantId('participant-C');
        $pD = new ParticipantId('participant-D');
        $billItem->addPayment($pA, $pA);
        $billItem->addPayment($pB, $pB);
        $billItem->addPayment($pC, $pC);
        $billItem->addPayment($pD, $pD);

        $expected = new MoneyBreakdown(
            [
                'participant-A' => new Money(100),
                'participant-B' => new Money(100),
                'participant-C' => new Money(100),
                'participant-D' => new Money(100),
            ]
        );
        $actual = $billItem->calculateBreakdown();

        $this->assertEquals($expected, $actual);
    }

    public function testCalculateBreakdownAllPayForD()
    {
        $billItem = new BillItem('Pizza');
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

        $expected = new MoneyBreakdown(
            [
                'participant-A' => new Money(100),
                'participant-B' => new Money(100),
                'participant-C' => new Money(100),
            ]
        );
        $actual = $billItem->calculateBreakdown();

        $this->assertEquals($expected, $actual);
    }

    public function testCalculateBreakdownADoesntBuyAllPayForD()
    {
        $billItem = new BillItem('Pizza');
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

        $expected = new MoneyBreakdown(
            [
                'participant-A' => new Money(33.33),
                'participant-B' => new Money(133.33),
                'participant-C' => new Money(133.33),
            ]
        );
        $actual = $billItem->calculateBreakdown();

        $this->assertEqualsWithDelta($expected, $actual, 0.01);
    }

    public function testCalculateBreakdownCPayForD()
    {
        $billItem = new BillItem('Pizza');
        $billItem->setCost(new Money(400));

        $pA = new ParticipantId('participant-A');
        $pB = new ParticipantId('participant-B');
        $pC = new ParticipantId('participant-C');
        $pD = new ParticipantId('participant-D');

        $billItem->addPayment($pA, $pA);
        $billItem->addPayment($pB, $pB);
        $billItem->addPayment($pC, $pC);
        $billItem->addPayment($pC, $pD);

        $expected = new MoneyBreakdown(
            [
                'participant-A' => new Money(100),
                'participant-B' => new Money(100),
                'participant-C' => new Money(200),
            ]
        );
        $actual = $billItem->calculateBreakdown();

        $this->assertEqualsWithDelta($expected, $actual, 0.01);
    }
}
