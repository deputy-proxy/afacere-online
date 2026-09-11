<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('bio')->nullable();
            $table->json('expertise')->nullable();
            $table->string('verification_status')->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->unique('user_id');
        });
        Schema::create('expert_availabilities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('expert_id')->constrained()->cascadeOnDelete();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->boolean('is_bookable')->default(true);
            $table->timestamps();
            $table->index(['expert_id', 'starts_at']);
        });
        Schema::create('consultations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expert_id')->constrained()->cascadeOnDelete();
            $table->foreignId('availability_id')->nullable()->constrained('expert_availabilities')->nullOnDelete();
            $table->string('status')->default('requested');
            $table->text('request_note')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->index(['business_id', 'status']);
            $table->index(['expert_id', 'status']);
        });
        Schema::create('consultation_shares', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('consultation_id')->constrained()->cascadeOnDelete();
            $table->string('share_type', 40);
            $table->json('fields')->nullable();
            $table->timestamp('consented_at');
            $table->foreignId('consented_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['consultation_id', 'share_type']);
        });
        Schema::create('consultation_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('consultation_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('outcome')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultation_sessions');
        Schema::dropIfExists('consultation_shares');
        Schema::dropIfExists('consultations');
        Schema::dropIfExists('expert_availabilities');
        Schema::dropIfExists('experts');
    }
};
