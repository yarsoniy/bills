<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\DebtResolver\Service;

use App\Domain\DebtResolver\Service\DebtResolver;
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
    public function testResolve(array $balance, array $expected): void
    {
        $resolver = new DebtResolver();
        $actual = $resolver->resolve($this->tester->createMoneyBreakdown($balance));
        $expectedTransactions = array_map(fn (array $data) => $this->tester->createTransaction(...$data), $expected);
        $this->assertEquals($expectedTransactions, $actual);
    }

    public function providerResolve(): array
    {
        return [
            [
                'balance' => [
                    'pA' => 100,
                    'pB' => 50,
                    'pC' => -60,
                    'pD' => -90,
                ],
                'expected' => [
                    ['pD', 'pB', 50],
                    ['pD', 'pA', 40],
                    ['pC', 'pA', 60],
                ],
            ],
        ];
    }
}
