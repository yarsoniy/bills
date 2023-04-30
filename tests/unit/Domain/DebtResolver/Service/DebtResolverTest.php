<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\DebtResolver\Service;

use App\Domain\DebtResolver\Service\DebtResolver;
use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;
use App\Tests\UnitTester;
use Codeception\Test\Unit;

class DebtResolverTest extends Unit
{
    protected UnitTester $tester;

    /**
     * @dataProvider providerResolve
     *
     * @param array[] $expected
     */
    public function testResolve(MoneyBreakdown $balance, array $expected): void
    {
        $resolver = new DebtResolver();
        $actual = $resolver->resolve($balance);
        $expectedTransactions = array_map(fn (array $data) => $this->tester->createTransaction(...$data), $expected);
        $this->assertEquals($expectedTransactions, $actual);
    }

    public function providerResolve(): array
    {
        return [
            [
                'balance' => new MoneyBreakdown(
                    [
                        'pA' => new Money(100),
                        'pB' => new Money(50),
                        'pC' => new Money(-60),
                        'pD' => new Money(-90),
                    ]
                ),
                'expected' => [
                    ['pD', 'pB', 50],
                    ['pD', 'pA', 40],
                    ['pC', 'pA', 60],
                ],
            ],
        ];
    }
}
