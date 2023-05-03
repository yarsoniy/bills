<?php

declare(strict_types=1);

namespace App\Controller\Shared;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseController extends AbstractController
{
    public function __construct(
        readonly private ResponseFormatter $formatter,
        readonly private ValidatorInterface $validator,
        readonly private SerializerInterface $serializer,
    ) {
    }

    protected function parseRequest(Request $request, string $class): ?object
    {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), $class, 'json');
        } catch (UnexpectedValueException $e) {
            $dto = null;
        }

        return $dto;
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

    protected function errorCantParseRequest(): JsonResponse
    {
        $httpCode = Response::HTTP_BAD_REQUEST;
        $msg = "Can't parse request. Please check types and format";
        $errorResult = $this->formatter->formatError($msg);

        return new JsonResponse($errorResult, $httpCode);
    }

    protected function validateObject(object $input): ConstraintViolationListInterface
    {
        return $this->validateInput($input);
    }

    /**
     * @param Constraint[] $constraints
     */
    protected function validateUrlParams(array $urlParams, array $constraints): ConstraintViolationListInterface
    {
        return $this->validateInput(
            ['url' => $urlParams],
            new Assert\Collection(['url' => new Assert\Collection($constraints)])
        );
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
