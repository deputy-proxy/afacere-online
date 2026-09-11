<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guide_revisions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('content');
            $table->timestamp('created_at');
            $table->unique(['guide_id', 'version']);
        });

        Schema::create('guide_tags', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('guide_tag_assignments', function (Blueprint $table): void {
            $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guide_tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['guide_id', 'guide_tag_id']);
        });

        Schema::create('guide_stage_assignments', function (Blueprint $table): void {
            $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
            $table->string('stage', 40);
            $table->primary(['guide_id', 'stage']);
            $table->index('stage');
        });

        Schema::create('guide_priority_assignments', function (Blueprint $table): void {
            $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
            $table->foreignId('priority_id')->constrained()->cascadeOnDelete();
            $table->primary(['guide_id', 'priority_id']);
        });

        Schema::create('guide_action_assignments', function (Blueprint $table): void {
            $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
            $table->foreignId('action_id')->constrained()->cascadeOnDelete();
            $table->primary(['guide_id', 'action_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guide_action_assignments');
        Schema::dropIfExists('guide_priority_assignments');
        Schema::dropIfExists('guide_stage_assignments');
        Schema::dropIfExists('guide_tag_assignments');
        Schema::dropIfExists('guide_tags');
        Schema::dropIfExists('guide_revisions');
    }
};
