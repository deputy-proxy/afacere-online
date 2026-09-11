<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_goals', function (Blueprint $table): void {
            $table->string('stage', 40)->nullable()->after('business_id')->index();
        });

        Schema::table('business_metrics', function (Blueprint $table): void {
            $table->string('stage', 40)->nullable()->after('business_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('business_goals', function (Blueprint $table): void {
            $table->dropColumn('stage');
        });

        Schema::table('business_metrics', function (Blueprint $table): void {
            $table->dropColumn('stage');
        });
    }
};
