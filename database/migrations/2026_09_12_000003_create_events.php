<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_events', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('workshop');
            $table->string('status')->default('published');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('capacity')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->boolean('is_online')->default(true);
            $table->timestamps();
        });
        Schema::create('event_registrations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained('platform_events')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('registered');
            $table->timestamp('registered_at');
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->unique(['event_id', 'user_id']);
        });
        Schema::create('event_attendance', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('registration_id')->constrained('event_registrations')->cascadeOnDelete();
            $table->timestamp('attended_at');
            $table->timestamps();
            $table->unique('registration_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_attendance');
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('platform_events');
    }
};