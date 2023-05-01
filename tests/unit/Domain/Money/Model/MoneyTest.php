<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Money\Model;

use App\Domain\Money\Model\Money;
use App\Tests\UnitTester;
use Codeception\Test\Unit;

class MoneyTest extends Unit
{
    protected UnitTester $tester;

    public function testAdd()
    {
        $a = new Money(777.23);
        $b = new Money(333.44);

        $expected = new Money(1110.67);
        $actual = $a->add($b);

        $this->assertEquals($expected, $actual);
    }

    public function testSub()
    {
        $a = new Money(1110.67);
        $b = new Money(333.44);

        $expected = new Money(777.23);
        $actual = $a->sub($b);

        $this->assertEquals($expected, $actual);
    }

    public function testNegative()
    {
        $original = new Money(43.76);

        $negative = $original->negative();
        $this->assertEquals($negative, new Money(-43.76));
        $this->assertEquals($negative->negative(), new Money(43.76));
    }

    public function testAbs()
    {
        $original = new Money(-43.76);
        $this->assertEquals($original->abs(), new Money(43.76));
        $this->assertEquals($original->abs()->abs(), new Money(43.76));
    }

    public function testSplitBy3()
    {
        $a = new Money(1000);

        $expected = [
            new Money(333.33333),
            new Money(333.33333),
            new Money(333.33333),
        ];
        $actual = $a->split(3);

        $this->assertEqualsWithDelta($expected, $actual, 0.00001);
    }

    public function testSplitBy0()
    {
        $a = new Money(1000);
        $this->expectException(\DivisionByZeroError::class);
        $a->split(0);
    }

    public function testSplitByKey()
    {
        $a = new Money(1000);
        $expected = $this->tester->createMoneyBreakdown([
            'key1' => 333.33333,
            'key2' => 333.33333,
            'key3' => 333.33333,
        ]);
        $actual = $a->splitByKey(['key1', 'key2', 'key3']);
        $this->assertEqualsWithDelta($expected, $actual, 0.00001);
    }

    /**
     * @dataProvider providerRound
     *
     * @return void
     */
    public function testRound(float $original, float $expected)
    {
        $this->assertEquals(new Money($expected), (new Money($original))->round());
    }

    public function providerRound(): array
    {
        return [
            [
                'original' => 33.333333,
                'expected' => 33.33,
            ],
            [
                'original' => 66.666666,
                'expected' => 66.67,
            ],
            [
                'original' => 20.025,
                'expected' => 20.03,
            ],
        ];
    }
}
