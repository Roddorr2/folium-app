<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expressions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->date('translation_date')->nullable();
            $table->timestamps();
        });

        Schema::create('manifestations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expression_id')->constrained()->cascadeOnDelete();
            $table->string('isbn')->nullable();
            $table->string('publisher')->nullable();
            $table->integer('publication_year')->nullable();
            $table->string('format')->default('Físico');
            $table->timestamps();
        });

        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manifestation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('barcode')->unique();
            $table->string('shelf_location')->nullable();
            $table->enum('status', ['available', 'loaned', 'reserved', 'in_transit', 'lost'])->default('available');
            $table->timestamps();
        });

        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable();
            $table->date('due_date');
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
        Schema::dropIfExists('items');
        Schema::dropIfExists('manifestations');
        Schema::dropIfExists('expressions');
    }
};
