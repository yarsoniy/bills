<?php

declare(strict_types=1);

namespace App\Controller\Shared;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationHelper
{
    public function __construct(
        readonly private ValidatorInterface $validator,
        readonly private ErrorFormatter $errorFormatter
    ) {
    }

    public function validateObject(object $input): ?JsonResponse
    {
        return $this->validateInput($input);
    }

    public function validateParams(array $params, array $constraintsByParams): ?JsonResponse
    {
        return $this->validateInput(
            $params,
            new Assert\Collection($constraintsByParams, null, null, true)
        );
    }

    /**
     * @param Constraint|Constraint[]|null $constraints
     *
     * @return void
     */
    private function validateInput(object|array $input, Constraint|array|null $constraints = null): ?JsonResponse
    {
        $errorList = $this->validator->validate($input, $constraints);
        if (!$errorList->count()) {
            return null;
        }

        $httpCode = Response::HTTP_BAD_REQUEST;
        $msg = Response::$statusTexts[$httpCode];
        $errorResult = $this->errorFormatter->formatViolations($msg, $errorList);

        return new JsonResponse($errorResult, $httpCode);
    }
}
