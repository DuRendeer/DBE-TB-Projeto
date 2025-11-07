<?php

namespace App\Repositories;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repository Pattern - Appointment Repository Implementation
 */
class AppointmentRepository implements AppointmentRepositoryInterface
{
    public function findById(int $id): ?Appointment
    {
        return Appointment::with(['user', 'pet', 'service'])->find($id);
    }

    public function findByUser(int $userId): Collection
    {
        return Appointment::with(['user', 'pet', 'service'])
            ->where('user_id', $userId)
            ->orderBy('scheduled_at', 'desc')
            ->get();
    }

    public function findByStatus(string $status): Collection
    {
        return Appointment::with(['user', 'pet', 'service'])
            ->where('status', $status)
            ->get();
    }

    public function findUpcoming(): Collection
    {
        return Appointment::with(['user', 'pet', 'service'])
            ->where('scheduled_at', '>', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('scheduled_at', 'asc')
            ->get();
    }

    public function create(array $data): Appointment
    {
        $appointment = Appointment::create($data);
        return $this->findById($appointment->id);
    }

    public function update(int $id, array $data): Appointment
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update($data);
        return $this->findById($id);
    }

    public function delete(int $id): bool
    {
        $appointment = Appointment::findOrFail($id);
        return $appointment->delete();
    }

    public function countByUser(int $userId): int
    {
        return Appointment::where('user_id', $userId)->count();
    }
}
