<?php

namespace App\Domain\Money;

readonly class Money
{
    public float $value;

    public function __construct(float $value = 0)
    {
        $this->value = $value;
    }

    public function add(Money $money): Money
    {
        return new self($this->value + $money->value);
    }

    public function sub(Money $money): Money
    {
        return new self($this->value - $money->value);
    }

    /**
     * @param int $parts
     * @return Money[]
     */
    public function split(int $parts): array
    {
        $splitValue = $this->value / $parts;

        $result = [];
        for ($i = 0; $i < $parts; $i++) {
            $result[] = new self($splitValue);
        }

        return $result;
    }
}
