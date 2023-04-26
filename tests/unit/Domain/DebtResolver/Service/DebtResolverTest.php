<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\DebtResolver\Service;

use App\Domain\AccountingBook\Model\Operation;
use App\Domain\AccountingBook\Model\OperationType;
use App\Domain\DebtResolver\Service\DebtResolver;
use App\Domain\Money\Model\Money;
use App\Domain\Money\Model\MoneyBreakdown;
use App\Domain\Participant\ParticipantId;
use Codeception\Test\Unit;

class DebtResolverTest extends Unit
{
    /**
     * @dataProvider providerResolve
     *
     * @param Operation[] $expected
     */
    public function testResolve(MoneyBreakdown $balance, array $expected): void
    {
        $resolver = new DebtResolver();
        $actual = $resolver->resolve($balance);

        $this->assertEquals($expected, $actual);
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
                    new Operation(OperationType::PAY_BACK, new ParticipantId('pD'), new ParticipantId('pB'), new Money(50)),
                    new Operation(OperationType::PAY_BACK, new ParticipantId('pD'), new ParticipantId('pA'), new Money(40)),
                    new Operation(OperationType::PAY_BACK, new ParticipantId('pC'), new ParticipantId('pA'), new Money(60)),
                ],
            ],
        ];
    }
}
