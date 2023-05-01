<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Money\Model;

use App\Domain\Money\Model\Money;
use App\Tests\UnitTester;
use Codeception\Test\Unit;

class MoneyBreakdownTest extends Unit
{
    protected UnitTester $tester;

    public function testGet()
    {
        $mb = $this->tester->createMoneyBreakdown([
            'item-1' => 100,
            'item-2' => 200,
        ]);

        $this->assertEquals(new Money(100), $mb->get('item-1'));
        $this->assertEquals(new Money(200), $mb->get('item-2'));
        $this->assertEquals(new Money(), $mb->get('item-3'));
    }

    public function testAdd()
    {
        $mb = $this->tester->createMoneyBreakdown([
            'item-1' => 100,
            'item-2' => 200,
        ]);

        $mb = $mb->add('item-3', new Money(300));

        $expected = [
            'item-1' => new Money(100),
            'item-2' => new Money(200),
            'item-3' => new Money(300),
        ];

        $this->assertEquals($expected, $mb->items);
    }

    public function testKeys()
    {
        $mb = $this->tester->createMoneyBreakdown([
            'item-1' => 100,
            'item-2' => 200,
        ]);

        $expected = ['item-1', 'item-2'];
        $this->assertEquals($expected, $mb->keys());
    }

    public function testMerge()
    {
        $mb1 = $this->tester->createMoneyBreakdown([
            'item-1' => 100,
            'item-2' => 200,
        ]);
        $mb2 = $this->tester->createMoneyBreakdown([
            'item-3' => 300,
            'item-4' => 400,
        ]);
        $expected = $this->tester->createMoneyBreakdown([
            'item-1' => 100,
            'item-2' => 200,
            'item-3' => 300,
            'item-4' => 400,
        ]);
        $this->assertEquals($expected, $mb1->merge($mb2));
    }

    public function testSum()
    {
        $mb = $this->tester->createMoneyBreakdown([
            'item-1' => 100,
            'item-2' => 200,
            'item-3' => 300,
            'item-4' => 400,
        ]);

        $expected = new Money(1000);
        $this->assertEquals($expected, $mb->sum());
    }

    public function testRound()
    {
        $mb = $this->tester->createMoneyBreakdown([
            'item-1' => 33.33333,
            'item-2' => 66.66666,
            'item-3' => 99.99999,
        ]);
        $expected = $this->tester->createMoneyBreakdown([
            'item-1' => 33.33,
            'item-2' => 66.67,
            'item-3' => 100.00,
        ]);
        $this->assertEquals($expected, $mb->round());
    }

    /**
     * @dataProvider providerRoundWithCorrection
     *
     * @return void
     */
    public function testRoundWithCorrection(array $breakdown, float $desiredTotal, array $expected)
    {
        $breakdownObject = $this->tester->createMoneyBreakdown($breakdown);
        $actualObject = $breakdownObject->roundWithCorrection(new Money($desiredTotal));
        $expectedObject = $this->tester->createMoneyBreakdown($expected);
        $this->assertEquals($expectedObject, $actualObject);
    }

    public function providerRoundWithCorrection(): array
    {
        return [
            [
                'breakdown' => [
                    'item-1' => 33.33333,
                    'item-2' => 33.33333,
                    'item-3' => 33.33333,
                ],
                'desired_total' => 100,
                'expected' => [
                    'item-1' => 33.34,
                    'item-2' => 33.33,
                    'item-3' => 33.33,
                ],
            ],
            [
                'breakdown' => [
                    'item-1' => 33.33333,
                    'item-2' => 33.33333,
                    'item-3' => 33.33333,
                ],
                'desired_total' => 100.01,
                'expected' => [
                    'item-1' => 33.34,
                    'item-2' => 33.34,
                    'item-3' => 33.33,
                ],
            ],
            [
                'breakdown' => [
                    'item-1' => 0,
                    'item-2' => 33.33333,
                    'item-3' => 33.33333,
                    'item-4' => 33.33333,
                ],
                'desired_total' => 100,
                'expected' => [
                    'item-1' => 0,
                    'item-2' => 33.34,
                    'item-3' => 33.33,
                    'item-4' => 33.33,
                ],
            ],
            [
                'breakdown' => [
                    'item-1' => 10,
                    'item-2' => 10,
                    'item-3' => 10,
                ],
                'desired_total' => 40,
                'expected' => [
                    'item-1' => 13.34,
                    'item-2' => 13.33,
                    'item-3' => 13.33,
                ],
            ],
            [
                'breakdown' => [
                    'item-1' => 10,
                    'item-2' => 10,
                    'item-3' => 10,
                ],
                'desired_total' => 10,
                'expected' => [
                    'item-1' => 3.33,
                    'item-2' => 3.33,
                    'item-3' => 3.34,
                ],
            ],
        ];
    }
}
