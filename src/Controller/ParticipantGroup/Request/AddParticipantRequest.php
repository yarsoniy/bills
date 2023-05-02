<?php

declare(strict_types=1);

namespace App\Controller\ParticipantGroup\Request;

use Symfony\Component\Validator\Constraints as Assert;

readonly class AddParticipantRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public ?string $name,

        #[Assert\Type(CreateParticipantGroupRequest::class)]
        /**
         * @var CreateParticipantGroupRequest
         */
        public CreateParticipantGroupRequest $group,
    ) {
    }
}
