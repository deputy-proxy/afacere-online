<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guide_progress', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('guide_version');
            $table->foreignId('current_step_id')->nullable()->constrained('guide_steps')->nullOnDelete();
            $table->json('completed_steps')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['guide_id', 'user_id', 'business_id', 'guide_version']);
            $table->index(['business_id', 'user_id', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guide_progress');
    }
};
