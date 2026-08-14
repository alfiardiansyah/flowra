<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('month', 7); // Format: 'YYYY-MM', e.g. '2026-08'
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'category_id', 'month']);
            $table->index(['user_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
