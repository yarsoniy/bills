<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Bill\Model;

use App\Domain\ParticipantGroup\Model\ParticipantId;
use App\Tests\UnitTester;
use Codeception\Test\Unit;

class BillItemTest extends Unit
{
    protected UnitTester $tester;

    public function testCalculateBreakdownEqual()
    {
        $pA = new ParticipantId('participant-A');
        $pB = new ParticipantId('participant-B');
        $pC = new ParticipantId('participant-C');
        $pD = new ParticipantId('participant-D');

        $billItem = $this->tester->createBillItem([
            'id' => 'pizza',
            'cost' => 400,
            'payments' => [
                [$pA, $pA],
                [$pB, $pB],
                [$pC, $pC],
                [$pD, $pD],
            ],
        ]);

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
        $pA = new ParticipantId('participant-A');
        $pB = new ParticipantId('participant-B');
        $pC = new ParticipantId('participant-C');
        $pD = new ParticipantId('participant-D');

        $billItem = $this->tester->createBillItem([
            'id' => 'pizza',
            'cost' => 300,
            'payments' => [
                [$pA, $pA],
                [$pB, $pB],
                [$pC, $pC],
                [$pA, $pD],
                [$pB, $pD],
                [$pC, $pD],
            ],
        ]);

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
        $pA = new ParticipantId('participant-A');
        $pB = new ParticipantId('participant-B');
        $pC = new ParticipantId('participant-C');
        $pD = new ParticipantId('participant-D');

        $billItem = $this->tester->createBillItem([
            'id' => 'pizza',
            'cost' => 300,
            'payments' => [
                [$pB, $pB],
                [$pC, $pC],
                [$pA, $pD],
                [$pB, $pD],
                [$pC, $pD],
            ],
        ]);

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
        $pA = new ParticipantId('participant-A');
        $pB = new ParticipantId('participant-B');
        $pC = new ParticipantId('participant-C');
        $pD = new ParticipantId('participant-D');

        $billItem = $this->tester->createBillItem([
            'id' => 'pizza',
            'cost' => 400,
            'payments' => [
                [$pA, $pA],
                [$pB, $pB],
                [$pC, $pC],
                [$pC, $pD],
            ],
        ]);

        $expected = $this->tester->createMoneyBreakdown([
            'participant-A' => 100,
            'participant-B' => 100,
            'participant-C' => 200,
        ]);
        $actual = $billItem->calculateBreakdown();

        $this->assertEqualsWithDelta($expected, $actual, 0.01);
    }
}
