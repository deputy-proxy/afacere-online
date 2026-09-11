<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_providers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('verification_status')->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
        Schema::create('marketplace_services', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->constrained('marketplace_providers')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('eligibility')->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
        Schema::create('marketplace_leads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained('marketplace_providers')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('marketplace_services')->nullOnDelete();
            $table->string('status')->default('new');
            $table->text('message')->nullable();
            $table->json('shared_context')->nullable();
            $table->timestamps();
            $table->index(['business_id', 'status']);
        });
        Schema::create('marketplace_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lead_id')->constrained('marketplace_leads')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('body')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->unique(['lead_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_reviews');
        Schema::dropIfExists('marketplace_leads');
        Schema::dropIfExists('marketplace_services');
        Schema::dropIfExists('marketplace_providers');
    }
};