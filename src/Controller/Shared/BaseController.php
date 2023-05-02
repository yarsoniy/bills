<?php

declare(strict_types=1);

namespace App\Controller\Shared;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseController extends AbstractController
{
    public function __construct(
        readonly private ResponseFormatter $formatter,
        readonly private ValidatorInterface $validator,
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

    protected function errorValidationFailed(ConstraintViolationListInterface $validationErrors): JsonResponse
    {
        $httpCode = Response::HTTP_BAD_REQUEST;
        $msg = Response::$statusTexts[$httpCode];
        $errorResult = $this->formatter->formatViolations($msg, $validationErrors);

        return new JsonResponse($errorResult, $httpCode);
    }

    protected function validateObject(object $input): ConstraintViolationListInterface
    {
        return $this->validateInput($input);
    }

    protected function validateParams(array $input, array $constraints): ConstraintViolationListInterface
    {
        return $this->validateInput($input, new Assert\Collection($constraints, null, null, true));
    }

    /**
     * @param Constraint|Constraint[]|null $constraints
     *
     * @return void
     */
    private function validateInput(object|array $input, Constraint|array|null $constraints = null): ConstraintViolationListInterface
    {
        return $this->validator->validate($input, $constraints);
    }
}
