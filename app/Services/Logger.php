<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class Logger
{
    private static ?Logger $instance = null;

    // Private constructor prevents direct instantiation
    private function __construct() {}

    // Get the single instance
    public static function getInstance(): Logger
    {
        if (self::$instance === null) {
            self::$instance = new Logger();
        }
        return self::$instance;
    }

    // Example log methods
    public function info(string $message, array $context = []): void
    {
        Log::info($message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        Log::warning($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        Log::error($message, $context);
    }

    // You can add more log levels as needed
}
