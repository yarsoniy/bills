<?php

declare(strict_types=1);

namespace App\Controller\Shared;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    public function __construct(
        readonly private ResponseFormatter $formatter,
        readonly private ValidationHelper $validationHelper
    ) {
    }

    protected function success(array $data = [], int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse($this->formatter->formatSuccess(null, $data), $status);
    }

    protected function errorNotFound(string $msg, int $status = Response::HTTP_NOT_FOUND): JsonResponse
    {
        return new JsonResponse($this->formatter->formatError($msg), $status);
    }

    protected function validateObject(object $input): ?JsonResponse
    {
        return $this->validationHelper->validateObject($input);
    }

    protected function validateParams(array $input, array $constraints): ?JsonResponse
    {
        return $this->validationHelper->validateParams($input, $constraints);
    }
}
