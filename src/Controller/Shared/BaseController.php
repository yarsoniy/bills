<?php

declare(strict_types=1);

namespace App\Controller\Shared;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    public function __construct(
        readonly private ResponseFactory $responseFactory,
        readonly private ValidationHelper $validationHelper
    ) {
    }

    protected function success(array $data = [], int $status = Response::HTTP_OK): JsonResponse
    {
        return $this->responseFactory->createSuccess($data, $status);
    }

    protected function validateObject(object $input): ?JsonResponse
    {
        return $this->validationHelper->validateObject($input);
    }

    protected function validateParams(Request $request, array $constraints): ?JsonResponse
    {
        return $this->validationHelper->validateParams($request->toArray(), $constraints);
    }
}
