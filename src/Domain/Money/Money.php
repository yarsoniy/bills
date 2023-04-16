<?php

declare(strict_types=1);

namespace App\Domain\Money;

readonly class Money
{
    private const ROUND_PRECISION = 2;

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

    public function round(): self
    {
        return new self(round($this->value, self::ROUND_PRECISION));
    }

    /**
     * @return Money[]
     */
    public function split(int $count): array
    {
        $splitValue = new self($this->value / $count);

        $result = [];
        for ($i = 0; $i < $count; ++$i) {
            $result[] = $splitValue;
        }

        return $result;
    }

    public function splitByKey(array $keys): MoneyBreakdown
    {
        $keys = array_unique($keys);
        $values = $this->split(\count($keys));

        return new MoneyBreakdown(array_combine($keys, $values));
    }
}
