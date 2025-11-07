<?php

namespace App\Commands;

/**
 * CQRS Command - Represents a write operation (Update Appointment Status)
 */
class UpdateAppointmentStatusCommand
{
    public function __construct(
        public readonly int $appointmentId,
        public readonly string $status,
        public readonly ?string $notes = null
    ) {
    }

    /**
     * Valid appointment statuses
     */
    private const VALID_STATUSES = [
        'pending',
        'confirmed',
        'in_progress',
        'completed',
        'cancelled'
    ];

    /**
     * Validate command data
     */
    public function validate(): array
    {
        $errors = [];

        if (!in_array($this->status, self::VALID_STATUSES)) {
            $errors[] = 'Invalid status. Valid options: ' . implode(', ', self::VALID_STATUSES);
        }

        return $errors;
    }

    /**
     * Convert to array for database update
     */
    public function toArray(): array
    {
        $data = ['status' => $this->status];

        if ($this->notes !== null) {
            $data['notes'] = $this->notes;
        }

        return $data;
    }
}
