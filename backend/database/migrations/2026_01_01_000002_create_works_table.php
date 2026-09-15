<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('abstract')->nullable();
            $table->string('original_language')->default('es');
            $table->string('dewey')->nullable();
            $table->string('nature')->default('Obra Literaria');
            $table->timestamps();
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('author_work', function (Blueprint $table) {
            $table->foreignId('work_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->constrained()->cascadeOnDelete();
            $table->primary(['work_id', 'author_id']);
        });

        Schema::create('subject_work', function (Blueprint $table) {
            $table->foreignId('work_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->primary(['work_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_work');
        Schema::dropIfExists('author_work');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('works');
    }
};
