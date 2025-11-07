<?php

namespace App\Console\Commands;

use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Services\TelegramService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendMonthlyReportToTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:monthly-report {--month= : Month to generate report for (YYYY-MM format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send monthly survey report to Telegram channel';

    /**
     * Execute the console command.
     */
    public function handle(TelegramService $telegram)
    {
        if (!$telegram->isEnabled()) {
            $this->error('Telegram is not enabled or configured properly.');
            return Command::FAILURE;
        }

        // Determine the month to report on
        $month = $this->option('month')
            ? Carbon::createFromFormat('Y-m', $this->option('month'))
            : Carbon::now()->subMonth();

        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        $this->info("Generating report for: {$month->format('F Y')}");

        // Collect statistics
        $totalResponses = SurveyResponse::whereBetween('created_at', [$startDate, $endDate])->count();
        $activeSurveys = Survey::where('is_active', true)->count();
        $totalSurveys = Survey::count();

        // Get top 5 surveys by response count
        $topSurveys = Survey::withCount(['responses' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
            ->having('responses_count', '>', 0)
            ->orderBy('responses_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($survey) {
                return [
                    'title' => $survey->title,
                    'responses' => $survey->responses_count,
                ];
            })
            ->toArray();

        $reportData = [
            'month' => $month->format('F Y'),
            'total_responses' => $totalResponses,
            'active_surveys' => $activeSurveys,
            'total_surveys' => $totalSurveys,
            'top_surveys' => $topSurveys,
        ];

        // Send to Telegram
        $success = $telegram->sendMonthlyReport($reportData);

        if ($success) {
            $this->info('Monthly report sent to Telegram successfully!');
            return Command::SUCCESS;
        } else {
            $this->error('Failed to send monthly report to Telegram.');
            return Command::FAILURE;
        }
    }
}
