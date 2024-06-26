<?php

declare(strict_types=1);

namespace App\Controller\Shared;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
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

    protected function denormalize(Request $request, string $class, array $groups = ['*']): ?object
    {
        try {
            $dto = $this->serializer->denormalize(
                $request->toArray(),
                $class,
                'json',
                [
                    'groups' => $groups,
                    DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s',
                    DateTimeNormalizer::TIMEZONE_KEY => 'UTC',
                ]
            );
        } catch (UnexpectedValueException|JsonException $e) {
            $dto = null;
        }

        return $dto;
    }

    protected function normalize(object|array $dto, array $groups = ['*']): array
    {
        return $this->serializer->normalize($dto, 'json', [
            'groups' => $groups,
            DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s',
            DateTimeNormalizer::TIMEZONE_KEY => 'UTC',
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            ObjectNormalizer::PRESERVE_EMPTY_OBJECTS => true,
        ]);
    }

    protected function success(array $data = [], int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse($this->formatter->formatSuccess(null, $data), $status);
    }

    protected function errorNotFound(string $msg, int $status = Response::HTTP_NOT_FOUND): JsonResponse
    {
        return new JsonResponse($this->formatter->formatError($msg), $status);
    }

    protected function errorBadRequest(string $msg, int $status = Response::HTTP_BAD_REQUEST): JsonResponse
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
