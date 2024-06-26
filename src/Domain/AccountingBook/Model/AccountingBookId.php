<?php

declare(strict_types=1);

namespace App\Domain\AccountingBook\Model;

readonly class AccountingBookId
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
