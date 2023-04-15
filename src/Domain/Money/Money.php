<?php

declare(strict_types=1);

namespace App\Domain\Money;

readonly class Money
{
    public float $value;

    public function __construct(float $value = 0)
    {
        $this->value = $value;
    }

    public function add(self $money): self
    {
        return new self($this->value + $money->value);
    }

    public function sub(self $money): self
    {
        return new self($this->value - $money->value);
    }

    /**
     * @return Money[]
     */
    public function split(int $parts): array
    {
        $splitValue = $this->value / $parts;

        $result = [];
        for ($i = 0; $i < $parts; ++$i) {
            $result[] = new self($splitValue);
        }

        return $result;
    }
}
