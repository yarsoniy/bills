<?php

declare(strict_types=1);

namespace App\Domain\Bill\Model;

readonly class BillItemId
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
