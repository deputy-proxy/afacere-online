<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('action_plan_revisions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('action_plan_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('snapshot');
            $table->timestamps();
            $table->unique(['action_plan_id', 'version']);
        });

        Schema::create('action_evidence', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('action_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 50);
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
            $table->index(['action_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('action_evidence');
        Schema::dropIfExists('action_plan_revisions');
    }
};
