<?php

declare(strict_types=1);

namespace App\Controller\ParticipantGroup\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CreateParticipantGroupRequest
{
    #[Assert\NotBlank]
    /** @var string */
    public $title;
}
