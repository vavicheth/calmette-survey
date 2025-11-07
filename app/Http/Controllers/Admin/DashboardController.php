<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\Question;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $totalSurveys = Survey::count();
        $activeSurveys = Survey::where('is_active', true)->count();
        $totalQuestions = Question::count();
        $totalResponses = SurveyResponse::count();

        // Get responses for the last 30 days
        $last30Days = Carbon::now()->subDays(30);
        $recentResponses = SurveyResponse::where('created_at', '>=', $last30Days)->count();

        // Get daily responses for chart (last 14 days)
        $dailyResponses = [];
        $dates = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dates[] = $date->format('d/m');
            $count = SurveyResponse::whereDate('created_at', $date)->count();
            $dailyResponses[] = $count;
        }

        // Get most popular surveys
        $popularSurveys = Survey::withCount('responses')
            ->orderBy('responses_count', 'desc')
            ->take(5)
            ->get();

        // Get recent responses
        $latestResponses = SurveyResponse::with('survey')
            ->latest()
            ->take(10)
            ->get();

        // Survey response rate (responses per survey)
        $avgResponsesPerSurvey = $totalSurveys > 0 ? round($totalResponses / $totalSurveys, 1) : 0;

        return view('admin.dashboard.index', compact(
            'totalSurveys',
            'activeSurveys',
            'totalQuestions',
            'totalResponses',
            'recentResponses',
            'dailyResponses',
            'dates',
            'popularSurveys',
            'latestResponses',
            'avgResponsesPerSurvey'
        ));
    }
}
