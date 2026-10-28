<?php

namespace App\Services;

use App\Models\JobApplication;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiScreeningService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key') ?? '';
        $this->model  = config('services.gemini.model', 'gemini-3.8-flash');
    }

    protected function getApiUrl(): string
    {
        return "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    /**
     * Screen a job application CV against the job description.
     */
    public function screen(JobApplication $application): array
    {
        // 1. Extract text from the PDF resume
        $cvText = $this->extractPdfText($application);

        if (empty($cvText)) {
            return [
                'score'          => null,
                'recommendation' => 'Review',
                'summary'        => 'Could not extract text from the CV (possibly a scanned image PDF). Please review manually.',
            ];
        }

        // 2. Get the job description
        $jobTitle       = $application->jobPosting->title ?? 'Unknown Position';
        $jobDescription = $application->jobPosting->description ?? 'No description provided.';

        // 3. Build prompt
        $prompt = <<<PROMPT
You are an expert HR recruiter. Your job is to evaluate a candidate's CV against a job description and return a structured JSON response.

## Job Position: {$jobTitle}

## Job Description:
{$jobDescription}

## Candidate CV:
{$cvText}

---

Based on the above, evaluate the candidate and respond ONLY with a valid JSON object (no markdown, no extra text) in this exact format:
{
  "score": <integer from 0 to 100 representing how well the candidate matches the role>,
  "recommendation": "<one of: Shortlist, Review, Reject>",
  "summary": "<2-4 sentence summary explaining the score, highlighting key strengths and gaps>"
}

Scoring guide:
- 70-100: Strong match → Shortlist
- 40-69: Partial match → Review
- 0-39: Poor match → Reject
PROMPT;

        // 4. Call Gemini API
        try {
            $response = Http::withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                ]
            ])->timeout(60)->post("{$this->getApiUrl()}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature'      => 0.2,
                    'responseMimeType' => 'application/json',
                    'thinkingConfig'   => [
                        'thinkingBudget' => 0,
                    ],
                ]
            ]);

            if ($response->failed()) {
                Log::error('Gemini API error', ['status' => $response->status(), 'body' => $response->body()]);
                throw new \Exception('Gemini API returned error: ' . $response->status());
            }

            $text = $response->json('candidates.0.content.parts.0.text');

            // Strip any markdown code fences if Gemini wraps it
            $text = preg_replace('/```(?:json)?\s*/i', '', $text);
            $text = trim(str_replace('```', '', $text));

            $result = json_decode($text, true);

            if (!$result || !isset($result['score'], $result['recommendation'], $result['summary'])) {
                throw new \Exception('Unexpected Gemini response format: ' . $text);
            }

            return [
                'score'          => (int) $result['score'],
                'recommendation' => $result['recommendation'],
                'summary'        => $result['summary'],
            ];

        } catch (\Exception $e) {
            Log::error('AI Screening failed', ['application_id' => $application->id, 'error' => $e->getMessage()]);

            return [
                'score'          => null,
                'recommendation' => 'Review',
                'summary'        => 'AI screening failed. Please review this application manually. Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Extract plain text from a PDF resume using smalot/pdfparser.
     */
    protected function extractPdfText(JobApplication $application): string
    {
        if (empty($application->resume_path)) {
            return '';
        }

        $filePath = public_path('resumes/' . $application->resume_path);

        if (!file_exists($filePath)) {
            Log::warning('AI Screening: Resume file not found', ['path' => $filePath]);
            return '';
        }

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf    = $parser->parseFile($filePath);
            $text   = $pdf->getText();
            return substr(trim($text), 0, 8000); // Cap at 8k chars to stay within token limits
        } catch (\Exception $e) {
            Log::error('PDF parsing failed', ['path' => $filePath, 'error' => $e->getMessage()]);
            return '';
        }
    }
}
