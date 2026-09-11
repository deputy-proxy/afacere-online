<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table): void {
            $table->json('context')->nullable()->after('profile');
        });

        Schema::create('business_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('disk', 50)->default('local');
            $table->string('path');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('visibility', 20)->default('private');
            $table->timestamp('uploaded_at');
            $table->timestamps();

            $table->index(['business_id', 'uploaded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_documents');

        Schema::table('businesses', function (Blueprint $table): void {
            $table->dropColumn('context');
        });
    }
};
