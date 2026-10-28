<?php

namespace App\Services;

use App\Models\JobOffer;
use App\Models\JobApplication;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\JobOfferMail;

class JobOfferService
{
    public function generateOffer(JobApplication $application, array $data): JobOffer
    {
        // Update application status
        $application->update(['status' => 'Offered']);

        // Generate PDF
        $pdf = Pdf::loadView('admin.hr.recruitment.offer_pdf', [
            'application' => $application,
            'salary' => $data['salary_offer'],
            'startDate' => $data['expected_start_date'],
            'contractDuration' => $data['contract_duration_months'] ?? null,
        ]);

        $filename = 'offer_' . $application->id . '_' . time() . '.pdf';
        $directory = app()->environment('testing') ? public_path('testing_offer_letter') : public_path('offer_letter');
        
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        file_put_contents($directory . '/' . $filename, $pdf->output());

        $data['job_application_id'] = $application->id;
        $data['document_path'] = $filename;
        $data['status'] = 'Pending';

        $jobOffer = JobOffer::create($data);

        // Send Offer Email with attached PDF
        Mail::to($application->email)->send(new JobOfferMail($application));

        return $jobOffer;
    }
}
