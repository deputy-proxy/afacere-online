<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guide_progress_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('guide_progress_id')->constrained('guide_progress')->cascadeOnDelete();
            $table->foreignId('guide_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('guide_version');
            $table->string('event_type', 40);
            $table->foreignId('guide_step_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('occurred_at');
            $table->timestamps();
            $table->index(['business_id', 'guide_id', 'event_type', 'occurred_at'], 'guide_progress_events_bus_gui_eve_occ_index_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guide_progress_events');
    }
};
