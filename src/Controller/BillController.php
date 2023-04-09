<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BillController
{
    #[Route("/api/test")]
    public function test()
    {
        return new JsonResponse(["success" => true, "test" => 'ololo']);
    }
}