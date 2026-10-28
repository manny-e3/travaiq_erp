<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CloseExpiredJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:close-expired-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close job postings that have passed their closing date and auto-reject low scoring applications';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\JobPostingService $jobPostingService)
    {
        $expiredJobs = \App\Models\JobPosting::where('status', '!=', 'Closed')
            ->whereNotNull('closing_date')
            ->whereDate('closing_date', '<', \Carbon\Carbon::today())
            ->get();

        $count = 0;
        foreach ($expiredJobs as $job) {
            $jobPostingService->updatePosting($job, ['status' => 'Closed']);
            $count++;
        }

        $this->info("Closed {$count} expired job posting(s).");
    }
}
