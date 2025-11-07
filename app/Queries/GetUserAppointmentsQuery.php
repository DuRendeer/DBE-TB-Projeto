<?php

namespace App\Queries;

/**
 * CQRS Query - Represents a read operation (Get User Appointments)
 *
 * Queries são DTOs que representam requisições de leitura de dados.
 *
 * Benefícios:
 * - Encapsula os critérios de busca
 * - Facilita cache e otimização de queries
 * - Separa a lógica de leitura da lógica de escrita
 */
class GetUserAppointmentsQuery
{
    public function __construct(
        public readonly int $userId,
        public readonly ?string $status = null,
        public readonly ?string $orderBy = 'scheduled_at',
        public readonly string $orderDirection = 'desc',
        public readonly ?int $limit = null
    ) {
    }

    /**
     * Get query filters as array
     */
    public function getFilters(): array
    {
        $filters = ['user_id' => $this->userId];

        if ($this->status !== null) {
            $filters['status'] = $this->status;
        }

        return $filters;
    }

    /**
     * Get order by configuration
     */
    public function getOrderBy(): array
    {
        return [
            'column' => $this->orderBy ?? 'scheduled_at',
            'direction' => $this->orderDirection
        ];
    }
}
