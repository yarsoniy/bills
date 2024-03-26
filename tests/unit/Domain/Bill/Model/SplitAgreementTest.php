<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Bill\Model;

use App\Domain\Bill\Model\SplitAgreement;
use App\Domain\Bill\Model\SplitOperation;
use App\Domain\Bill\Model\SplitRule;
use App\Domain\ParticipantGroup\Model\ParticipantId;
use Codeception\Test\Unit;

class SplitAgreementTest extends Unit
{
    /**
     * @dataProvider providerGetOperations
     */
    public function testGetOperations($rules, $expected)
    {
        $agreement = new SplitAgreement($rules);
        $actual = $agreement->getOperations();
        $this->assertEquals($expected, $actual);
    }

    public function providerGetOperations()
    {
        return [
            [
                'rules' => [
                    new SplitRule([new ParticipantId('A')], [new ParticipantId('B')]),
                    new SplitRule([new ParticipantId('C')], [new ParticipantId('D')]),
                ],
                'expected' => [
                    new SplitOperation(new ParticipantId('A'), new ParticipantId('B')),
                    new SplitOperation(new ParticipantId('C'), new ParticipantId('D')),
                ],
            ],
            [
                'rules' => [
                    new SplitRule([new ParticipantId('A'), new ParticipantId('B')], [new ParticipantId('C')]),
                    new SplitRule([new ParticipantId('C')], [new ParticipantId('D'), new ParticipantId('E')]),
                ],
                'expected' => [
                    new SplitOperation(new ParticipantId('A'), new ParticipantId('C')),
                    new SplitOperation(new ParticipantId('B'), new ParticipantId('C')),
                    new SplitOperation(new ParticipantId('C'), new ParticipantId('D')),
                    new SplitOperation(new ParticipantId('C'), new ParticipantId('E')),
                ],
            ],
        ];
    }
}
