<?php

declare(strict_types=1);

namespace App\Controller\ParticipantGroup;

use App\Application\UseCase\BillService;
use App\Application\UseCase\ParticipantGroupService;
use App\Controller\Bill\DTO\BillDTO;
use App\Controller\Bill\DTOMapper\BillMapper;
use App\Controller\ParticipantGroup\DTO\ParticipantDTO;
use App\Controller\ParticipantGroup\DTO\ParticipantGroupDTO;
use App\Controller\ParticipantGroup\DTOMapper\ParticipantGroupMapper;
use App\Controller\Shared\BaseController;
use App\Domain\ParticipantGroup\Exception\ParticipantGroupNotFoundException;
use App\Domain\ParticipantGroup\Model\ParticipantGroupId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;

class ParticipantGroupController extends BaseController
{
    #[Route('/api/v1/participant_group', methods: 'GET')]
    public function getGroups(
        ParticipantGroupService $participantGroupService,
        ParticipantGroupMapper $responseMapper
    ): JsonResponse {
        $groups = $participantGroupService->getGroups();
        $DTOs = $responseMapper->manyToDTO($groups);

        return $this->success($this->normalize($DTOs));
    }

    #[Route('/api/v1/participant_group', methods: 'POST')]
    public function create(
        Request $request,
        ParticipantGroupService $participantGroupService
    ): JsonResponse {
        /** @var ParticipantGroupDTO $dto */
        if (!$dto = $this->denormalize($request, ParticipantGroupDTO::class)) {
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
        ParticipantGroupMapper $responseMapper
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
        $groupResource = $responseMapper->toDTO($group);

        return $this->success($this->normalize($groupResource));
    }

    #[Route('/api/v1/participant_group/{groupId}/participant', methods: 'POST')]
    public function addParticipant(
        $groupId,
        Request $request,
        ParticipantGroupService $participantGroupService
    ): JsonResponse {
        /** @var ParticipantDTO $dto */
        if (!$dto = $this->denormalize($request, ParticipantDTO::class)) {
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
        /** @var BillDTO $dto */
        if (!$dto = $this->denormalize($request, BillDTO::class)) {
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
            $billId = $billService->createBill(new ParticipantGroupId($groupId), $dto->title);
        } catch (ParticipantGroupNotFoundException $e) {
            return $this->errorNotFound($e->getMessage());
        }

        return $this->success(['id' => $billId->id]);
    }

    #[Route('/api/v1/participant_group/{groupId}/bill', methods: 'GET')]
    public function getBillList(
        $groupId,
        BillService $billService,
        BillMapper $responseMapper
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
        $resources = $responseMapper->manyToDTO($bills);

        return $this->success($this->normalize($resources));
    }
}
