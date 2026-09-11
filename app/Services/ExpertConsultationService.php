<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Business;
use App\Models\Consultation;
use App\Models\ConsultationShare;
use App\Models\Expert;
use App\Models\ExpertAvailability;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ExpertConsultationService
{
    /** @return Collection<int, Expert> */
    public function discover(?string $term = null): Collection
    {
        $query = Expert::query()->where('verification_status', 'verified');
        if ($term !== null && $term !== '') {
            $query->where(function (Builder $query) use ($term): void {
                $query->where('title', 'like', "%{$term}%")->orWhere('bio', 'like', "%{$term}%");
            });
        }

        return $query->orderBy('title')->get();
    }

    public function request(Business $business, User $user, Expert $expert, ExpertAvailability $availability, ?string $note = null): Consultation
    {
        abort_unless($business->members()->whereKey($user->id)->exists(), 403);
        if (! $expert->isVerified() || ! $availability->is_bookable || $availability->expert_id !== $expert->id || CarbonImmutable::parse($availability->starts_at)->isPast()) {
            throw ValidationException::withMessages(['availability' => 'The selected expert availability is not bookable.']);
        }

        return DB::transaction(fn (): Consultation => Consultation::query()->create([
            'business_id' => $business->id,
            'expert_id' => $expert->id,
            'availability_id' => $availability->id,
            'status' => 'requested',
            'request_note' => $note,
            'scheduled_at' => $availability->starts_at,
        ]));
    }

    /** @param array<int, string> $fields */
    public function shareBusinessContext(Consultation $consultation, User $user, string $shareType, array $fields = []): void
    {
        abort_unless($consultation->business->members()->whereKey($user->id)->exists(), 403);
        ConsultationShare::query()->updateOrCreate(
            ['consultation_id' => $consultation->id, 'share_type' => $shareType],
            ['fields' => $fields, 'consented_at' => now(), 'consented_by' => $user->id],
        );
    }
}
