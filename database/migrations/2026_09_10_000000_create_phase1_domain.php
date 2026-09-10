<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('bio')->nullable();
            $table->string('phone')->nullable();
            $table->string('locale', 10)->default('ro');
            $table->timestamps();
        });

        Schema::create('businesses', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->string('stage', 40)->default('idea');
            $table->json('profile')->nullable();
            $table->json('preferences')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('stage');
        });

        Schema::create('business_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 30)->default('member');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->unique(['business_id', 'user_id']);
            $table->index(['user_id', 'role']);
        });

        Schema::create('business_stage_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('from_stage', 40)->nullable();
            $table->string('to_stage', 40);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reason')->nullable();
            $table->timestamp('changed_at');
            $table->timestamps();
            $table->index(['business_id', 'changed_at']);
        });

        Schema::create('business_goals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('target', 18, 4)->nullable();
            $table->string('unit', 30)->nullable();
            $table->date('deadline')->nullable();
            $table->string('status', 30)->default('active');
            $table->timestamps();
            $table->index(['business_id', 'status']);
        });

        Schema::create('business_metrics', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('key', 80);
            $table->string('name');
            $table->string('unit', 30)->nullable();
            $table->string('aggregation', 30)->default('latest');
            $table->timestamps();
            $table->unique(['business_id', 'key']);
        });

        Schema::create('business_metric_values', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_metric_id')->constrained()->cascadeOnDelete();
            $table->decimal('value', 20, 6);
            $table->timestamp('measured_at');
            $table->json('context')->nullable();
            $table->timestamps();
            $table->index(['business_metric_id', 'measured_at']);
        });

        Schema::create('evaluation_versions', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 80)->unique();
            $table->string('name');
            $table->string('version', 30);
            $table->boolean('is_active')->default(false);
            $table->json('definition');
            $table->timestamps();
        });

        Schema::create('evaluations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('evaluation_version_id')->constrained()->restrictOnDelete();
            $table->string('status', 30)->default('draft');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['business_id', 'status']);
        });

        Schema::create('evaluation_answers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('evaluation_id')->constrained()->cascadeOnDelete();
            $table->string('question_key', 100);
            $table->json('value')->nullable();
            $table->timestamps();
            $table->unique(['evaluation_id', 'question_key']);
        });

        Schema::create('evaluation_findings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('evaluation_id')->constrained()->cascadeOnDelete();
            $table->string('dimension', 80);
            $table->string('severity', 30)->default('medium');
            $table->text('title');
            $table->text('description')->nullable();
            $table->decimal('score', 6, 2)->nullable();
            $table->json('context')->nullable();
            $table->timestamps();
        });

        Schema::create('recommendations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('evaluation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('evaluation_finding_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source_type', 40)->default('system');
            $table->string('status', 30)->default('suggested');
            $table->unsignedInteger('priority')->default(0);
            $table->string('title');
            $table->text('reason')->nullable();
            $table->text('recommended_action')->nullable();
            $table->text('expected_outcome')->nullable();
            $table->decimal('confidence', 5, 4)->nullable();
            $table->json('context')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            $table->index(['business_id', 'status', 'priority']);
        });

        Schema::create('priorities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recommendation_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('position');
            $table->string('title');
            $table->text('reason')->nullable();
            $table->string('status', 30)->default('active');
            $table->timestamps();
            $table->index(['business_id', 'position']);
        });

        Schema::create('action_plans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version')->default(1);
            $table->string('status', 30)->default('active');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['business_id', 'version']);
        });

        Schema::create('actions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('action_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('priority_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('recommendation_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('position');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 30)->default('recommended');
            $table->text('resolution_reason')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['action_plan_id', 'position']);
        });

        Schema::create('action_outcomes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('action_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('summary');
            $table->json('evidence')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
        });

        Schema::create('guides', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 30)->default('draft');
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('guide_sections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->string('title');
            $table->text('content')->nullable();
            $table->timestamps();
            $table->unique(['guide_id', 'position']);
        });

        Schema::create('guide_steps', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('guide_section_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->string('title');
            $table->text('content')->nullable();
            $table->json('resources')->nullable();
            $table->timestamps();
            $table->unique(['guide_section_id', 'position']);
        });

        Schema::create('opportunity_types', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('opportunities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('opportunity_type_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('criteria')->nullable();
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->index(['is_published', 'valid_until']);
        });

        Schema::create('business_opportunities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('opportunity_id')->constrained()->cascadeOnDelete();
            $table->string('status', 30)->default('matched');
            $table->decimal('match_score', 6, 3)->nullable();
            $table->json('match_context')->nullable();
            $table->timestamps();
            $table->unique(['business_id', 'opportunity_id']);
        });

        Schema::create('monitor_configurations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('enabled')->default(false);
            $table->string('cadence', 30)->default('weekly');
            $table->timestamp('next_check_in_at')->nullable();
            $table->timestamps();
        });

        Schema::create('monitor_check_ins', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->json('responses');
            $table->timestamp('recorded_at');
            $table->timestamps();
            $table->index(['business_id', 'recorded_at']);
        });

        Schema::create('health_indicators', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('key', 80);
            $table->string('status', 30);
            $table->decimal('value', 12, 4)->nullable();
            $table->timestamp('measured_at');
            $table->json('context')->nullable();
            $table->timestamps();
            $table->index(['business_id', 'key', 'measured_at']);
        });

        Schema::create('ai_prompts', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('template');
            $table->unsignedInteger('version')->default(1);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('ai_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('ai_prompt_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider');
            $table->string('model');
            $table->string('status', 30);
            $table->json('input')->nullable();
            $table->json('output')->nullable();
            $table->unsignedInteger('input_tokens')->nullable();
            $table->unsignedInteger('output_tokens')->nullable();
            $table->decimal('cost', 12, 6)->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
            $table->index(['business_id', 'created_at']);
        });

        Schema::create('products', function (Blueprint $table): void {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('product_plans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->string('name');
            $table->unsignedInteger('price_minor');
            $table->char('currency', 3)->default('EUR');
            $table->json('entitlements');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['product_id', 'key']);
        });

        Schema::create('subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_plan_id')->constrained()->restrictOnDelete();
            $table->string('status', 30)->default('active');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->string('external_reference')->nullable()->unique();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });

        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('amount_minor');
            $table->char('currency', 3);
            $table->string('status', 30);
            $table->string('external_reference')->nullable()->unique();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number')->unique();
            $table->unsignedInteger('amount_minor');
            $table->char('currency', 3);
            $table->string('status', 30);
            $table->timestamp('issued_at');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('entitlements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->string('value');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'key']);
        });

        Schema::create('notifications_log', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->string('action');
            $table->json('context')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
            $table->index(['subject_type', 'subject_id']);
            $table->index(['actor_id', 'occurred_at']);
        });

        Schema::create('analytics_events', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('business_id')->nullable()->constrained()->nullOnDelete();
            $table->string('idempotency_key', 120)->nullable()->unique();
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();
            $table->index(['name', 'occurred_at']);
        });
    }

    public function down(): void
    {
        foreach ([
            'analytics_events',
            'audit_logs',
            'notifications_log',
            'entitlements',
            'invoices',
            'payments',
            'subscriptions',
            'product_plans',
            'products',
            'ai_runs',
            'ai_prompts',
            'health_indicators',
            'monitor_check_ins',
            'monitor_configurations',
            'business_opportunities',
            'opportunities',
            'opportunity_types',
            'guide_steps',
            'guide_sections',
            'guides',
            'action_outcomes',
            'actions',
            'action_plans',
            'priorities',
            'recommendations',
            'evaluation_findings',
            'evaluation_answers',
            'evaluations',
            'evaluation_versions',
            'business_metric_values',
            'business_metrics',
            'business_goals',
            'business_stage_histories',
            'business_members',
            'businesses',
            'profiles',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
