<?php

declare(strict_types=1);

namespace App\Controller\Bill;

use App\Application\UseCase\BillService;
use App\Controller\Bill\ResponseMapper\BillResponseMapper;
use App\Controller\Shared\BaseController;
use App\Domain\Bill\Exception\BillNotFoundException;
use App\Domain\Bill\Model\BillId;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;

class BillController extends BaseController
{
    #[Route('/api/v1/bill/{billId}', methods: 'GET')]
    public function get(
        $billId,
        BillService $billService,
        BillResponseMapper $responseMapper
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

        $resource = $responseMapper->map($bill);

        return $this->success($this->normalize($resource));
    }

    #[Route('/api/v1/bill/{billId}', methods: 'PUT')]
    public function edit(
        $billId,
        Request $request,
        BillService $billService
    ) {
    }
}
