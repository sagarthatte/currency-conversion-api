<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}

/* Sample live endpoint response for reference
{
  "success": true,
  "terms": "https://currencylayer.com/terms",
  "privacy": "https://currencylayer.com/privacy",
  "timestamp": 1712821623,
  "source": "USD",
  "quotes": {
    "USDAUD": 1.532778,
    "USDINR": 83.36935,
    "USDNZD": 1.670305,
    "USDAED": 3.672245
  }
}
*/