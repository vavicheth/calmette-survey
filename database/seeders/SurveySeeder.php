<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Survey;

class SurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default customer satisfaction survey
        $survey = Survey::create([
            'title' => 'Customer Satisfaction Survey',
            'description' => 'Help us improve our service by sharing your feedback.',
            'is_active' => true,
            'is_default' => true,
        ]);

        // Add questions to the survey
        $survey->questions()->createMany([
            [
                'question_text' => 'How satisfied are you with our service?',
                'question_type' => 'rating',
                'options' => null,
                'order' => 1,
                'is_required' => true,
            ],
            [
                'question_text' => 'Which of our services have you used?',
                'question_type' => 'checkbox',
                'options' => ['Customer Support', 'Technical Support', 'Sales', 'Billing'],
                'order' => 2,
                'is_required' => true,
            ],
            [
                'question_text' => 'How likely are you to recommend us to others?',
                'question_type' => 'radio',
                'options' => ['Very Likely', 'Likely', 'Neutral', 'Unlikely', 'Very Unlikely'],
                'order' => 3,
                'is_required' => true,
            ],
            [
                'question_text' => 'What do you like most about our service?',
                'question_type' => 'textarea',
                'options' => null,
                'order' => 4,
                'is_required' => false,
            ],
            [
                'question_text' => 'How can we improve?',
                'question_type' => 'textarea',
                'options' => null,
                'order' => 5,
                'is_required' => false,
            ],
        ]);
    }
}
