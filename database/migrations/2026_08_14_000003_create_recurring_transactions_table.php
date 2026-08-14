<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('expense'); // income, expense, transfer
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('destination_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->string('frequency')->default('monthly'); // daily, weekly, monthly, yearly
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_run_date');
            $table->date('last_run_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_record')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_active', 'next_run_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_transactions');
    }
};
