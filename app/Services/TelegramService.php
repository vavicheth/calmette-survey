<?php

namespace App\Services;

use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $telegram;
    protected $chatId;
    protected $enabled;

    public function __construct()
    {
        $this->enabled = config('telegram.enabled', false);

        if ($this->enabled && config('telegram.bot_token')) {
            $this->telegram = new BotApi(config('telegram.bot_token'));
            $this->chatId = config('telegram.chat_id');
        }
    }

    /**
     * Send a message to Telegram
     */
    public function sendMessage(string $message, string $parseMode = 'HTML'): bool
    {
        if (!$this->enabled || !$this->telegram || !$this->chatId) {
            Log::info('Telegram notification skipped (disabled or not configured)');
            return false;
        }

        try {
            $this->telegram->sendMessage(
                $this->chatId,
                $message,
                $parseMode,
                false,
                null,
                null,
                false
            );

            Log::info('Telegram message sent successfully');
            return true;
        } catch (Exception $e) {
            Log::error('Telegram send message error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send survey submission notification with full Q&A
     */
    public function sendSurveySubmissionNotification(string $surveyTitle, int $responseId, array $questionsAndAnswers = []): bool
    {
        $message = "🎯 <b>New Calmette Survey Submission</b>\n\n";
        $message .= "📋 Survey: <b>{$surveyTitle}</b>\n";
        $message .= "🆔 Response ID: #{$responseId}\n";
        $message .= "⏰ Time: " . now()->format('Y-m-d H:i:s');

        if (!empty($questionsAndAnswers)) {
            $message .= "\n\n" . str_repeat("─", 30) . "\n";
            $message .= "<b>📝 Responses:</b>\n\n";

            foreach ($questionsAndAnswers as $index => $qa) {
                $questionNumber = $index + 1;
                $questionText = $this->escapeHtml($qa['question']);
                $answerText = $this->escapeHtml($qa['answer']);
                $questionType = $qa['type'] ?? 'text';

                // Add emoji based on question type
                $emoji = match($questionType) {
                    'radio', 'select' => '🔘',
                    'checkbox' => '☑️',
                    'rating' => '⭐',
                    'textarea' => '📄',
                    default => '✏️'
                };

                $message .= "{$emoji} <b>Q{$questionNumber}:</b> {$questionText}\n";
                $message .= "💬 <b>Answer:</b> {$answerText}\n\n";
            }
        }

        // Telegram message limit is 4096 characters
        if (strlen($message) > 4000) {
            // If message is too long, send it in parts
            return $this->sendLongMessage($message);
        }

        return $this->sendMessage($message);
    }

    /**
     * Send long messages by splitting them
     */
    private function sendLongMessage(string $message): bool
    {
        $maxLength = 4000;
        $parts = [];

        // Split by questions to avoid cutting in the middle
        $lines = explode("\n\n", $message);
        $currentPart = '';

        foreach ($lines as $line) {
            if (strlen($currentPart . $line . "\n\n") > $maxLength) {
                if (!empty($currentPart)) {
                    $parts[] = trim($currentPart);
                    $currentPart = $line . "\n\n";
                } else {
                    // Single line is too long, force split
                    $parts[] = substr($line, 0, $maxLength);
                    $currentPart = substr($line, $maxLength) . "\n\n";
                }
            } else {
                $currentPart .= $line . "\n\n";
            }
        }

        if (!empty($currentPart)) {
            $parts[] = trim($currentPart);
        }

        // Send all parts
        $allSuccess = true;
        foreach ($parts as $index => $part) {
            if ($index > 0) {
                $part = "📄 <b>Continued...</b>\n\n" . $part;
            }
            $success = $this->sendMessage($part);
            $allSuccess = $allSuccess && $success;

            // Small delay between messages
            if ($index < count($parts) - 1) {
                usleep(500000); // 0.5 second delay
            }
        }

        return $allSuccess;
    }

    /**
     * Escape HTML special characters for Telegram
     */
    private function escapeHtml(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Send monthly report
     */
    public function sendMonthlyReport(array $reportData): bool
    {
        $message = "📊 <b>Monthly Survey Report</b>\n\n";
        $message .= "📅 Period: {$reportData['month']}\n\n";
        $message .= "📈 <b>Statistics:</b>\n";
        $message .= "• Total Responses: {$reportData['total_responses']}\n";
        $message .= "• Active Surveys: {$reportData['active_surveys']}\n";
        $message .= "• Total Surveys: {$reportData['total_surveys']}\n\n";

        if (!empty($reportData['top_surveys'])) {
            $message .= "🏆 <b>Top 5 Surveys:</b>\n";
            foreach ($reportData['top_surveys'] as $index => $survey) {
                $message .= ($index + 1) . ". {$survey['title']} ({$survey['responses']} responses)\n";
            }
        }

        return $this->sendMessage($message);
    }

    /**
     * Check if Telegram is enabled and configured
     */
    public function isEnabled(): bool
    {
        return $this->enabled && $this->telegram && $this->chatId;
    }
}
