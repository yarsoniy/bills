<?php

declare(strict_types=1);

namespace App\Controller\ParticipantGroup\Request;

use Symfony\Component\Validator\Constraints as Assert;

class AddParticipantRequest
{
    #[Assert\NotBlank]
    /** @var string */
    public $name;

    #[Assert\Valid]
    /** @var CreateParticipantGroupRequest[] */
    public $groups;
}
