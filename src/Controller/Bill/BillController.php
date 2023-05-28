<?php

declare(strict_types=1);

namespace App\Controller\Bill;

use App\Application\UseCase\BillItemService;
use App\Application\UseCase\BillService;
use App\Controller\Bill\DTOMapper\BillMapper;
use App\Controller\BillItem\DTO\BillItemDTO;
use App\Controller\Shared\BaseController;
use App\Domain\Bill\Exception\BillNotFoundException;
use App\Domain\Bill\Model\BillId;
use App\Domain\Money\Model\Money;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;

class BillController extends BaseController
{
    #[Route('/api/v1/bill/{billId}', methods: 'GET')]
    public function get(
        $billId,
        BillService $billService,
        BillMapper $responseMapper
    ) {
        $validationErrors = $this->validateUrlParams(
            ['billId' => $billId],
            ['billId' => new Assert\Uuid()]
        );
        if ($validationErrors->count()) {
            return $this->errorValidationFailed($validationErrors);
        }

        try {
            $bill = $billService->getBill(new BillId($billId));
        } catch (BillNotFoundException $e) {
            return $this->errorNotFound($e->getMessage());
        }

        $resource = $responseMapper->toDTO($bill);

        return $this->success($this->normalize($resource));
    }

    #[Route('/api/v1/bill/{billId}/item', methods: 'POST')]
    public function addItem(
        $billId,
        Request $request,
        BillItemService $billItemService
    ) {
        $validationErrors = $this->validateUrlParams(
            ['billId' => $billId],
            ['billId' => new Assert\Uuid()]
        );
        /** @var BillItemDTO $dto */
        if (!$dto = $this->denormalize($request, BillItemDTO::class)) {
            return $this->errorCantParseRequest();
        }
        $validationErrors->addAll($this->validateObject($dto));
        if ($validationErrors->count()) {
            return $this->errorValidationFailed($validationErrors);
        }

        try {
            $itemId = $billItemService->createItem(
                new BillId($billId),
                $dto->title,
                new Money($dto->cost ?? 0.0),
            );
        } catch (BillNotFoundException $e) {
            $this->errorNotFound($e->getMessage());
        }

        return $this->success(['id' => $itemId->id]);
    }

    #[Route('/api/v1/bill/{billId}', methods: 'PUT')]
    public function edit(
        $billId,
        Request $request,
        BillService $billService
    ) {
        // TODO implement
    }
}
