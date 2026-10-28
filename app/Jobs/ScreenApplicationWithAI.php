<?php

namespace App\Jobs;

use App\Models\JobApplication;
use App\Services\AiScreeningService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScreenApplicationWithAI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 60;

    public function __construct(protected JobApplication $application)
    {
    }

    public function handle(AiScreeningService $aiService): void
    {
        Log::info('AI Screening: Starting for application', ['id' => $this->application->id]);

        $result = $aiService->screen($this->application);

        $this->application->update([
            'ai_score'          => $result['score'],
            'ai_recommendation' => $result['recommendation'],
            'ai_summary'        => $result['summary'],
        ]);

        Log::info('AI Screening: Completed', [
            'id'             => $this->application->id,
            'score'          => $result['score'],
            'recommendation' => $result['recommendation'],
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('AI Screening Job failed permanently', [
            'application_id' => $this->application->id,
            'error'          => $exception->getMessage(),
        ]);

        // Mark as needing manual review
        $this->application->update([
            'ai_recommendation' => 'Review',
            'ai_summary'        => 'AI screening could not be completed. Please review manually.',
        ]);
    }
}
