<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('body');
            $table->string('visibility')->default('community');
            $table->string('status')->default('published');
            $table->timestamps();
        });
        Schema::create('peer_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('community_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->string('visibility')->default('community');
            $table->string('status')->default('open');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
        Schema::create('peer_review_responses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('peer_review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->boolean('accepted')->default(false);
            $table->timestamps();
            $table->unique(['peer_review_id', 'reviewer_id']);
        });
        Schema::create('moderation_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->string('reportable_type');
            $table->unsignedBigInteger('reportable_id');
            $table->string('reason');
            $table->string('status')->default('open');
            $table->text('resolution')->nullable();
            $table->timestamps();
            $table->index(['reportable_type', 'reportable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moderation_reports');
        Schema::dropIfExists('peer_review_responses');
        Schema::dropIfExists('peer_reviews');
        Schema::dropIfExists('community_posts');
    }
};