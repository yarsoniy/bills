<?php

declare(strict_types=1);

namespace App\Application\Service;

interface PersisterInterface
{
    public function flush(): void;
}
