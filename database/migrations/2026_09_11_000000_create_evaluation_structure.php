<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_sections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('evaluation_version_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->string('key', 100);
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['evaluation_version_id', 'key']);
            $table->unique(['evaluation_version_id', 'position']);
        });

        Schema::create('evaluation_questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('evaluation_section_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->string('key', 100);
            $table->string('type', 30);
            $table->text('prompt');
            $table->boolean('required')->default(true);
            $table->json('options')->nullable();
            $table->json('validation_rules')->nullable();
            $table->timestamps();
            $table->unique(['evaluation_section_id', 'key']);
            $table->unique(['evaluation_section_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_questions');
        Schema::dropIfExists('evaluation_sections');
    }
};
