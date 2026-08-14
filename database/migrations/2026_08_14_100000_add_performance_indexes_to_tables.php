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
            $table->index(['user_id', 'account_id'], 'idx_transactions_user_account');
            $table->index(['user_id', 'category_id'], 'idx_transactions_user_category');
        });

        Schema::table('debts_receivables', function (Blueprint $table) {
            $table->index(['user_id', 'status', 'due_date'], 'idx_debts_user_status_duedate');
            $table->index(['user_id', 'type', 'status'], 'idx_debts_user_type_status');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index(['user_id', 'type'], 'idx_categories_user_type');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->index(['user_id', 'is_active'], 'idx_accounts_user_active');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_transactions_user_type_date');
            $table->dropIndex('idx_transactions_user_account');
            $table->dropIndex('idx_transactions_user_category');
        });

        Schema::table('debts_receivables', function (Blueprint $table) {
            $table->dropIndex('idx_debts_user_status_duedate');
            $table->dropIndex('idx_debts_user_type_status');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('idx_categories_user_type');
        });

        Schema::table('accounts', function (Blueprint $table) {
            $table->dropIndex('idx_accounts_user_active');
        });
    }
};
