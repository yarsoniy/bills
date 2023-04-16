<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Money;

use App\Domain\Money\Money;
use App\Domain\Money\MoneyBreakdown;
use Codeception\Test\Unit;

class MoneyBreakdownTest extends Unit
{
    public function testGet()
    {
        $mb = new MoneyBreakdown(
            [
                'item-1' => new Money(100),
                'item-2' => new Money(200),
            ]
        );

        $this->assertEquals(new Money(100), $mb->get('item-1'));
        $this->assertEquals(new Money(200), $mb->get('item-2'));
        $this->assertEquals(new Money(), $mb->get('item-3'));
    }

    public function testAdd()
    {
        $mb = new MoneyBreakdown(
            [
                'item-1' => new Money(100),
                'item-2' => new Money(200),
            ]
        );

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
        $mb = new MoneyBreakdown(
            [
                'item-1' => new Money(100),
                'item-2' => new Money(200),
            ]
        );

        $expected = ['item-1', 'item-2'];
        $this->assertEquals($expected, $mb->keys());
    }

    public function testMerge()
    {
        $mb1 = new MoneyBreakdown(
            [
                'item-1' => new Money(100),
                'item-2' => new Money(200),
            ]
        );

        $mb2 = new MoneyBreakdown(
            [
                'item-3' => new Money(300),
                'item-4' => new Money(400),
            ]
        );

        $expected = new MoneyBreakdown(
            [
                'item-1' => new Money(100),
                'item-2' => new Money(200),
                'item-3' => new Money(300),
                'item-4' => new Money(400),
            ]
        );
        $this->assertEquals($expected, $mb1->merge($mb2));
    }

    public function testSum()
    {
        $mb = new MoneyBreakdown(
            [
                'item-1' => new Money(100),
                'item-2' => new Money(200),
                'item-3' => new Money(300),
                'item-4' => new Money(400),
            ]
        );

        $expected = new Money(1000);
        $this->assertEquals($expected, $mb->sum());
    }

    public function testRound()
    {
        $mb = new MoneyBreakdown(
            [
                'item-1' => new Money(33.33333),
                'item-2' => new Money(66.66666),
                'item-3' => new Money(99.99999),
            ]
        );

        $expected = new MoneyBreakdown(
            [
                'item-1' => new Money(33.33),
                'item-2' => new Money(66.67),
                'item-3' => new Money(100.00),
            ]
        );

        $this->assertEquals($expected, $mb->round());
    }

    /**
     * @dataProvider providerRoundWithCorrection
     *
     * @return void
     */
    public function testRoundWithCorrection(MoneyBreakdown $breakdown, Money $desiredTotal, MoneyBreakdown $expected)
    {
        $actual = $breakdown->roundWithCorrection($desiredTotal);
        $this->assertEquals($expected, $actual);
    }

    public function providerRoundWithCorrection(): array
    {
        return [
            [
                'breakdown' => new MoneyBreakdown(
                    [
                        'item-1' => new Money(33.33333),
                        'item-2' => new Money(33.33333),
                        'item-3' => new Money(33.33333),
                    ]
                ),
                'desired_total' => new Money(100),
                'expected' => new MoneyBreakdown(
                    [
                        'item-1' => new Money(33.34),
                        'item-2' => new Money(33.33),
                        'item-3' => new Money(33.33),
                    ]
                ),
            ],
            [
                'breakdown' => new MoneyBreakdown(
                    [
                        'item-1' => new Money(33.33333),
                        'item-2' => new Money(33.33333),
                        'item-3' => new Money(33.33333),
                    ]
                ),
                'desired_total' => new Money(100.01),
                'expected' => new MoneyBreakdown(
                    [
                        'item-1' => new Money(33.34),
                        'item-2' => new Money(33.34),
                        'item-3' => new Money(33.33),
                    ]
                ),
            ],
            [
                'breakdown' => new MoneyBreakdown(
                    [
                        'item-1' => new Money(0),
                        'item-2' => new Money(33.33333),
                        'item-3' => new Money(33.33333),
                        'item-4' => new Money(33.33333),
                    ]
                ),
                'desired_total' => new Money(100),
                'expected' => new MoneyBreakdown(
                    [
                        'item-1' => new Money(0),
                        'item-2' => new Money(33.34),
                        'item-3' => new Money(33.33),
                        'item-4' => new Money(33.33),
                    ]
                ),
            ],
            [
                'breakdown' => new MoneyBreakdown(
                    [
                        'item-1' => new Money(10),
                        'item-2' => new Money(10),
                        'item-3' => new Money(10),
                    ]
                ),
                'desired_total' => new Money(40),
                'expected' => new MoneyBreakdown(
                    [
                        'item-1' => new Money(13.34),
                        'item-2' => new Money(13.33),
                        'item-3' => new Money(13.33),
                    ]
                ),
            ],
            [
                'breakdown' => new MoneyBreakdown(
                    [
                        'item-1' => new Money(10),
                        'item-2' => new Money(10),
                        'item-3' => new Money(10),
                    ]
                ),
                'desired_total' => new Money(10),
                'expected' => new MoneyBreakdown(
                    [
                        'item-1' => new Money(3.33),
                        'item-2' => new Money(3.33),
                        'item-3' => new Money(3.34),
                    ]
                ),
            ],
            [
                'breakdown' => new MoneyBreakdown(
                    [
                        'item-1' => new Money(10),
                        'item-2' => new Money(10),
                        'item-3' => new Money(10),
                    ]
                ),
                'desired_total' => new Money(10),
                'expected' => new MoneyBreakdown(
                    [
                        'item-1' => new Money(3.33),
                        'item-2' => new Money(3.33),
                        'item-3' => new Money(3.34),
                    ]
                ),
            ],
        ];
    }
}
