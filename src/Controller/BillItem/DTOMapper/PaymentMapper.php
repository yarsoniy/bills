<?php

declare(strict_types=1);

namespace App\Controller\BillItem\DTOMapper;

use App\Controller\BillItem\DTO\PaymentDTO;
use App\Domain\Bill\Model\Payment;
use App\Domain\ParticipantGroup\Model\ParticipantId;

class PaymentMapper
{
    public function toDTO(Payment $payment): PaymentDTO
    {
        return new PaymentDTO(
            $payment->itemPayer->id,
            $payment->itemUser->id
        );
    }

    public function fromDTO(PaymentDTO $resource): Payment
    {
        return new Payment(
            new ParticipantId($resource->itemPayer),
            new ParticipantId($resource->itemUser),
        );
    }

    public function manyToDTO(array $payments): array
    {
        return array_map(fn (Payment $item) => $this->toDTO($item), $payments);
    }

    public function manyFromDTO(?array $DTOs): ?array
    {
        if (!$DTOs) {
            return [];
        }

        return array_map(fn (PaymentDTO $item) => $this->fromDTO($item), $DTOs);
    }
}
