<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pet;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppointmentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_appointment()
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create(['user_id' => $user->id]);
        $service = Service::factory()->create();

        $appointmentData = [
            'pet_id' => $pet->id,
            'service_id' => $service->id,
            'scheduled_at' => now()->addDays(1)->toDateTimeString(),
            'total_price' => 50.00,
            'notes' => 'Regular grooming session'
        ];

        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/appointments', $appointmentData);

        $response->assertStatus(201)
                ->assertJsonFragment(['status' => 'pending']);

        $this->assertDatabaseHas('appointments', [
            'user_id' => $user->id,
            'pet_id' => $pet->id,
            'service_id' => $service->id
        ]);
    }

    public function test_guest_cannot_create_appointment()
    {
        $pet = Pet::factory()->create();
        $service = Service::factory()->create();

        $appointmentData = [
            'pet_id' => $pet->id,
            'service_id' => $service->id,
            'scheduled_at' => now()->addDays(1)->toDateTimeString(),
            'total_price' => 50.00
        ];

        $response = $this->postJson('/api/appointments', $appointmentData);

        $response->assertStatus(401);
    }

    public function test_user_can_view_their_appointments()
    {
        $user = User::factory()->create();
        $appointments = Appointment::factory()->count(2)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/appointments');

        $response->assertStatus(200)
                ->assertJsonCount(2);
    }
}