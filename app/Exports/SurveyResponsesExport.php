<?php

namespace App\Exports;

use App\Models\Survey;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SurveyResponsesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $survey;
    protected $startDate;
    protected $endDate;

    public function __construct(Survey $survey, $startDate, $endDate)
    {
        $this->survey = $survey;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->survey->responses()
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->with('answers.question')
            ->get();
    }

    public function headings(): array
    {
        $headings = ['Response ID', 'Submitted At', 'IP Address'];

        foreach ($this->survey->questions()->orderBy('order')->get() as $question) {
            $headings[] = $question->question_text;
        }

        return $headings;
    }

    public function map($response): array
    {
        $row = [
            $response->id,
            $response->created_at->format('Y-m-d H:i:s'),
            $response->respondent_ip,
        ];

        foreach ($this->survey->questions()->orderBy('order')->get() as $question) {
            $answer = $response->answers->firstWhere('question_id', $question->id);
            $row[] = $answer ? $answer->answer_text : 'N/A';
        }

        return $row;
    }
}
