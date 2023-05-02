<?php

declare(strict_types=1);

namespace App\Controller\ParticipantGroup;

use App\Application\UseCase\ParticipantGroupService;
use App\Controller\ParticipantGroup\Request\AddParticipantRequest;
use App\Controller\ParticipantGroup\Request\CreateParticipantGroupRequest;
use App\Controller\Shared\BaseController;
use App\Domain\ParticipantGroup\Exception\ParticipantGroupNotFoundException;
use App\Domain\ParticipantGroup\Model\ParticipantGroupId;
use FOS\RestBundle\Controller\Annotations\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ParticipantGroupController extends BaseController
{
    #[Post('/api/v1/participant_group')]
    #[ParamConverter('request', converter: 'fos_rest.request_body')]
    public function create(
        CreateParticipantGroupRequest $request,
        ConstraintViolationListInterface $validationErrors,
        ParticipantGroupService $participantGroupService
    ): JsonResponse {
        if ($validationErrors->count()) {
            return $this->errorValidationFailed($validationErrors);
        }
        $groupId = $participantGroupService->createGroup($request->title);

        return $this->success(['id' => $groupId->id]);
    }

    #[Post('/api/v1/participant_group/{groupId}/participant')]
    #[ParamConverter('request', converter: 'fos_rest.request_body')]
    public function addParticipant(
        $groupId,
        AddParticipantRequest $request,
        ParticipantGroupService $participantGroupService
    ): JsonResponse {
        $validationErrors = $this->validateParams(
            ['groupId' => $groupId],
            ['groupId' => new Assert\Uuid()]
        );
        $validationErrors->addAll($this->validateObject($request));

        if ($validationErrors->count()) {
            return $this->errorValidationFailed($validationErrors);
        }

        try {
            $participantId = $participantGroupService->addParticipant(
                new ParticipantGroupId($groupId),
                $request->name
            );
        } catch (ParticipantGroupNotFoundException $e) {
            return $this->errorNotFound($e->getMessage());
        }

        return $this->success(['id' => $participantId->id]);
    }
}
