<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
                $table->foreignId('parent_id')->nullable()->constrained('categories')->cascadeOnDelete();
                $table->string('name');
                $table->string('type')->default('expense'); // income, expense
                $table->string('icon', 50)->default('flower');
                $table->string('color', 20)->default('#87A96B');
                $table->boolean('is_default')->default(false);
                $table->timestamps();

                $table->index(['user_id', 'type']);
                $table->index('parent_id');
            });
        } else {
            if (DB::getDriverName() === 'mysql') {
                try {
                    DB::statement('ALTER TABLE categories MODIFY user_id BIGINT UNSIGNED NULL');
                    DB::statement('ALTER TABLE categories MODIFY type VARCHAR(50) NOT NULL DEFAULT "expense"');
                } catch (\Exception $e) {}
            }

            Schema::table('categories', function (Blueprint $table) {
                if (!Schema::hasColumn('categories', 'parent_id')) {
                    $table->foreignId('parent_id')->nullable()->after('user_id')->constrained('categories')->cascadeOnDelete();
                }
                if (!Schema::hasColumn('categories', 'icon')) {
                    $table->string('icon', 50)->default('flower')->after('type');
                }
                if (!Schema::hasColumn('categories', 'color')) {
                    $table->string('color', 20)->default('#87A96B')->after('icon');
                }
                if (!Schema::hasColumn('categories', 'is_default')) {
                    $table->boolean('is_default')->default(false)->after('color');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
