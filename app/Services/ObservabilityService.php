<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;

final class ObservabilityService
{
    public function record(string $event, array $context = [], string $level = 'info'): void
    {
        $payload = [
            'event' => $event,
            'environment' => (string) config('app.env'),
            'deployment_id' => (string) config('performance.deployment_id', ''),
            'context' => $this->sanitize($context),
        ];

        if (function_exists('request')) {
            $requestId = request()->header('X-Request-ID');
            if (is_string($requestId) && $requestId !== '') {
                $payload['request_id'] = $requestId;
            }
        }

        match ($level) {
            'debug' => Log::debug($event, $payload),
            'notice' => Log::notice($event, $payload),
            'warning' => Log::warning($event, $payload),
            'error' => Log::error($event, $payload),
            'critical' => Log::critical($event, $payload),
            default => Log::info($event, $payload),
        };
    }

    private function sanitize(mixed $value, ?string $key = null): mixed
    {
        if ($key !== null && $this->isSensitiveKey($key)) {
            return '[REDACTED]';
        }

        if (is_array($value)) {
            $result = [];
            foreach ($value as $childKey => $childValue) {
                $result[(string) $childKey] = $this->sanitize($childValue, (string) $childKey);
            }
            return $result;
        }

        if (is_object($value)) {
            return '[REDACTED_OBJECT]';
        }

        return $value;
    }

    private function isSensitiveKey(string $key): bool
    {
        return preg_match('/password|secret|token|authorization|credential|card|cvv|cvc|signature|payload|raw[_-]?body/i', $key) === 1;
    }
}
