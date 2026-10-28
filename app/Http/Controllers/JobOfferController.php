<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Services\JobOfferService;
use Illuminate\Http\Request;

class JobOfferController extends Controller
{
    protected $jobOfferService;

    public function __construct(JobOfferService $jobOfferService)
    {
        $this->jobOfferService = $jobOfferService;
    }

    public function store(Request $request, JobApplication $jobApplication)
    {
        $validated = $request->validate([
            'salary_offer' => 'required|numeric|min:0',
            'expected_start_date' => 'required|date',
            'contract_duration_months' => 'nullable|integer|min:1',
        ]);

        $this->jobOfferService->generateOffer($jobApplication, $validated);

        return back()->with('success', 'Job offer generated successfully.');
    }
}
