<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add metadata columns to expressions table
        Schema::table('expressions', function (Blueprint $table) {
            if (!Schema::hasColumn('expressions', 'type')) {
                $table->string('type')->nullable()->default('Texto Impreso')->after('language_id');
            }
            if (!Schema::hasColumn('expressions', 'revision_year')) {
                $table->integer('revision_year')->nullable()->after('type');
            }
            if (!Schema::hasColumn('expressions', 'description')) {
                $table->text('description')->nullable()->after('revision_year');
            }
        });

        // 1. Refactorización de la tabla items (Separación de Sede Patrimonial y Ubicación Física)
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'home_branch_id')) {
                $table->foreignId('home_branch_id')->nullable()->after('manifestation_id')->constrained('branches')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('items', 'current_branch_id')) {
                $table->foreignId('current_branch_id')->nullable()->after('home_branch_id')->constrained('branches')->cascadeOnDelete();
            }
        });

        // Migración de datos existentes: copiar branch_id como home_branch_id y current_branch_id si branch_id existe
        if (Schema::hasColumn('items', 'branch_id')) {
            DB::statement('UPDATE items SET home_branch_id = branch_id, current_branch_id = branch_id WHERE home_branch_id IS NULL');

            Schema::table('items', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        // 2. Refactorización de la tabla loans (Auditoría Intersede y Trazabilidad)
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'checkout_branch_id')) {
                $table->foreignId('checkout_branch_id')->nullable()->after('user_id')->constrained('branches')->nullOnDelete();
            }
            if (!Schema::hasColumn('loans', 'return_branch_id')) {
                $table->foreignId('return_branch_id')->nullable()->after('checkout_branch_id')->constrained('branches')->nullOnDelete();
            }
        });

        // 3. Creación de la tabla transfers para trazabilidad de Préstamo Intersede (ILL) y Repatriación
        if (!Schema::hasTable('transfers')) {
            Schema::create('transfers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
                $table->foreignId('origin_branch_id')->constrained('branches')->cascadeOnDelete();
                $table->foreignId('destination_branch_id')->constrained('branches')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('requested_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->enum('status', ['pending', 'in_transit', 'completed', 'failed', 'lost'])->default('pending');
                $table->string('reason')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::table('expressions', function (Blueprint $table) {
            $table->dropColumn(['type', 'revision_year', 'description']);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
        });

        DB::statement('UPDATE items SET branch_id = current_branch_id');

        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['home_branch_id']);
            $table->dropForeign(['current_branch_id']);
            $table->dropColumn(['home_branch_id', 'current_branch_id']);
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['checkout_branch_id']);
            $table->dropForeign(['return_branch_id']);
            $table->dropColumn(['checkout_branch_id', 'return_branch_id']);
        });

        Schema::dropIfExists('transfers');
    }
};
