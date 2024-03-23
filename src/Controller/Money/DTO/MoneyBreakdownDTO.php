<?php

declare(strict_types=1);

namespace App\Controller\Money\DTO;

class MoneyBreakdownDTO
{
    public function __construct(
        /**
         * @var array<string, float>
         */
        readonly private ?array $values
    ) {
    }

    /**
     * @return array<string, float>
     */
    public function getValues()
    {
        // make sure that empty array is also serialized as object
        return $this->values ?: new \stdClass();
    }
}
