<?php

declare(strict_types=1);

namespace App\Controller\ParticipantGroup\Request;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AddParticipantRequest
{
    #[Assert\NotBlank]
    #[Serializer\Type('string')]
    /** @var string */
    public $name;

    #[Assert\Valid]
    #[Serializer\Type(CreateParticipantGroupRequest::class)]
    /** @var CreateParticipantGroupRequest */
    public $group;
}
