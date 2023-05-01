<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\UseCase\ParticipantGroupService;
use App\Controller\Shared\BaseController;
use App\Domain\ParticipantGroup\Exception\ParticipantGroupNotFoundException;
use App\Domain\ParticipantGroup\Model\ParticipantGroupId;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class ParticipantGroupController extends BaseController
{
    #[Post('/api/v1/participant_group')]
    public function create(
        Request $request,
        ParticipantGroupService $participantGroupService
    ): JsonResponse {
        if ($errorResponse = $this->validateParams($request->toArray(), [
            'title' => new Assert\NotBlank(),
        ])) {
            return $errorResponse;
        }

        $title = $request->get('title');
        $groupId = $participantGroupService->createGroup($title);

        return $this->success(['id' => $groupId->id]);
    }

    #[Post('/api/v1/participant_group/{groupId}/participant')]
    public function addParticipant(
        $groupId,
        Request $request,
        ParticipantGroupService $participantGroupService
    ): JsonResponse {
        if ($errorResponse = $this->validateParams([
            'groupId' => $groupId,
            'name' => $request->get('name'),
        ], [
            'groupId' => new Assert\Uuid(),
            'name' => new Assert\NotBlank(),
        ])) {
            return $errorResponse;
        }

        try {
            $participantId = $participantGroupService->addParticipant(
                new ParticipantGroupId($groupId),
                $request->get('name')
            );
        } catch (ParticipantGroupNotFoundException $e) {
            return $this->errorNotFound($e->getMessage());
        }

        return $this->success(['id' => $participantId->id]);
    }
}
