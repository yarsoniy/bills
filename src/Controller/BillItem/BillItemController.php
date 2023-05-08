<?php

declare(strict_types=1);

namespace App\Controller\BillItem;

use App\Application\UseCase\BillItemService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BillItemController
{
    #[Route('/api/v1/bill/{billId}', methods: 'POST')]
    public function create(
        $billId,
        Request $request,
        BillItemService $billItemService
    ) {
    }

    #[Route('/api/v1/bill_item/{billItemId}', methods: 'PUT')]
    public function edit(
        $billItemId,
        Request $request,
        BillItemService $billItemService
    ) {
    }
}
