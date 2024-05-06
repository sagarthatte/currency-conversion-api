<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;
use App\Models\Report;
use GuzzleHttp\Client;

class CurrencyController extends Controller
{
	public function getHistorical(Report $report) {
		$accessKey = env('CURRENCYLAYER_ACCESS_KEY');
		
		// Defaulting to certain values if not provided.
		$rangeVal = isset($report->range) ? $report->range : 'one_month';
		$currencyVal = isset($report->currency) ? $report->currency : 'AUD';


		// Calculate start date (and end date) for API call
		$endDate = date('Y-m-d');
		$startDate = date('Y-m-d');
		if ($rangeVal == 'one_month') {
			$startDate = date('Y-m-d', strtotime("-1 month"));
		} else if ($rangeVal == 'six_months') {
			$startDate = date('Y-m-d', strtotime("-6 months"));
		} else if ($rangeVal == 'one_year') {
			$startDate = date('Y-m-d', strtotime("-1 year"));
		}

		// Create request URL
		$url = "http://api.currencylayer.com/timeframe?access_key=$accessKey&currencies=$currencyVal&start_date=$startDate&end_date=$endDate";

		$client = new Client();
		$response = $client->request('GET', $url);

		// Check if response is okay
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
				'message' => 'Error fetching requested report data'
			], 500);
		}
	}

	public function getLiveRates(Request $request) {
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
}