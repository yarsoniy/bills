<?php

declare(strict_types=1);

namespace App\Controller\Shared;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ErrorFormatter
{
    public function formatError(string $msg, array $path, $details = null): array
    {
        $error = [];
        $error['message'] = $msg;
        if ($path) {
            $elements = array_map(fn ($item) => '['.$item.']', $path);
            $error['propertyPath'] = implode('', $elements);
        }
        if ($details) {
            $error['details'] = $details;
        }

        return ['error' => $error];
    }

    public function formatViolations(string $msg, ConstraintViolationListInterface $violations): array
    {
        $error = [];
        $error['message'] = $msg;

        $children = [];
        foreach ($violations as $violation) {
            $children[] = [
                'message' => $violation->getMessage(),
                'propertyPath' => $violation->getPropertyPath(),
            ];
        }

        if ($children) {
            $error['children'] = $children;
        }

        return ['error' => $error];
    }
}
