<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use App\Application\Service\PersisterInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrinePersister implements PersisterInterface
{
    public function __construct(
        readonly private EntityManagerInterface $em
    ) {
    }

    public function flush(): void
    {
        $this->em->flush();
    }
}
