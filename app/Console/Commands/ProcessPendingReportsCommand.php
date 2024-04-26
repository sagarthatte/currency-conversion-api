<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Report;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CurrencyController;
use App\Console\Commands\DateTime;

class ProcessPendingReportsCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'process:pending-reports';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'This command will retrieve all requested reports with pending status, complete them and store response in the records.';

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle()
	{
		$this->info('Processing pending reports...');

		$pendingReports = Report::where('status', 'pending')->get();

		foreach ($pendingReports as $report) {
			$currencyController = new CurrencyController();
			$responseData = $currencyController->getHistorical($report);
			$data = $responseData->getData(true);
			$rates = $data['rates'] ?? null;

			$filteredData = null;
			if ($rates) {
				if ($report->interval === 'weekly') {
					$this->info('Processing a 6 month, weekly report...');
					$filteredData = $this->processWeeklyData($rates, $report->currency);
				} else if ($report->interval === 'monthly') {
					$this->info('Processing a 1 year, monthly report...');
					$filteredData = $this->processMonthlyData($rates, $report->currency);
				} else {
					$this->info('Processing 1 month, daily...');
					$filteredData = $this->processDailyData($rates, $report->currency);
				}
			}

			$report->response_data = json_encode($filteredData);
			$report->status = 'completed';
			$report->response_at = now();
			$report->save();
		}
		
	}

	public function processDailyData($rates, $targetCurrency) {
		$filteredData = [];
		$currencyKey = 'USD' . $targetCurrency;
		// Simply getting daily data in intended format
		foreach($rates as $date => $rateData) {
			$filteredData[$date] = $rateData[$currencyKey];
		}
		return $filteredData;

	}

	public function processWeeklyData($rates, $targetCurrency) {
		$weeklyAverages = [];
		$currencyKey = 'USD' . $targetCurrency;
		// First add up data for each week into an array with count for # of records for each
		foreach($rates as $date => $rateData) {
			$formattedDate = new \DateTime($date);
			$weeklyKey = $formattedDate->format('W') . '-' . $formattedDate->format('Y');
			if (array_key_exists($weeklyKey, $weeklyAverages)) {
				$weeklyAverages[$weeklyKey]['sum'] += $rateData[$currencyKey];
				$weeklyAverages[$weeklyKey]['count'] += 1;
			} else {
				$weeklyAverages[$weeklyKey] = [
					'sum' => $rateData[$currencyKey],
					'count' => 1
				];
			}
		}

		$filteredData = [];
		foreach($weeklyAverages as $key => $data) {
			$weekDetails = explode ("-", $key);
			$filteredData['Week ' . $weekDetails[0] . ', ' . $weekDetails[1]] = $data['sum'] / $data['count'];
		}
		return $filteredData;
	}

	public function processMonthlyData($rates, $targetCurrency) {
		$monthlyAverages = [];
		$currencyKey = 'USD' . $targetCurrency;
		// Again add up data for eachmonth with count for data from each month
		foreach($rates as $date => $rateData) {
			$formattedDate = new \DateTime($date);
			$monthlyKey = $formattedDate->format('F') . ' ' . $formattedDate->format('Y');
			if (array_key_exists($monthlyKey, $monthlyAverages)) {
				$monthlyAverages[$monthlyKey]['sum'] += $rateData[$currencyKey];
				$monthlyAverages[$monthlyKey]['count'] += 1;
			} else {
				$monthlyAverages[$monthlyKey] = [
					'sum' => $rateData[$currencyKey],
					'count' => 1
				];
			}
		}

		$filteredData = [];
		foreach ($monthlyAverages as $key => $data) {
			$filteredData[$key] = $data['sum'] / $data['count'];
		}
		return $filteredData;
	}

	
}
