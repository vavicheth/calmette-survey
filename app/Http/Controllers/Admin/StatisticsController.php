<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Exports\SurveyResponsesExport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class StatisticsController extends Controller
{
    public function index(Request $request, Survey $survey)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Convert to Carbon instances and set time bounds
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        $survey->load(['questions.answers' => function($query) use ($startDateTime, $endDateTime) {
            $query->whereHas('surveyResponse', function($q) use ($startDateTime, $endDateTime) {
                $q->whereBetween('created_at', [$startDateTime, $endDateTime]);
            });
        }]);

        $responses = $survey->responses()
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->with('answers.question')
            ->latest()
            ->get();

        $totalResponses = $responses->count();

        // Calculate statistics for each question
        $statistics = [];
        $chartData = [];

        foreach ($survey->questions as $question) {
            $answers = $question->answers()
                ->whereHas('surveyResponse', function($q) use ($startDateTime, $endDateTime) {
                    $q->whereBetween('created_at', [$startDateTime, $endDateTime]);
                })
                ->get();

            $stats = [
                'question' => $question->question_text,
                'type' => $question->question_type,
                'total_responses' => $answers->count(),
                'question_id' => $question->id,
            ];

            if (in_array($question->question_type, ['radio', 'checkbox', 'select'])) {
                $answerCounts = $answers->groupBy('answer_text')->map->count();
                $stats['breakdown'] = $answerCounts->toArray();

                // Prepare chart data
                $chartData[] = [
                    'id' => $question->id,
                    'question' => $question->question_text,
                    'labels' => array_keys($answerCounts->toArray()),
                    'data' => array_values($answerCounts->toArray()),
                ];
            } elseif ($question->question_type === 'rating') {
                $average = $answers->avg(function($answer) {
                    return (int) $answer->answer_text;
                });
                $stats['average'] = round($average, 2);
                $stats['breakdown'] = $answers->groupBy('answer_text')->map->count()->toArray();

                // Prepare chart data
                $ratingData = $answers->groupBy('answer_text')->map->count()->toArray();
                ksort($ratingData);
                $chartData[] = [
                    'id' => $question->id,
                    'question' => $question->question_text,
                    'labels' => array_keys($ratingData),
                    'data' => array_values($ratingData),
                ];
            } else {
                $stats['responses'] = $answers->take(10)->pluck('answer_text')->toArray();
            }

            $statistics[] = $stats;
        }

        return view('admin.statistics.index', compact('survey', 'statistics', 'totalResponses', 'startDate', 'endDate', 'responses', 'chartData'));
    }

    public function exportExcel(Request $request, Survey $survey)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Convert to Carbon instances and set time bounds
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        return Excel::download(
            new SurveyResponsesExport($survey, $startDateTime, $endDateTime),
            'survey-' . $survey->id . '-responses-' . date('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf(Request $request, Survey $survey)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Convert to Carbon instances and set time bounds
        $startDateTime = Carbon::parse($startDate)->startOfDay();
        $endDateTime = Carbon::parse($endDate)->endOfDay();

        $responses = $survey->responses()
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->with('answers.question')
            ->get();

        $totalResponses = $responses->count();

        // Calculate statistics
        $statistics = [];
        foreach ($survey->questions as $question) {
            $answers = $question->answers()
                ->whereHas('surveyResponse', function($q) use ($startDateTime, $endDateTime) {
                    $q->whereBetween('created_at', [$startDateTime, $endDateTime]);
                })
                ->get();

            $stats = [
                'question' => $question->question_text,
                'type' => $question->question_type,
                'total_responses' => $answers->count(),
            ];

            if (in_array($question->question_type, ['radio', 'checkbox', 'select'])) {
                $answerCounts = $answers->groupBy('answer_text')->map->count();
                $stats['breakdown'] = $answerCounts->toArray();
            } elseif ($question->question_type === 'rating') {
                $average = $answers->avg(function($answer) {
                    return (int) $answer->answer_text;
                });
                $stats['average'] = round($average, 2);
                $stats['breakdown'] = $answers->groupBy('answer_text')->map->count()->toArray();
            }

            $statistics[] = $stats;
        }

        $pdf = Pdf::loadView('admin.statistics.pdf', compact('survey', 'statistics', 'totalResponses', 'startDate', 'endDate', 'responses'));

        return $pdf->download('survey-' . $survey->id . '-report-' . date('Y-m-d') . '.pdf');
    }
}
