<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debt_receivable_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debt_receivable_id')->constrained('debts_receivables')->cascadeOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['debt_receivable_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_receivable_payments');
    }
};
