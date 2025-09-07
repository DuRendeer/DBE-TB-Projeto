<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pet;
use App\Models\Appointment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_have_multiple_pets()
    {
        $user = User::factory()->create();
        $pets = Pet::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->pets);
        $this->assertTrue($user->pets->contains($pets->first()));
    }

    public function test_user_password_is_hashed()
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->assertTrue(password_verify('password123', $user->password));
        $this->assertNotEquals('password123', $user->password);
    }

    public function test_user_can_have_appointments()
    {
        $user = User::factory()->create();
        $appointments = Appointment::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->appointments);
        $this->assertTrue($user->appointments->contains($appointments->first()));
    }
}