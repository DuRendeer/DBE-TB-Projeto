<?php

namespace App\Commands;

class CreateAppointmentCommand
{
    public function __construct(
        public readonly int $userId,
        public readonly int $petId,
        public readonly int $serviceId,
        public readonly string $scheduledAt,
        public readonly float $totalPrice,
        public readonly ?string $notes = null
    ) {
    }
}
