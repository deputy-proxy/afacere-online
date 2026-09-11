<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BusinessGoalStatus;
use App\Enums\BusinessMetricAggregation;
use App\Enums\BusinessStage;
use App\Models\Business;
use App\Models\BusinessGoal;
use App\Models\BusinessMetric;
use App\Models\BusinessMetricValue;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

final class BusinessGoalsMetricsService
{
    /**
     * @param array{type: string, title: string, description?: string|null, target: int|float|string, unit?: string|null, deadline?: DateTimeInterface|string|null, status?: BusinessGoalStatus|string, stage?: BusinessStage|string|null} $attributes
     */
    public function createGoal(Business $business, User $actor, array $attributes): BusinessGoal
    {
        Gate::forUser($actor)->authorize('update', $business);

        $target = $attributes['target'] ?? null;
        if (is_numeric($target)) {
            $status = $this->goalStatus($attributes['status'] ?? BusinessGoalStatus::Active);
            $stage = $this->businessStage($attributes['stage'] ?? null);

            return $business->goals()->create([
                'type' => $attributes['type'],
                'title' => $attributes['title'],
                'description' => $attributes['description'] ?? null,
                'target' => $target,
                'unit' => $attributes['unit'] ?? null,
                'deadline' => $attributes['deadline'] ?? null,
                'status' => $status,
                'stage' => $stage,
            ]);
        }

        throw ValidationException::withMessages(['target' => 'A measurable goal requires a numeric target.']);
    }

    /** @param array{status?: BusinessGoalStatus|string, target?: int|float|string, deadline?: DateTimeInterface|string|null, title?: string, description?: string|null, unit?: string|null, stage?: BusinessStage|string|null} $attributes */
    public function updateGoal(BusinessGoal $goal, User $actor, array $attributes): BusinessGoal
    {
        $business = $goal->business;
        Gate::forUser($actor)->authorize('update', $business);

        if (array_key_exists('target', $attributes) && is_numeric($attributes['target']) === false) {
            throw ValidationException::withMessages(['target' => 'The goal target must be numeric.']);
        }

        $updates = $attributes;
        if (array_key_exists('status', $updates)) {
            $updates['status'] = $this->goalStatus($updates['status']);
        }
        if (array_key_exists('stage', $updates)) {
            $updates['stage'] = $this->businessStage($updates['stage']);
        }

        $goal->update($updates);

        return $goal->fresh() ?? $goal;
    }

    /**
     * @param array{key: string, name: string, unit?: string|null, aggregation?: BusinessMetricAggregation|string, stage?: BusinessStage|string|null} $attributes
     */
    public function createMetric(Business $business, User $actor, array $attributes): BusinessMetric
    {
        Gate::forUser($actor)->authorize('update', $business);

        $aggregation = $this->metricAggregation($attributes['aggregation'] ?? BusinessMetricAggregation::Latest);
        $stage = $this->businessStage($attributes['stage'] ?? null);

        return $business->metrics()->create([
            'key' => $attributes['key'],
            'name' => $attributes['name'],
            'unit' => $attributes['unit'] ?? null,
            'aggregation' => $aggregation,
            'stage' => $stage,
        ]);
    }

    public function recordMetricValue(BusinessMetric $metric, User $actor, int|float|string $value, DateTimeInterface $measuredAt, array $context = []): BusinessMetricValue
    {
        Gate::forUser($actor)->authorize('update', $metric->business);

        if (is_numeric($value) === false) {
            throw ValidationException::withMessages(['value' => 'Metric values must be numeric.']);
        }

        return $metric->values()->create([
            'value' => $value,
            'measured_at' => $measuredAt,
            'context' => $context,
        ]);
    }

    /** @return list<BusinessMetricValue> */
    public function metricHistory(BusinessMetric $metric, User $actor): array
    {
        Gate::forUser($actor)->authorize('view', $metric->business);

        return $metric->values()->orderBy('measured_at')->orderBy('id')->get()->all();
    }

    private function goalStatus(BusinessGoalStatus|string $status): BusinessGoalStatus
    {
        if ($status instanceof BusinessGoalStatus) {
            return $status;
        }

        return BusinessGoalStatus::tryFrom($status) ?? throw ValidationException::withMessages([
            'status' => 'The goal status is invalid.',
        ]);
    }

    private function metricAggregation(BusinessMetricAggregation|string $aggregation): BusinessMetricAggregation
    {
        if ($aggregation instanceof BusinessMetricAggregation) {
            return $aggregation;
        }

        return BusinessMetricAggregation::tryFrom($aggregation) ?? throw ValidationException::withMessages([
            'aggregation' => 'The metric aggregation is invalid.',
        ]);
    }

    private function businessStage(BusinessStage|string|null $stage): ?BusinessStage
    {
        if ($stage === null || $stage instanceof BusinessStage) {
            return $stage;
        }

        return BusinessStage::tryFrom($stage) ?? throw ValidationException::withMessages([
            'stage' => 'The business lifecycle stage is invalid.',
        ]);
    }
}
