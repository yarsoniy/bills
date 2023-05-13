<?php

declare(strict_types=1);

namespace App\Infrastructure\Mongo;

use App\Application\Service\PersisterInterface;

class MongoPersister implements PersisterInterface
{
    public function flush(): void
    {
        // TODO: Implement flush() method.
    }
}
