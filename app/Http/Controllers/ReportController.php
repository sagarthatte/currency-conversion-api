<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Report;

class ReportController extends Controller
{
	public function storeRequest (Request $request)
	{
		$validatedData = $request->validate([
			'range' => 'required|in:one_year,six_months,one_month',
			'currencies' => 'required|string'
		]);

		$user = Auth::user();

		$report = new Report();

		$interval = '';
		switch ($validatedData['range']) {
			case 'one_month':
				$interval = 'daily';
				break;
			case 'six_months':
				$interval = 'weekly';
				break;
			case 'one_year':
				$interval = 'monthly';
				break;
			default:
				$interval = 'daily';
				break;
		}

		$report = $report->storeRequest($user, [
			'type' => 'historical',
			'range' => $validatedData['range'],
			'interval' => $interval,
			'currency' => $validatedData['currencies']
		]);

		return response()->json([
			'success' => true,
			'message' => 'Report request created successfully.',
			'report_id' => $report->id
		]);
	}

	// Fetch all reports for authenticated user
	public function getReportsList () {

		$reports = Report::where('user_id', Auth::user()->id)->get();

		return response()->json($reports);
	}

	// public function getReportStatus (Request $request, $reportId) {
	// 	$report = Report::where('id', $reportId)
	// 		->where('user_id', Auth::user()->id)
	// 	;
	// }
}
