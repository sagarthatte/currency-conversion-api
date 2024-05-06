<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Http; // Mock HTTP
use App\Http\Controllers\CurrencyController;
use Illuminate\Http\Request;
use Illuminate\Testing\TestResponse;

class CurrencyLayerTest extends TestCase
{

	/**
	 * Test the getHistorical rates endpoint
	 * 
	 * @return void
	 */

	 public function test_getHistorical_returns_data()
	 {
		$requestParams = [
			'start_date' => '2024-05-01',
			'end_date' => '2024-05-05',
			'currencies' => 'AUD'
		];

		$response = $this->getJson('/historical-reports', $requestParams);

		$content = $response->getContent();
		$response->assertJson(json_decode($content, true));

	 }
	/**
	 * Test the get live rates endpoint
	 *
	 * @return void
	 */
	
	 public function test_getLiveRates_returns_data()
	 {

		$requestParams = [
			"base" => "USD",
			"target" => "AMD,ANG,AOA"
		];

		$response = $this->getJson('/live-rates', $requestParams);

		$content = $response->getContent();

		$response->assertJson(json_decode($content, true));

	}
}