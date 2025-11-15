<?php

namespace App\Queries;

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
}
