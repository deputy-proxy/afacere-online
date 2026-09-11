<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunity_matches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('opportunity_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 6, 3);
            $table->json('criteria_results');
            $table->timestamp('matched_at');
            $table->timestamps();
            $table->unique(['business_id', 'opportunity_id']);
        });

        Schema::create('opportunity_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('opportunity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->default('planned');
            $table->json('context')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['business_id', 'opportunity_id']);
        });

        Schema::create('monitor_thresholds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('metric_key', 80);
            $table->string('operator', 10);
            $table->decimal('threshold', 20, 6);
            $table->string('severity', 20)->default('warning');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('monitor_alerts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('monitor_threshold_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 50);
            $table->string('severity', 20);
            $table->text('message');
            $table->json('context')->nullable();
            $table->timestamp('triggered_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('monitor_summaries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->json('summary');
            $table->timestamps();
            $table->unique(['business_id', 'period_start', 'period_end']);
        });

        Schema::create('ai_prompt_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ai_prompt_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->text('template');
            $table->json('output_schema')->nullable();
            $table->boolean('active')->default(false);
            $table->timestamps();
            $table->unique(['ai_prompt_id', 'version']);
        });

        Schema::create('ai_recommendations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ai_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recommendation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 30)->default('suggested');
            $table->json('payload');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_feedback', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ai_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_usage_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ai_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('input_tokens')->default(0);
            $table->unsignedInteger('output_tokens')->default(0);
            $table->decimal('cost', 12, 6)->default(0);
            $table->timestamps();
        });

        Schema::create('domain_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 120);
            $table->string('subject_type', 120)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
            $table->index(['subject_type', 'subject_id']);
        });

        Schema::create('user_notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 120);
            $table->string('title');
            $table->text('body');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'read_at']);
        });

        Schema::create('analytics_conversions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->string('funnel', 80);
            $table->string('step', 80);
            $table->string('idempotency_key')->unique();
            $table->timestamp('occurred_at');
            $table->timestamps();
        });

        Schema::create('analytics_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 100);
            $table->date('period_start');
            $table->date('period_end');
            $table->json('payload');
            $table->timestamps();
            $table->unique(['key', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_snapshots');
        Schema::dropIfExists('analytics_conversions');
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('domain_events');
        Schema::dropIfExists('ai_usage_records');
        Schema::dropIfExists('ai_feedback');
        Schema::dropIfExists('ai_recommendations');
        Schema::dropIfExists('ai_prompt_versions');
        Schema::dropIfExists('monitor_summaries');
        Schema::dropIfExists('monitor_alerts');
        Schema::dropIfExists('monitor_thresholds');
        Schema::dropIfExists('opportunity_applications');
        Schema::dropIfExists('opportunity_matches');
    }
};
