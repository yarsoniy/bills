<?php

declare(strict_types=1);

namespace App\Domain\Bill\Model;

readonly class BillId
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
