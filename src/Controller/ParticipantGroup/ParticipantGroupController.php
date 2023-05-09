<?php

declare(strict_types=1);

namespace App\Controller\ParticipantGroup;

use App\Application\UseCase\BillService;
use App\Application\UseCase\ParticipantGroupService;
use App\Controller\Bill\Resource\BillResource;
use App\Controller\Bill\ResponseMapper\BillResponseMapper;
use App\Controller\ParticipantGroup\Resource\ParticipantGroupResource;
use App\Controller\ParticipantGroup\Resource\ParticipantResource;
use App\Controller\ParticipantGroup\ResponseMapper\ParticipantGroupResponseMapper;
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
        /** @var ParticipantGroupResource $dto */
        if (!$dto = $this->denormalize($request, ParticipantGroupResource::class)) {
            return $this->errorCantParseRequest();
        }

        $validationErrors = $this->validateObject($dto);
        if ($validationErrors->count()) {
            return $this->errorValidationFailed($validationErrors);
        }

        $groupId = $participantGroupService->createGroup($dto->getTitle());

        return $this->success(['id' => $groupId->id]);
    }

    #[Route('/api/v1/participant_group/{groupId}', methods: 'GET')]
    public function get(
        $groupId,
        ParticipantGroupService $participantGroupService,
        ParticipantGroupResponseMapper $responseMapper
    ) {
        $validationErrors = $this->validateUrlParams(
            ['groupId' => $groupId],
            ['groupId' => new Assert\Uuid()]
        );
        if ($validationErrors->count()) {
            return $this->errorValidationFailed($validationErrors);
        }

        try {
            $group = $participantGroupService->getGroup(new ParticipantGroupId($groupId));
        } catch (ParticipantGroupNotFoundException $e) {
            return $this->errorNotFound($e->getMessage());
        }
        $groupResource = $responseMapper->map($group);

        return $this->success($this->normalize($groupResource));
    }

    #[Route('/api/v1/participant_group/{groupId}/participant', methods: 'POST')]
    public function addParticipant(
        $groupId,
        Request $request,
        ParticipantGroupService $participantGroupService
    ): JsonResponse {
        /** @var ParticipantResource $dto */
        if (!$dto = $this->denormalize($request, ParticipantResource::class)) {
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
                $dto->getName()
            );
        } catch (ParticipantGroupNotFoundException $e) {
            return $this->errorNotFound($e->getMessage());
        }

        return $this->success(['id' => $participantId->id]);
    }

    #[Route('/api/v1/participant_group/{groupId}/bill', methods: 'POST')]
    public function createBill(
        $groupId,
        Request $request,
        BillService $billService
    ) {
        /** @var BillResource $dto */
        if (!$dto = $this->denormalize($request, BillResource::class)) {
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
            $billId = $billService->createBill(new ParticipantGroupId($groupId), $dto->getTitle());
        } catch (ParticipantGroupNotFoundException $e) {
            return $this->errorNotFound($e->getMessage());
        }

        return $this->success(['id' => $billId->id]);
    }

    #[Route('/api/v1/participant_group/{groupId}/bill', methods: 'GET')]
    public function getBillList(
        $groupId,
        BillService $billService,
        BillResponseMapper $responseMapper
    ) {
        $validationErrors = $this->validateUrlParams(
            ['groupId' => $groupId],
            ['groupId' => new Assert\Uuid()]
        );
        if ($validationErrors->count()) {
            return $this->errorValidationFailed($validationErrors);
        }

        try {
            $bills = $billService->findByParticipantGroup(new ParticipantGroupId($groupId));
        } catch (ParticipantGroupNotFoundException $e) {
            return $this->errorBadRequest($e->getMessage());
        }
        $resources = $responseMapper->mapMany($bills);

        return $this->success($this->normalize($resources));
    }
}
