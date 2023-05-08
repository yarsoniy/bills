<?php

declare(strict_types=1);

namespace App\Controller\Bill;

use App\Application\UseCase\BillService;
use App\Controller\Shared\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BillController extends BaseController
{
    #[Route('/api/v1/participant_group/{groupId}', methods: 'POST')]
    public function create(
        $groupId,
        Request $request,
        BillService $billService
    ) {
    }

    #[Route('/api/v1/bill/{billId}', methods: 'PUT')]
    public function edit(
        $billId,
        Request $request,
        BillService $billService
    ) {
    }
}
