<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\UseCase\ParticipantGroupService;
use App\Controller\Shared\BaseController;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ParticipantGroupController extends BaseController
{
    #[Post('/api/v1/participant_group')]
    public function createParticipantGroup(
        Request $request,
        ParticipantGroupService $participantGroupService
    ): JsonResponse {
        if ($errorResponse = $this->validateParams($request, ['title' => new Assert\NotBlank()])) {
            return $errorResponse;
        }

        $title = $request->get('title');
        $id = $participantGroupService->createGroup($title);

        return $this->success(['id' => $id->id]);
    }
}
