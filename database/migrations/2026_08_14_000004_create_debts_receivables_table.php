<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debts_receivables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('debt'); // debt (hutang), receivable (piutang)
            $table->string('person_name');
            $table->decimal('amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->date('date');
            $table->date('due_date')->nullable();
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->string('status')->default('unpaid'); // unpaid, partially_paid, paid
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type', 'status']);
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debts_receivables');
    }
};
