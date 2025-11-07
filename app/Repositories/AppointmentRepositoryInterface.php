<?php

namespace App\Repositories;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Collection;

/**
 * Repository Pattern - Appointment Repository Interface
 */
interface AppointmentRepositoryInterface
{
    public function findById(int $id): ?Appointment;

    public function findByUser(int $userId): Collection;

    public function findByStatus(string $status): Collection;

    public function findUpcoming(): Collection;

    public function create(array $data): Appointment;

    public function update(int $id, array $data): Appointment;

    public function delete(int $id): bool;

    public function countByUser(int $userId): int;
}
