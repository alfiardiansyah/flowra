<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['user_id', 'type', 'date'], 'idx_transactions_user_type_date');
        });

        Schema::table('debts_receivables', function (Blueprint $table) {
            $table->index(['user_id', 'status', 'due_date'], 'idx_debts_user_status_duedate');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_transactions_user_type_date');
        });

        Schema::table('debts_receivables', function (Blueprint $table) {
            $table->dropIndex('idx_debts_user_status_duedate');
        });
    }
};
