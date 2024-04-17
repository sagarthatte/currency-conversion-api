<?php

namespace App\Console\Commands;

use Illuminate\HttpRequest;
use GuzzleHttp\Client;
use App\Models\Currency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class GetCurrenciesCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'command:get-currencies';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command to fetch list of available currencies from Currency Layer and store them in currencies table';

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle()
	{
		$apiKey = env('CURRENCYLAYER_ACCESS_KEY');
		$apiUrl = 'http://api.currencylayer.com/list?access_key=' . $apiKey;

		$client = new Client();
		$response = $client->get($apiUrl);
		
		if ($response->getStatusCode() === 200) {
			$data = json_decode($response->getBody(), true);
			$currencyData = $data['currencies'];
			
			// Extract only code and name for each currency
			$currencies = [];
			foreach ($currencyData as $key => $value) {
			  $currencies[] = [
				'code' => $key,
				'name' => $value,
			  ];
			}
		
			$this->storeCurrencyData($currencies);
		  } else {
			$this->error('API request failed: ' . $response->getStatusCode());
		  }

		//return Command::SUCCESS;
	}

	private function storeCurrencyData($currencies)
	{
		foreach ($currencies as $currencyData) {
			$currency = new Currency;
			$currency->code = $currencyData['code'];
			$currency->name = $currencyData['name'];
			$currency->save();
		}
	}
}
