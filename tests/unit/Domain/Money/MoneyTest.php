<?php

declare(strict_types=1);

namespace App\Tests\unit\Domain\Money;

use App\Domain\Money\Money;
use Codeception\Test\Unit;

class MoneyTest extends Unit
{
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
}
