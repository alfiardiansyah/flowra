<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('expense'); // income, expense, transfer
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('destination_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->string('description');
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable();
            $table->foreignId('recurring_transaction_id')->nullable()->constrained('recurring_transactions')->nullOnDelete();
            $table->foreignId('debt_receivable_id')->nullable()->constrained('debts_receivables')->nullOnDelete();
            $table->timestamps();

            $table->index(['user_id', 'date']);
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'account_id']);
            $table->index(['user_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
