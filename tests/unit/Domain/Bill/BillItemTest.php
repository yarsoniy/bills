<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Bill;

use App\Domain\Bill\Model\BillItem;
use App\Domain\Money\Money;
use App\Domain\Participant\ParticipantId;
use Codeception\Test\Unit;

class BillItemTest extends Unit
{
    public function testCalculatePayerSharesEqual()
    {
        $billItem = new BillItem('Pizza');
        $billItem->setCost(new Money(400));

        $pA = new ParticipantId('participant-A');
        $pB = new ParticipantId('participant-B');
        $pC = new ParticipantId('participant-C');
        $pD = new ParticipantId('participant-D');
        $billItem->addPaymentDirection($pA, $pA);
        $billItem->addPaymentDirection($pB, $pB);
        $billItem->addPaymentDirection($pC, $pC);
        $billItem->addPaymentDirection($pD, $pD);

        $expected = [
            'participant-A' => new Money(100),
            'participant-B' => new Money(100),
            'participant-C' => new Money(100),
            'participant-D' => new Money(100),
        ];
        $actual = $billItem->calculatePayerShares();

        $this->assertEquals($expected, $actual);
    }

    public function testCalculatePayerSharesAllPayForD()
    {
        $billItem = new BillItem('Pizza');
        $billItem->setCost(new Money(300));

        $pA = new ParticipantId('participant-A');
        $pB = new ParticipantId('participant-B');
        $pC = new ParticipantId('participant-C');
        $pD = new ParticipantId('participant-D');

        $billItem->addPaymentDirection($pA, $pA);
        $billItem->addPaymentDirection($pB, $pB);
        $billItem->addPaymentDirection($pC, $pC);
        $billItem->addPaymentDirection($pA, $pD);
        $billItem->addPaymentDirection($pB, $pD);
        $billItem->addPaymentDirection($pC, $pD);

        $expected = [
            'participant-A' => new Money(100),
            'participant-B' => new Money(100),
            'participant-C' => new Money(100),
        ];
        $actual = $billItem->calculatePayerShares();

        $this->assertEquals($expected, $actual);
    }

    public function testCalculatePayerSharesADoesntBuyAllPayForD()
    {
        $billItem = new BillItem('Pizza');
        $billItem->setCost(new Money(300));

        $pA = new ParticipantId('participant-A');
        $pB = new ParticipantId('participant-B');
        $pC = new ParticipantId('participant-C');
        $pD = new ParticipantId('participant-D');

        $billItem->addPaymentDirection($pB, $pB);
        $billItem->addPaymentDirection($pC, $pC);
        $billItem->addPaymentDirection($pA, $pD);
        $billItem->addPaymentDirection($pB, $pD);
        $billItem->addPaymentDirection($pC, $pD);

        $expected = [
            'participant-A' => new Money(33.33),
            'participant-B' => new Money(133.33),
            'participant-C' => new Money(133.33),
        ];
        $actual = $billItem->calculatePayerShares();

        $this->assertEqualsWithDelta($expected, $actual, 0.01);
    }

    public function testCalculatePayerSharesCPayForD()
    {
        $billItem = new BillItem('Pizza');
        $billItem->setCost(new Money(400));

        $pA = new ParticipantId('participant-A');
        $pB = new ParticipantId('participant-B');
        $pC = new ParticipantId('participant-C');
        $pD = new ParticipantId('participant-D');

        $billItem->addPaymentDirection($pA, $pA);
        $billItem->addPaymentDirection($pB, $pB);
        $billItem->addPaymentDirection($pC, $pC);
        $billItem->addPaymentDirection($pC, $pD);

        $expected = [
            'participant-A' => new Money(100),
            'participant-B' => new Money(100),
            'participant-C' => new Money(200),
        ];
        $actual = $billItem->calculatePayerShares();

        $this->assertEqualsWithDelta($expected, $actual, 0.01);
    }
}
