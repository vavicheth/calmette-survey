<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function store(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:text,textarea,radio,checkbox,select,rating',
            'options' => 'nullable|array',
            'options.*' => 'string',
            'order' => 'integer',
            'is_required' => 'boolean',
        ]);

        $order = $validated['order'] ?? $survey->questions()->max('order') + 1;

        $survey->questions()->create([
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'options' => $validated['options'] ?? null,
            'order' => $order,
            'is_required' => $validated['is_required'] ?? false,
        ]);

        return back()->with('success', 'Question added successfully!');
    }

    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:text,textarea,radio,checkbox,select,rating',
            'options' => 'nullable|array',
            'options.*' => 'string',
            'order' => 'nullable|integer',
            'is_required' => 'nullable|boolean',
        ]);

        // Handle is_required checkbox (if not checked, it won't be in the request)
        $validated['is_required'] = $request->has('is_required') ? true : false;

        // Set options to null if not provided (for text, textarea, rating types)
        if (!isset($validated['options']) || empty($validated['options'])) {
            $validated['options'] = null;
        }

        $question->update($validated);

        return back()->with('success', 'Question updated successfully!');
    }

    public function destroy(Question $question)
    {
        $question->delete();

        return back()->with('success', 'Question deleted successfully!');
    }
}
