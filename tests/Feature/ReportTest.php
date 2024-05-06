<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;
use App\Models\User;
use Tests\TestCase;

class ReportTest extends TestCase
{
	use RefreshDatabase, WithFaker;
	
	/** @test */
	public function test_can_create_and_store_report()
	{

		$user = User::factory()->create();
		$data = [
			'type' => 'historical',
			'status' => 'pending',
			'user_id' => $user->id,
			'range' => 'one_year',
			'interval' => 'monthly',
			'currency' => 'NZD',
			'request_at' => now(),
			'response_at' => null
		];

		$report = Report:: create($data);

		$this->assertDatabaseHas('reports', $data);
		$this->assertEquals($data['type'], 'historical');
		$this->assertEquals($data['currency'], 'NZD');
		$this->assertEquals($data['status'], 'pending');

	}

	public function test_can_fetch_report_by_id()
	{
		$report = Report::factory()->create();
		$fetchedReport = Report::find($report->id);

		$this->assertNotNull($fetchedReport);
	}
}
