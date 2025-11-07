<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index()
    {
        $surveys = Survey::withCount(['questions', 'responses'])->latest()->get();
        return view('admin.surveys.index', compact('surveys'));
    }

    public function create()
    {
        return view('admin.surveys.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        // If setting as default, unset other defaults
        if ($request->is_default) {
            Survey::where('is_default', true)->update(['is_default' => false]);
        }

        Survey::create($validated);

        return redirect()->route('admin.surveys.index')
            ->with('success', 'Survey created successfully!');
    }

    public function show(Survey $survey)
    {
        $survey->load(['questions' => function($query) {
            $query->orderBy('order');
        }]);

        return view('admin.surveys.show', compact('survey'));
    }

    public function edit(Survey $survey)
    {
        $survey->load(['questions' => function($query) {
            $query->orderBy('order');
        }]);

        return view('admin.surveys.edit', compact('survey'));
    }

    public function update(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        // If setting as default, unset other defaults
        if ($request->is_default && !$survey->is_default) {
            Survey::where('is_default', true)->update(['is_default' => false]);
        }

        $survey->update($validated);

        return redirect()->route('admin.surveys.index')
            ->with('success', 'Survey updated successfully!');
    }

    public function destroy(Survey $survey)
    {
        $survey->delete();

        return redirect()->route('admin.surveys.index')
            ->with('success', 'Survey deleted successfully!');
    }

    public function toggleStatus(Survey $survey)
    {
        $survey->update(['is_active' => !$survey->is_active]);

        return back()->with('success', 'Survey status updated!');
    }

    public function setDefault(Survey $survey)
    {
        Survey::where('is_default', true)->update(['is_default' => false]);
        $survey->update(['is_default' => true]);

        return back()->with('success', 'Default survey updated!');
    }
}
