<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\AccountingBook\Model;

use App\Domain\AccountingBook\Model\RecordType;
use App\Tests\UnitTester;
use Codeception\Test\Unit;

class AccountingBookTest extends Unit
{
    protected UnitTester $tester;

    /**
     * @dataProvider providerCalculateBalance
     *
     * @param array[] $records
     *
     * @return void
     */
    public function testCalculateBalance(array $records, array $expected)
    {
        $book = $this->tester->createAccountingBook();
        foreach ($records as $recordData) {
            $type = $recordData['type'];
            $transactions = array_map(fn (array $item) => $this->tester->createTransaction(...$item), $recordData['transactions']);
            $record = $this->tester->createRecord(['type' => $type, 'transactions' => $transactions]);
            $book->addRecord($record);
        }

        $expectedBreakdown = $this->tester->createMoneyBreakdown($expected);
        $this->assertEquals($expectedBreakdown, $book->calculateBalance());
    }

    public function providerCalculateBalance(): array
    {
        return [
            [
                'records' => [
                    [
                        'type' => RecordType::LEND,
                        'transactions' => [
                            ['pA', 'pB', 200],
                            ['pA', 'pC', 100],
                            ['pA', 'pD', 50],
                        ],
                    ],
                ],
                'expected' => [
                    'pA' => 350,
                    'pB' => -200,
                    'pC' => -100,
                    'pD' => -50,
                ],
            ],
            [
                'records' => [
                    [
                        'type' => RecordType::LEND,
                        'transactions' => [
                            ['pA', 'pB', 200],
                            ['pA', 'pC', 100],
                            ['pA', 'pD', 50],
                        ],
                    ],
                    [
                        'type' => RecordType::PAY_BACK,
                        'transactions' => [
                            ['pB', 'pA', 120],
                            ['pC', 'pA', 70],
                            ['pD', 'pA', 30],
                        ],
                    ],
                ],
                'expected' => [
                    'pA' => 130,
                    'pB' => -80,
                    'pC' => -30,
                    'pD' => -20,
                ],
            ],
            [
                'records' => [
                    [
                        'type' => RecordType::LEND,
                        'transactions' => [
                            ['pA', 'pB', 200],
                            ['pA', 'pC', 100],
                            ['pA', 'pD', 50],
                        ],
                    ],
                    [
                        'type' => RecordType::PAY_BACK,
                        'transactions' => [
                            ['pB', 'pA', 120],
                            ['pC', 'pA', 70],
                            ['pD', 'pA', 30],
                        ],
                    ],
                    [
                        'type' => RecordType::DEBT_CANCELLATION,
                        'transactions' => [
                            ['pA', 'pB', 80],
                            ['pA', 'pC', 30],
                            ['pA', 'pD', 20],
                        ],
                    ],
                ],
                'expected' => [
                    'pA' => 0,
                    'pB' => 0,
                    'pC' => 0,
                    'pD' => 0,
                ],
            ],
        ];
    }
}
