<?php

namespace App\Handlers;

use App\Queries\GetUserAppointmentsQuery;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Collection;

/**
 * CQRS Query Handler
 *
 * Responsável por executar a query de listagem de agendamentos.
 * Otimizado para leitura com eager loading.
 */
class GetUserAppointmentsHandler
{
    /**
     * Handle the query
     */
    public function handle(GetUserAppointmentsQuery $query): Collection
    {
        $builder = Appointment::with(['user', 'pet', 'service'])
            ->where($query->getFilters());

        // Aplica ordenação
        $orderBy = $query->getOrderBy();
        $builder->orderBy($orderBy['column'], $orderBy['direction']);

        // Aplica limite se especificado
        if ($query->limit !== null) {
            $builder->limit($query->limit);
        }

        return $builder->get();
    }

    /**
     * Get appointments count for a user
     */
    public function count(GetUserAppointmentsQuery $query): int
    {
        return Appointment::where($query->getFilters())->count();
    }
}
