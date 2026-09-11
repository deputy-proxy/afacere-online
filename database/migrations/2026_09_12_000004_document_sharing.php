<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_evidence', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_document_id')->constrained('business_documents')->cascadeOnDelete();
            $table->string('evidence_type', 40);
            $table->string('evidenceable_type')->nullable();
            $table->unsignedBigInteger('evidenceable_id')->nullable();
            $table->timestamps();
            $table->index(['evidenceable_type', 'evidenceable_id']);
        });
        Schema::create('document_shares', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_document_id')->constrained('business_documents')->cascadeOnDelete();
            $table->foreignId('shared_by')->constrained('users')->cascadeOnDelete();
            $table->string('recipient_type', 40);
            $table->unsignedBigInteger('recipient_id');
            $table->timestamp('shared_at');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->index(['recipient_type', 'recipient_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('document_shares');
        Schema::dropIfExists('document_evidence');
    }
};
