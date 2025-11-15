<?php

namespace App\Commands;

class UpdateAppointmentStatusCommand
{
    public function __construct(
        public readonly int $appointmentId,
        public readonly string $status,
        public readonly ?string $notes = null
    ) {
    }
}
