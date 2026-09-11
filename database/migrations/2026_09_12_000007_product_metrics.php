<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_metric_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->string('metric_key');
            $table->string('scope_type');
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('value', 18, 4);
            $table->json('dimensions')->nullable();
            $table->timestamps();
            $table->unique(['metric_key', 'scope_type', 'scope_id', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_metric_snapshots');
    }
};