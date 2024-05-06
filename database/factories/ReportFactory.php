<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Report;
//use Database\Factories\UserFactory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition()
	{
		$user = User::factory()->create();
		return [
            'type' => 'historical',
			'user_id' => $user->id,
            'status' => 'pending',
            'request_at' => now(),
			'response_at' => null,
            'currency' => Str::random(3),
			'range' => 'monthly',
			'interval' => 'daily'
        ];
	}
}
