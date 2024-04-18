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
		$report = $report->storeRequest($user, [
			'type' => 'historical',
			'requestData' => json_encode($validatedData)
		]);

		return response()->json([
			'message' => 'Report request created successfully.',
			'report_id' => $report->id
		]);
	}

	public function getReportStatus (Request $request, $reportId) {
		$report = Report::where('id', $reportId)
			->where('user_id', Auth::user()->id)
		;
	}
}
