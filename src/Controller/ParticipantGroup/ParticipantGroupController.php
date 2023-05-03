<?php

declare(strict_types=1);

namespace App\Controller\ParticipantGroup;

use App\Application\UseCase\ParticipantGroupService;
use App\Controller\ParticipantGroup\Request\AddParticipantRequest;
use App\Controller\ParticipantGroup\Request\CreateParticipantGroupRequest;
use App\Controller\Shared\BaseController;
use App\Domain\ParticipantGroup\Exception\ParticipantGroupNotFoundException;
use App\Domain\ParticipantGroup\Model\ParticipantGroupId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;

class ParticipantGroupController extends BaseController
{
    #[Route('/api/v1/participant_group', methods: 'POST')]
    public function create(
        Request $request,
        ParticipantGroupService $participantGroupService
    ): JsonResponse {
        /** @var CreateParticipantGroupRequest $dto */
        if (!$dto = $this->parseRequest($request, CreateParticipantGroupRequest::class)) {
            return $this->errorCantParseRequest();
        }

        $validationErrors = $this->validateObject($dto);
        if ($validationErrors->count()) {
            return $this->errorValidationFailed($validationErrors);
        }

        $groupId = $participantGroupService->createGroup($dto->title);

        return $this->success(['id' => $groupId->id]);
    }

    #[Route('/api/v1/participant_group/{groupId}/participant', methods: 'POST')]
    public function addParticipant(
        $groupId,
        Request $request,
        ParticipantGroupService $participantGroupService
    ): JsonResponse {
        /** @var AddParticipantRequest $dto */
        if (!$dto = $this->parseRequest($request, AddParticipantRequest::class)) {
            return $this->errorCantParseRequest();
        }

        $validationErrors = $this->validateUrlParams(
            ['groupId' => $groupId],
            ['groupId' => new Assert\Uuid()]
        );
        $validationErrors->addAll($this->validateObject($dto));

        if ($validationErrors->count()) {
            return $this->errorValidationFailed($validationErrors);
        }

        try {
            $participantId = $participantGroupService->addParticipant(
                new ParticipantGroupId($groupId),
                $dto->name
            );
        } catch (ParticipantGroupNotFoundException $e) {
            return $this->errorNotFound($e->getMessage());
        }

        return $this->success(['id' => $participantId->id]);
    }
}
