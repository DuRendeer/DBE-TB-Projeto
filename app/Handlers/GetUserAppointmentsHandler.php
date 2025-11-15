<?php

namespace App\Handlers;

use App\Queries\GetUserAppointmentsQuery;
use App\Models\Appointment;
use Illuminate\Database\Eloquent\Collection;

class GetUserAppointmentsHandler
{
    public function handle(GetUserAppointmentsQuery $query): Collection
    {
        $builder = Appointment::with(['user', 'pet', 'service'])
            ->where('user_id', $query->userId);

        if ($query->status !== null) {
            $builder->where('status', $query->status);
        }

        $builder->orderBy($query->orderBy ?? 'scheduled_at', $query->orderDirection);

        if ($query->limit !== null) {
            $builder->limit($query->limit);
        }

        return $builder->get();
    }

    public function count(GetUserAppointmentsQuery $query): int
    {
        $builder = Appointment::where('user_id', $query->userId);

        if ($query->status !== null) {
            $builder->where('status', $query->status);
        }

        return $builder->count();
    }
}
