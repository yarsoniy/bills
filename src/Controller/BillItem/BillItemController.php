<?php

declare(strict_types=1);

namespace App\Controller\BillItem;

use App\Application\UseCase\BillItemService;
use App\Controller\BillItem\DTO\BillItemDTO;
use App\Controller\BillItem\DTOMapper\BillItemMapper;
use App\Controller\BillItem\DTOMapper\PaymentMapper;
use App\Controller\Shared\BaseController;
use App\Domain\Bill\Exception\BillItemNotFoundException;
use App\Domain\Bill\Exception\BillNotFoundException;
use App\Domain\Bill\Model\BillId;
use App\Domain\Bill\Model\BillItemId;
use App\Domain\Money\Model\Money;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;

class BillItemController extends BaseController
{
    #[Route('/api/v1/bill/{billId}/item/{itemId}', methods: 'GET')]
    public function get(
        $billId,
        $itemId,
        BillItemService $billItemService,
        BillItemMapper $billItemMapper,
    ) {
        $validationErrors = $this->validateUrlParams(
            ['billId' => $billId, 'itemId' => $itemId],
            ['billId' => new Assert\Uuid(), 'itemId' => new Assert\Uuid()]
        );
        if ($validationErrors->count()) {
            return $this->errorValidationFailed($validationErrors);
        }

        try {
            $itemView = $billItemService->getItem(new BillId($billId), new BillItemId($itemId));
        } catch (BillNotFoundException|BillItemNotFoundException $e) {
            $this->errorNotFound($e->getMessage());
        }

        $resource = $billItemMapper->toDTO($itemView);

        return $this->success($this->normalize($resource));
    }

    #[Route('/api/v1/bill/{billId}/item/{itemId}', methods: 'PUT')]
    public function edit(
        $billId,
        $itemId,
        Request $request,
        PaymentMapper $paymentMapper,
        BillItemService $billItemService,
    ) {
        $validationErrors = $this->validateUrlParams(
            ['billId' => $billId, 'itemId' => $itemId],
            ['billId' => new Assert\Uuid(), 'itemId' => new Assert\Uuid()]
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
            $billItemService->editItem(
                new BillId($billId),
                new BillItemId($itemId),
                $dto->title,
                new Money($dto->cost ?? 0.0),
                $paymentMapper->manyFromDTO($dto->payments)
            );
        } catch (BillNotFoundException|BillItemNotFoundException $e) {
            $this->errorNotFound($e->getMessage());
        }

        return $this->success();
    }
}
