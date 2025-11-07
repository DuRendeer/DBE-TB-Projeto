<?php

namespace App\Http\Controllers;

use App\Commands\CreateAppointmentCommand;
use App\Commands\UpdateAppointmentStatusCommand;
use App\Queries\GetUserAppointmentsQuery;
use App\Handlers\CreateAppointmentHandler;
use App\Handlers\UpdateAppointmentStatusHandler;
use App\Handlers\GetUserAppointmentsHandler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Refactored Appointment Controller usando CQRS
 *
 * Aplicação dos Princípios SOLID e CQRS:
 *
 * 1. Single Responsibility: Controller apenas coordena,
 *    handlers executam a lógica
 *
 * 2. Command Query Responsibility Segregation:
 *    - Commands para operações de escrita
 *    - Queries para operações de leitura
 *
 * 3. Dependency Injection: Handlers injetados no construtor
 */
class AppointmentControllerRefactored extends Controller
{
    /**
     * Dependency Injection via Constructor
     */
    public function __construct(
        private readonly CreateAppointmentHandler $createHandler,
        private readonly UpdateAppointmentStatusHandler $updateHandler,
        private readonly GetUserAppointmentsHandler $queryHandler
    ) {
    }

    /**
     * List user appointments (CQRS Query)
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|string|in:pending,confirmed,in_progress,completed,cancelled',
            'limit' => 'sometimes|integer|min:1|max:100'
        ]);

        // Cria Query object
        $query = new GetUserAppointmentsQuery(
            userId: $request->user()->id,
            status: $validated['status'] ?? null,
            limit: $validated['limit'] ?? null
        );

        // Executa Query através do Handler
        $appointments = $this->queryHandler->handle($query);

        return response()->json($appointments);
    }

    /**
     * Create appointment (CQRS Command)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'service_id' => 'required|exists:services,id',
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string',
            'total_price' => 'required|numeric|min:0'
        ]);

        // Cria Command object
        $command = new CreateAppointmentCommand(
            userId: $request->user()->id,
            petId: $validated['pet_id'],
            serviceId: $validated['service_id'],
            scheduledAt: $validated['scheduled_at'],
            totalPrice: $validated['total_price'],
            notes: $validated['notes'] ?? null
        );

        try {
            // Executa Command através do Handler
            $appointment = $this->createHandler->handle($command);

            return response()->json($appointment, 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Show appointment (CQRS Query)
     */
    public function show(Request $request, int $id): JsonResponse
    {
        // Poderia usar um GetAppointmentByIdQuery aqui
        $query = new GetUserAppointmentsQuery(
            userId: $request->user()->id
        );

        $appointments = $this->queryHandler->handle($query);
        $appointment = $appointments->firstWhere('id', $id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        return response()->json($appointment);
    }

    /**
     * Update appointment status (CQRS Command)
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,in_progress,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        // Cria Command object
        $command = new UpdateAppointmentStatusCommand(
            appointmentId: $id,
            status: $validated['status'],
            notes: $validated['notes'] ?? null
        );

        try {
            // Executa Command através do Handler
            $appointment = $this->updateHandler->handle($command);

            return response()->json($appointment);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }
    }

    /**
     * Count user appointments
     */
    public function count(Request $request): JsonResponse
    {
        $query = new GetUserAppointmentsQuery(
            userId: $request->user()->id
        );

        $count = $this->queryHandler->count($query);

        return response()->json(['count' => $count]);
    }
}
