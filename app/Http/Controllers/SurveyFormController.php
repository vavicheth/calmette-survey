<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Services\TelegramService;
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

    public function submit(Request $request, Survey $survey, TelegramService $telegram)
    {
        if (!$survey->is_active) {
            return back()->with('error', 'This survey is no longer active.');
        }

        // Load questions to prepare Q&A data
        $survey->load(['questions' => function($query) {
            $query->orderBy('order');
        }]);

        // Create survey response
        $surveyResponse = SurveyResponse::create([
            'survey_id' => $survey->id,
            'respondent_ip' => $request->ip(),
            'respondent_user_agent' => $request->userAgent(),
        ]);

        // Prepare questions and answers for Telegram
        $questionsAndAnswers = [];

        // Save answers and build Q&A array
        foreach ($request->input('answers', []) as $questionId => $answerText) {
            $question = $survey->questions->firstWhere('id', $questionId);

            if ($question) {
                // Store original answer (array or string)
                $answerForDb = is_array($answerText) ? implode(', ', $answerText) : $answerText;

                $surveyResponse->answers()->create([
                    'question_id' => $questionId,
                    'answer_text' => $answerForDb,
                ]);

                // Add to Q&A array for Telegram
                $questionsAndAnswers[] = [
                    'question' => $question->question_text,
                    'answer' => $answerForDb ?: '(No answer provided)',
                    'type' => $question->question_type,
                ];
            }
        }

        // Send Telegram notification with full Q&A
        $telegram->sendSurveySubmissionNotification(
            $survey->title,
            $surveyResponse->id,
            $questionsAndAnswers
        );

        return redirect()->route('survey.thank-you');
    }

    public function thankYou()
    {
        return view('survey.thank-you');
    }
}
