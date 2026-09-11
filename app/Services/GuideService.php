<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Guide;
use App\Models\GuideRevision;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

final class GuideService
{
    public function publish(Guide $guide, User $actor): Guide
    {
        Gate::forUser($actor)->authorize('publish', $guide);

        if ($guide->getAttribute('status') === 'published') {
            return $guide;
        }

        if ($guide->sections()->count() === 0) {
            throw ValidationException::withMessages(['guide' => 'A guide must contain at least one section before publication.']);
        }

        return DB::transaction(function () use ($guide, $actor): Guide {
            $guide->update([
                'status' => 'published',
                'published_at' => now(),
            ]);

            $this->snapshot($guide, $actor);

            return $guide->fresh() ?? $guide;
        });
    }

    public function startRevision(Guide $guide, User $actor): Guide
    {
        Gate::forUser($actor)->authorize('update', $guide);

        if ($guide->getAttribute('status') !== 'published') {
            throw ValidationException::withMessages(['guide' => 'Only published guides can start a new revision.']);
        }

        return DB::transaction(function () use ($guide): Guide {
            $guide->update([
                'status' => 'draft',
                'version' => ((int) $guide->getAttribute('version')) + 1,
                'published_at' => null,
            ]);

            return $guide->fresh() ?? $guide;
        });
    }

    public function publishRevision(Guide $guide, User $actor): Guide
    {
        return $this->publish($guide, $actor);
    }

    private function snapshot(Guide $guide, User $actor): GuideRevision
    {
        $content = [
            'title' => $guide->getAttribute('title'),
            'description' => $guide->getAttribute('description'),
            'sections' => $guide->sections()->with('steps')->get()->map(
                fn ($section): array => [
                    'position' => $section->getAttribute('position'),
                    'title' => $section->getAttribute('title'),
                    'content' => $section->getAttribute('content'),
                    'steps' => $section->steps->map(fn ($step): array => [
                        'position' => $step->getAttribute('position'),
                        'title' => $step->getAttribute('title'),
                        'content' => $step->getAttribute('content'),
                        'resources' => $step->getAttribute('resources'),
                    ])->values()->all(),
                ],
            )->values()->all(),
        ];

        return $guide->revisions()->create([
            'version' => $guide->getAttribute('version'),
            'created_by' => $actor->id,
            'content' => $content,
            'created_at' => now(),
        ]);
    }
}
