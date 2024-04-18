<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;
use GuzzleHttp\Client;

class CurrencyController extends Controller
{
	public function convert(Request $request) {
		$accessKey = env('CURRENCYLAYER_ACCESS_KEY');
		$base = $request->get('base'); // Fetch base currency
		$target = $request->get('target'); // fetch target currency(ies)

		// Request URL generate
		$url = "http://api.currencylayer.com/live?access_key=$accessKey&currencies=$target&source=$base";
		
		$client = new Client();
		$response = $client->request('GET', $url);

		// If response is OK
		if ($response->getStatusCode() === 200) {
			$data = json_decode($response->getBody(), true);

			if (isset($data['success']) && $data['success'] === true) {
				return response()->json([
					'success' => true,
					'rates' => $data['quotes']
				]);
			} else {
				return response()->json([
					'success' => false,
					'message' => $data['error']['info']
				], 400);
			}
		} else {
			// Handle other status code scenarios
			return response()->json([
				'success' => false,
				'message' => 'Error fetching currency data'
			], 500);
		}
	}

	// Controller method to fetch all available currencies
	public function index()
    {
        $currencies = Currency::all(); // Fetch all currencies
        return response()->json($currencies);
    }

	// Test scheduling funtion
	public function historical() {
		$testInput = [1, 2, 3];
		var_dump($testInput);
	}

}