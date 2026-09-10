<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Business;
use App\Models\BusinessInvitation;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class BusinessMembershipService
{
    /** @return array{invitation: BusinessInvitation, token: string} */
    public function invite(Business $business, User $actor, string $email, string $role = 'member'): array
    {
        $this->authorizeManager($business, $actor);
        $email = Str::lower(trim($email));

        if (! in_array($role, ['admin', 'member', 'viewer'], true)) {
            throw ValidationException::withMessages(['role' => 'The selected role is invalid.']);
        }

        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if ($user !== null && $business->members()->whereKey($user->id)->exists()) {
            throw ValidationException::withMessages(['email' => 'This user is already a business member.']);
        }

        $token = Str::random(64);
        $invitation = DB::transaction(function () use ($business, $actor, $email, $role, $token): BusinessInvitation {
            BusinessInvitation::query()
                ->where('business_id', $business->id)
                ->where('email', $email)
                ->whereNull('accepted_at')
                ->whereNull('revoked_at')
                ->update(['revoked_at' => Carbon::now()]);

            $invitation = BusinessInvitation::query()->create([
                'business_id' => $business->id,
                'invited_by' => $actor->id,
                'email' => $email,
                'role' => $role,
                'token_hash' => hash('sha256', $token),
                'expires_at' => Carbon::now()->addDays(7),
            ]);

            AuditLog::create([
                'actor_id' => $actor->id,
                'subject_type' => BusinessInvitation::class,
                'subject_id' => $invitation->id,
                'action' => 'business_membership_invited',
                'context' => ['business_id' => $business->id, 'email' => $email, 'role' => $role],
                'occurred_at' => Carbon::now(),
            ]);

            return $invitation;
        });

        return ['invitation' => $invitation, 'token' => $token];
    }

    public function accept(string $token, User $user): BusinessInvitation
    {
        $invitation = BusinessInvitation::query()
            ->where('token_hash', hash('sha256', $token))
            ->first();

        if ($invitation === null || ! $invitation->isPending()) {
            throw ValidationException::withMessages(['token' => 'The invitation is invalid or expired.']);
        }

        if (Str::lower($user->email) !== Str::lower($invitation->email)) {
            throw ValidationException::withMessages(['token' => 'This invitation belongs to another user.']);
        }

        DB::transaction(function () use ($invitation, $user): void {
            $invitation->business()->firstOrFail()->members()->syncWithoutDetaching([
                $user->id => ['role' => $invitation->role, 'joined_at' => Carbon::now()],
            ]);
            $invitation->update(['accepted_at' => Carbon::now()]);

            AuditLog::create([
                'actor_id' => $user->id,
                'subject_type' => BusinessInvitation::class,
                'subject_id' => $invitation->id,
                'action' => 'business_membership_accepted',
                'context' => ['business_id' => $invitation->business_id, 'role' => $invitation->role],
                'occurred_at' => Carbon::now(),
            ]);
        });

        return $invitation->fresh() ?? $invitation;
    }

    public function revoke(BusinessInvitation $invitation, User $actor): void
    {
        $business = $invitation->business()->firstOrFail();
        $this->authorizeManager($business, $actor);

        if (! $invitation->isPending()) {
            throw ValidationException::withMessages(['invitation' => 'Only pending invitations can be revoked.']);
        }

        $invitation->update(['revoked_at' => Carbon::now()]);

        AuditLog::create([
            'actor_id' => $actor->id,
            'subject_type' => BusinessInvitation::class,
            'subject_id' => $invitation->id,
            'action' => 'business_membership_invitation_revoked',
            'context' => ['business_id' => $business->id, 'email' => $invitation->email],
            'occurred_at' => Carbon::now(),
        ]);
    }

    private function authorizeManager(Business $business, User $actor): void
    {
        abort_unless(
            $actor->isAdmin() || $business->members()
                ->whereKey($actor->id)
                ->wherePivotIn('role', ['owner', 'admin'])
                ->exists(),
            403
        );
    }
}
