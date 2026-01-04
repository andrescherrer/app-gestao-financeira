<?php

namespace App\Infrastructure\Services;

use Illuminate\Support\Facades\Log;

final class StructuredLogger
{
    public function logTransactionCreated(string $transactionId, string $userId, array $context = []): void
    {
        Log::channel('application')->info('Transaction created', array_merge([
            'event' => 'transaction.created',
            'transaction_id' => $transactionId,
            'user_id' => $userId,
            'timestamp' => now()->toIso8601String(),
        ], $context));
    }

    public function logSecurityEvent(string $event, string $userId, array $context = []): void
    {
        Log::channel('security')->info('Security event', array_merge([
            'event' => $event,
            'user_id' => $userId,
            'timestamp' => now()->toIso8601String(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ], $context));
    }

    public function logPerformance(string $operation, float $duration, array $context = []): void
    {
        Log::channel('performance')->debug('Performance metric', array_merge([
            'operation' => $operation,
            'duration_ms' => round($duration * 1000, 2),
            'timestamp' => now()->toIso8601String(),
        ], $context));
    }

    public function logError(string $message, \Throwable $exception, array $context = []): void
    {
        Log::channel('application')->error($message, array_merge([
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => now()->toIso8601String(),
        ], $context));
    }
}
