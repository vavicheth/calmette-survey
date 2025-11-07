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
     * Send survey submission notification
     */
    public function sendSurveySubmissionNotification(string $surveyTitle, int $responseId): bool
    {
        $message = "🎯 <b>New Survey Submission</b>\n\n";
        $message .= "📋 Survey: <b>{$surveyTitle}</b>\n";
        $message .= "🆔 Response ID: #{$responseId}\n";
        $message .= "⏰ Time: " . now()->format('Y-m-d H:i:s');

        return $this->sendMessage($message);
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
