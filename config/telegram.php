<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Token
    |--------------------------------------------------------------------------
    |
    | The bot token from BotFather on Telegram.
    |
    */
    'bot_token' => env('TELEGRAM_BOT_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Telegram Chat ID
    |--------------------------------------------------------------------------
    |
    | The chat ID where notifications will be sent.
    | Can be a channel, group, or user ID.
    |
    */
    'chat_id' => env('TELEGRAM_CHAT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Enable Telegram Notifications
    |--------------------------------------------------------------------------
    |
    | Enable or disable Telegram notifications globally.
    |
    */
    'enabled' => env('TELEGRAM_ENABLED', true),
];
