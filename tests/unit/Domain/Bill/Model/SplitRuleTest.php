<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Bill\Model;

use App\Domain\Bill\Model\SplitOperation;
use App\Domain\Bill\Model\SplitRule;
use App\Domain\ParticipantGroup\Model\ParticipantId;
use Codeception\Test\Unit;

class SplitRuleTest extends Unit
{
    /**
     * @dataProvider providerGetOperations
     */
    public function testGetOperations($payers, $users, $expected)
    {
        $rule = new SplitRule($payers, $users);
        $actual = $rule->getOperations();
        $this->assertEquals($expected, $actual);
    }

    public function providerGetOperations()
    {
        return [
            [
                'payers' => [],
                'users' => [],
                'expected' => [],
            ],
            [
                'payers' => [new ParticipantId('A')],
                'users' => [new ParticipantId('B')],
                'expected' => [
                    new SplitOperation(new ParticipantId('A'), new ParticipantId('B')),
                ],
            ],
            [
                'payers' => [new ParticipantId('A'), new ParticipantId('B')],
                'users' => [new ParticipantId('C')],
                'expected' => [
                    new SplitOperation(new ParticipantId('A'), new ParticipantId('C')),
                    new SplitOperation(new ParticipantId('B'), new ParticipantId('C')),
                ],
            ],
            [
                'payers' => [new ParticipantId('A')],
                'users' => [new ParticipantId('B'), new ParticipantId('C')],
                'expected' => [
                    new SplitOperation(new ParticipantId('A'), new ParticipantId('B')),
                    new SplitOperation(new ParticipantId('A'), new ParticipantId('C')),
                ],
            ],
            [
                'payers' => [new ParticipantId('A'), new ParticipantId('B'), new ParticipantId('C')],
                'users' => [new ParticipantId('D'), new ParticipantId('E'), new ParticipantId('F')],
                'expected' => [
                    new SplitOperation(new ParticipantId('A'), new ParticipantId('D')),
                    new SplitOperation(new ParticipantId('A'), new ParticipantId('E')),
                    new SplitOperation(new ParticipantId('A'), new ParticipantId('F')),
                    new SplitOperation(new ParticipantId('B'), new ParticipantId('D')),
                    new SplitOperation(new ParticipantId('B'), new ParticipantId('E')),
                    new SplitOperation(new ParticipantId('B'), new ParticipantId('F')),
                    new SplitOperation(new ParticipantId('C'), new ParticipantId('D')),
                    new SplitOperation(new ParticipantId('C'), new ParticipantId('E')),
                    new SplitOperation(new ParticipantId('C'), new ParticipantId('F')),
                ],
            ],
            [
                'payers' => [new ParticipantId('A'), new ParticipantId('B'), new ParticipantId('C')],
                'users' => [new ParticipantId('A'), new ParticipantId('B'), new ParticipantId('C')],
                'expected' => [
                    new SplitOperation(new ParticipantId('A'), new ParticipantId('A')),
                    new SplitOperation(new ParticipantId('A'), new ParticipantId('B')),
                    new SplitOperation(new ParticipantId('A'), new ParticipantId('C')),
                    new SplitOperation(new ParticipantId('B'), new ParticipantId('A')),
                    new SplitOperation(new ParticipantId('B'), new ParticipantId('B')),
                    new SplitOperation(new ParticipantId('B'), new ParticipantId('C')),
                    new SplitOperation(new ParticipantId('C'), new ParticipantId('A')),
                    new SplitOperation(new ParticipantId('C'), new ParticipantId('B')),
                    new SplitOperation(new ParticipantId('C'), new ParticipantId('C')),
                ],
            ],
        ];
    }
}
