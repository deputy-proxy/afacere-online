<?php

declare(strict_types=1);

use App\Models\CommunityPost;
use App\Models\User;
use App\Services\PeerReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('creates a controlled peer review request', function (): void {
    $user = User::factory()->create();
    $post = CommunityPost::query()->create(['user_id' => $user->id, 'title' => 'Question', 'body' => 'Need feedback', 'visibility' => 'community', 'status' => 'published']);
    $review = app(PeerReviewService::class)->request($user, $post, 'anonymized');

    expect($review->status)->toBe('open')->and($review->visibility)->toBe('anonymized');
});

it('prevents self-review and closed-review responses', function (): void {
    $user = User::factory()->create();
    $post = CommunityPost::query()->create(['user_id' => $user->id, 'title' => 'Question', 'body' => 'Need feedback', 'visibility' => 'community', 'status' => 'published']);
    $review = app(PeerReviewService::class)->request($user, $post, 'community');

    expect(fn (): mixed => app(PeerReviewService::class)->respond($review, $user, 'My own feedback'))->toThrow(ValidationException::class);
    $review->update(['status' => 'closed']);
    expect(fn (): mixed => app(PeerReviewService::class)->respond($review, User::factory()->create(), 'Feedback'))->toThrow(ValidationException::class);
});