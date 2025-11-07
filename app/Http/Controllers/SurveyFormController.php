<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;

class SurveyFormController extends Controller
{
    public function index()
    {
        // Get the default survey or the first active survey
        $survey = Survey::active()
            ->where('is_default', true)
            ->with(['questions' => function($query) {
                $query->orderBy('order');
            }])
            ->first();

        if (!$survey) {
            $survey = Survey::active()
                ->with(['questions' => function($query) {
                    $query->orderBy('order');
                }])
                ->first();
        }

        if (!$survey) {
            return view('survey.no-survey');
        }

        return view('survey.form', compact('survey'));
    }

    public function show(Survey $survey)
    {
        if (!$survey->is_active) {
            abort(404);
        }

        $survey->load(['questions' => function($query) {
            $query->orderBy('order');
        }]);

        return view('survey.form', compact('survey'));
    }

    public function submit(Request $request, Survey $survey)
    {
        if (!$survey->is_active) {
            return back()->with('error', 'This survey is no longer active.');
        }

        // Create survey response
        $surveyResponse = SurveyResponse::create([
            'survey_id' => $survey->id,
            'respondent_ip' => $request->ip(),
            'respondent_user_agent' => $request->userAgent(),
        ]);

        // Save answers
        foreach ($request->input('answers', []) as $questionId => $answerText) {
            if (is_array($answerText)) {
                $answerText = implode(', ', $answerText);
            }

            $surveyResponse->answers()->create([
                'question_id' => $questionId,
                'answer_text' => $answerText,
            ]);
        }

        return redirect()->route('survey.thank-you');
    }

    public function thankYou()
    {
        return view('survey.thank-you');
    }
}
