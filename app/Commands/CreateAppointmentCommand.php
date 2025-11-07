<?php

namespace App\Commands;

/**
 * CQRS Command - Represents a write operation (Create Appointment)
 *
 * Commands são DTOs (Data Transfer Objects) que representam
 * a intenção de modificar o estado do sistema.
 *
 * Benefícios do CQRS:
 * - Separação clara entre operações de leitura e escrita
 * - Facilita validação e auditoria de comandos
 * - Permite otimizações específicas para cada tipo de operação
 */
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

    /**
     * Validate command data
     */
    public function validate(): array
    {
        $errors = [];

        if ($this->totalPrice < 0) {
            $errors[] = 'Total price must be positive';
        }

        if (strtotime($this->scheduledAt) <= time()) {
            $errors[] = 'Scheduled date must be in the future';
        }

        return $errors;
    }

    /**
     * Convert to array for database insertion
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'pet_id' => $this->petId,
            'service_id' => $this->serviceId,
            'scheduled_at' => $this->scheduledAt,
            'total_price' => $this->totalPrice,
            'notes' => $this->notes,
            'status' => 'pending'
        ];
    }
}
