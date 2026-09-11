<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commerce_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('purchasable_type');
            $table->unsignedBigInteger('purchasable_id');
            $table->string('provider');
            $table->string('provider_reference')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('RON');
            $table->string('idempotency_key')->unique();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            $table->index(['purchasable_type', 'purchasable_id']);
            $table->unique(['provider', 'provider_reference']);
        });
        Schema::create('commerce_entitlements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transaction_id')->constrained('commerce_transactions')->cascadeOnDelete();
            $table->string('key');
            $table->timestamp('granted_at');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'key']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('commerce_entitlements');
        Schema::dropIfExists('commerce_transactions');
    }
};
