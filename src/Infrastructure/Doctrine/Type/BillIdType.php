<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Type;

use App\Domain\Bill\Model\BillId;

class BillIdType extends BaseGuidType
{
    public function getName()
    {
        return 'BillId';
    }

    protected function getClass(): string
    {
        return BillId::class;
    }
}
