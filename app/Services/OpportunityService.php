<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Business;
use App\Models\Opportunity;
use App\Models\OpportunityApplication;
use App\Models\OpportunityMatch;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class OpportunityService
{
    /** @return array{eligible: bool, score: float, results: array<string, bool>} */
    public function evaluate(Business $business, Opportunity $opportunity, ?Carbon $at = null): array
    {
        $at ??= Carbon::now();
        /** @var array<string, mixed> $criteria */
        $criteria = is_array($opportunity->criteria) ? $opportunity->criteria : [];
        /** @var array<string, bool> $results */
        $results = [];
        foreach ($criteria as $key => $rule) {
            if (! is_array($rule) || ! array_key_exists('operator', $rule) || ! array_key_exists('value', $rule)) {
                throw ValidationException::withMessages(['criteria' => "Invalid criterion [{$key}]."]);
            }
            $actual = data_get(['stage' => $business->stage->value, 'profile' => $business->profile, 'context' => $business->context, 'preferences' => $business->preferences], $key);
            $results[$key] = $this->matches($actual, (string) $rule['operator'], $rule['value']);
        }
        $eligible = $opportunity->isCurrent($at) && ! in_array(false, $results, true);
        $score = $results === [] ? ($eligible ? 1.0 : 0.0) : round(count(array_filter($results)) / count($results), 3);

        return ['eligible' => $eligible, 'score' => $score, 'results' => $results];
    }

    public function match(Business $business, Opportunity $opportunity, ?User $actor = null): OpportunityMatch
    {
        abort_unless($actor === null || $actor->isAdmin() || $business->members()->whereKey($actor->id)->exists(), 403);
        $evaluation = $this->evaluate($business, $opportunity);
        if (! $evaluation['eligible']) {
            throw ValidationException::withMessages(['opportunity' => 'Business does not meet the opportunity criteria.']);
        }

        return OpportunityMatch::query()->updateOrCreate(
            ['business_id' => $business->id, 'opportunity_id' => $opportunity->id],
            ['score' => $evaluation['score'], 'criteria_results' => $evaluation['results'], 'matched_at' => Carbon::now()],
        );
    }

    public function apply(Business $business, Opportunity $opportunity, User $user): OpportunityApplication
    {
        abort_unless($user->isAdmin() || $business->members()->whereKey($user->id)->exists(), 403);
        $this->evaluate($business, $opportunity);

        return DB::transaction(fn (): OpportunityApplication => OpportunityApplication::query()->updateOrCreate(
            ['business_id' => $business->id, 'opportunity_id' => $opportunity->id],
            ['user_id' => $user->id, 'status' => 'planned'],
        ));
    }

    private function matches(mixed $actual, string $operator, mixed $expected): bool
    {
        return match ($operator) {
            'eq' => $actual === $expected,
            'neq' => $actual !== $expected,
            'in' => is_array($expected) && in_array($actual, $expected, true),
            'contains' => is_string($actual) && is_string($expected) && str_contains($actual, $expected),
            'gte' => is_numeric($actual) && is_numeric($expected) && (float) $actual >= (float) $expected,
            'lte' => is_numeric($actual) && is_numeric($expected) && (float) $actual <= (float) $expected,
            default => throw ValidationException::withMessages(['criteria' => "Unsupported operator [{$operator}]."]),
        };
    }
}
