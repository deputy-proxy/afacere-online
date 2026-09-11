<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Business;
use App\Models\Opportunity;
use App\Models\OpportunityApplication;
use App\Models\OpportunityEvent;
use App\Models\OpportunityMatch;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class OpportunityMatchingService
{
    /** @return Builder<Opportunity> */
    public function current(): Builder
    {
        return Opportunity::query()
            ->where('is_published', true)
            ->where(function (Builder $query): void {
                $query->whereNull('valid_from')->orWhere('valid_from', '<=', now());
            })
            ->where(function (Builder $query): void {
                $query->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            })
            ->with('type')
            ->orderBy('valid_until');
    }

    /** @return Collection<int, OpportunityMatch> */
    public function matches(Business $business, User $user): Collection
    {
        $this->authorizeBusiness($business, $user);

        return $this->current()->get()->map(function (Opportunity $opportunity) use ($business, $user): OpportunityMatch {
            $match = $this->evaluate($opportunity, $business);
            if ((float) $match->getAttribute('score') === 1.0) {
                $this->recordOncePerDay($opportunity, $business, $user, 'matched', ['match_id' => $match->id]);
            }

            return $match;
        })->filter(fn (OpportunityMatch $match): bool => $match->getAttribute('score') !== null && (float) $match->getAttribute('score') > 0)->sortByDesc(fn (OpportunityMatch $match): float => (float) $match->getAttribute('score'))->values();
    }

    public function viewed(Opportunity $opportunity, Business $business, User $user): void
    {
        $this->authorizeBusiness($business, $user);
        if ($opportunity->isCurrent()) {
            $this->recordOncePerDay($opportunity, $business, $user, 'viewed');
        }
    }

    public function apply(Opportunity $opportunity, Business $business, User $user): OpportunityApplication
    {
        $this->authorizeBusiness($business, $user);
        if (! $opportunity->isCurrent()) {
            throw ValidationException::withMessages(['opportunity' => 'This opportunity is no longer current.']);
        }

        $match = $this->evaluate($opportunity, $business);
        if ((float) $match->getAttribute('score') < 1.0) {
            throw ValidationException::withMessages(['opportunity' => 'The business does not meet all required eligibility criteria.']);
        }

        $application = DB::transaction(function () use ($opportunity, $business, $user, $match): OpportunityApplication {
            return OpportunityApplication::query()->firstOrCreate(
                ['opportunity_id' => $opportunity->id, 'business_id' => $business->id, 'user_id' => $user->id],
                ['context' => ['match_id' => $match->id], 'submitted_at' => now()],
            );
        });
        $this->recordOncePerDay($opportunity, $business, $user, 'applied', ['application_id' => $application->id]);

        return $application;
    }

    private function evaluate(Opportunity $opportunity, Business $business): OpportunityMatch
    {
        $criteria = $opportunity->getAttribute('criteria');
        $criteria = is_array($criteria) ? $criteria : [];
        $results = [];
        $passed = 0;

        foreach ($criteria as $key => $rule) {
            if (! is_string($key) || ! is_array($rule)) {
                continue;
            }

            $actual = data_get($business->getAttribute('profile') ?? [], $key, data_get($business->getAttribute('context') ?? [], $key));
            $result = $this->check($actual, $rule);
            $results[$key] = ['passed' => $result, 'expected' => $rule, 'actual' => $actual];
            $passed += $result ? 1 : 0;
        }

        $score = $criteria === [] ? 1.0 : $passed / count($criteria);

        return OpportunityMatch::query()->updateOrCreate(
            ['business_id' => $business->id, 'opportunity_id' => $opportunity->id],
            ['score' => $score, 'criteria_results' => $results, 'matched_at' => now()],
        );
    }

    /** @param array<string, mixed> $rule */
    private function check(mixed $actual, array $rule): bool
    {
        $operator = $rule['operator'] ?? 'equals';
        $expected = $rule['value'] ?? null;

        return match ($operator) {
            'equals' => $actual === $expected,
            'not_equals' => $actual !== $expected,
            'in' => is_array($expected) && in_array($actual, $expected, true),
            'contains' => is_string($actual) && is_string($expected) && str_contains(strtolower($actual), strtolower($expected)),
            'min' => is_numeric($actual) && is_numeric($expected) && (float) $actual >= (float) $expected,
            'max' => is_numeric($actual) && is_numeric($expected) && (float) $actual <= (float) $expected,
            default => false,
        };
    }

    /** @param array<string, mixed> $context */
    private function recordOncePerDay(Opportunity $opportunity, Business $business, User $user, string $type, array $context = []): void
    {
        $exists = OpportunityEvent::query()
            ->where('opportunity_id', $opportunity->id)
            ->where('business_id', $business->id)
            ->where('user_id', $user->id)
            ->where('event_type', $type)
            ->whereDate('occurred_at', today())
            ->exists();

        if (! $exists) {
            OpportunityEvent::query()->create([
                'opportunity_id' => $opportunity->id,
                'business_id' => $business->id,
                'user_id' => $user->id,
                'event_type' => $type,
                'context' => $context,
                'occurred_at' => now(),
            ]);
        }
    }

    private function authorizeBusiness(Business $business, User $user): void
    {
        abort_unless($business->members()->whereKey($user->id)->exists(), 403);
    }
}
