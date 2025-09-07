<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Appointment::with(['user', 'pet', 'service']);

        if ($request->user()) {
            $query->where('user_id', $request->user()->id);
        }

        $appointments = $query->orderBy('scheduled_at', 'desc')->get();
        return response()->json($appointments);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'service_id' => 'required|exists:services,id',
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string',
            'total_price' => 'required|numeric|min:0'
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status'] = 'pending';

        $appointment = Appointment::create($validated);
        $appointment->load(['user', 'pet', 'service']);

        return response()->json($appointment, 201);
    }

    public function show(Appointment $appointment): JsonResponse
    {
        $appointment->load(['user', 'pet', 'service']);
        return response()->json($appointment);
    }

    public function update(Request $request, Appointment $appointment): JsonResponse
    {
        $validated = $request->validate([
            'pet_id' => 'sometimes|exists:pets,id',
            'service_id' => 'sometimes|exists:services,id',
            'scheduled_at' => 'sometimes|date|after:now',
            'status' => 'sometimes|in:pending,confirmed,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'total_price' => 'sometimes|numeric|min:0'
        ]);

        $appointment->update($validated);
        $appointment->load(['user', 'pet', 'service']);

        return response()->json($appointment);
    }

    public function destroy(Appointment $appointment): JsonResponse
    {
        $appointment->delete();
        return response()->json(['message' => 'Appointment deleted successfully']);
    }
}