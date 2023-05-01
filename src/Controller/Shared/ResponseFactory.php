<?php

declare(strict_types=1);

namespace App\Controller\Shared;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseFactory
{
    public function createSuccess(array $data = [], int $status = Response::HTTP_OK): JsonResponse
    {
        $response = [];
        $response['success'] = true;
        if ($data) {
            $response['data'] = $data;
        }

        return new JsonResponse($response, $status);
    }
}
