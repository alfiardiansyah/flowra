<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Seed Default System Categories
        $defaultCategories = [
            // Incomes
            [
                'name' => 'Gaji & Pendapatan',
                'type' => 'income',
                'icon' => 'sunflower',
                'color' => '#87A96B',
                'is_default' => true,
                'children' => ['Gaji', 'Gaji Pokok', 'Tunjangan', 'Bonus & THR', 'Bonus']
            ],
            [
                'name' => 'Investasi',
                'type' => 'income',
                'icon' => 'oak-tree',
                'color' => '#6B8E23',
                'is_default' => true,
                'children' => ['Dividen', 'Capital Gain', 'Bunga Tabungan']
            ],
            [
                'name' => 'Freelance & Usaha',
                'type' => 'income',
                'icon' => 'wildflower',
                'color' => '#9BCF53',
                'is_default' => true,
                'children' => ['Freelance', 'Proyek', 'Penjualan', 'Bisnis']
            ],
            [
                'name' => 'Pendapatan Lainnya',
                'type' => 'income',
                'icon' => 'bouquet',
                'color' => '#5DADE2',
                'is_default' => true,
                'children' => ['Hadiah & Hibah', 'Cashback', 'Lainnya']
            ],
            // Expenses
            [
                'name' => 'Makanan & Minuman',
                'type' => 'expense',
                'icon' => 'apple',
                'color' => '#FF7A5C',
                'is_default' => true,
                'children' => ['Makanan', 'Makan Harian', 'Restoran & Kafe', 'Kopi & Camilan', 'Belanja Dapur']
            ],
            [
                'name' => 'Transportasi',
                'type' => 'expense',
                'icon' => 'leaf-wind',
                'color' => '#5DADE2',
                'is_default' => true,
                'children' => ['Transport', 'Bahan Bakar', 'Transportasi Umum', 'Parkir & Tol', 'Ojek / Taksi Online']
            ],
            [
                'name' => 'Belanja',
                'type' => 'expense',
                'icon' => 'shopping-leaf',
                'color' => '#6B8E23',
                'is_default' => true,
                'children' => ['Belanja', 'Pakaian', 'Elektronik', 'Kebutuhan Harian']
            ],
            [
                'name' => 'Tagihan & Utilitas',
                'type' => 'expense',
                'icon' => 'cactus',
                'color' => '#8B7355',
                'is_default' => true,
                'children' => ['Tagihan', 'Listrik', 'Air', 'Internet & Pulsa', 'Langganan']
            ],
            [
                'name' => 'Hiburan',
                'type' => 'expense',
                'icon' => 'cherry-blossom',
                'color' => '#B19CD9',
                'is_default' => true,
                'children' => ['Hiburan', 'Bioskop & Game', 'Traveling & Liburan', 'Hobi']
            ],
            [
                'name' => 'Kesehatan',
                'type' => 'expense',
                'icon' => 'medical-leaf',
                'color' => '#9BCF53',
                'is_default' => true,
                'children' => ['Kesehatan', 'Obat & Vitamin', 'Dokter & RS', 'Asuransi']
            ],
            [
                'name' => 'Pendidikan',
                'type' => 'expense',
                'icon' => 'book-sprout',
                'color' => '#87A96B',
                'is_default' => true,
                'children' => ['Pendidikan', 'Buku & Kursus', 'SPP & Uang Sekolah']
            ],
            [
                'name' => 'Pengeluaran Lainnya',
                'type' => 'expense',
                'icon' => 'mixed-leaves',
                'color' => '#8B7355',
                'is_default' => true,
                'children' => ['Lainnya', 'Donasi & Amal', 'Pajak & Biaya Admin']
            ]
        ];

        $now = now();
        $categoryMap = []; // name => id

        foreach ($defaultCategories as $parentData) {
            $parentId = DB::table('categories')->insertGetId([
                'user_id' => null,
                'parent_id' => null,
                'name' => $parentData['name'],
                'type' => $parentData['type'],
                'icon' => $parentData['icon'],
                'color' => $parentData['color'],
                'is_default' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $categoryMap[strtolower($parentData['name'])] = $parentId;

            foreach ($parentData['children'] as $childName) {
                $childId = DB::table('categories')->insertGetId([
                    'user_id' => null,
                    'parent_id' => $parentId,
                    'name' => $childName,
                    'type' => $parentData['type'],
                    'icon' => $parentData['icon'],
                    'color' => $parentData['color'],
                    'is_default' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $categoryMap[strtolower($childName)] = $childId;
            }
        }

        // 2. Setup Default Accounts for existing users and migrate existing income/expense data
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            // Create default accounts for user if none exist
            $userAccounts = [
                'cash' => [
                    'name' => 'Dompet Tunai',
                    'type' => 'cash',
                    'icon' => 'cash-leaf',
                    'color' => '#87A96B',
                    'opening_balance' => 0,
                    'current_balance' => 0,
                ],
                'bca' => [
                    'name' => 'BCA',
                    'type' => 'bank',
                    'icon' => 'bank-bca',
                    'color' => '#5DADE2',
                    'opening_balance' => 0,
                    'current_balance' => 0,
                ],
                'mandiri' => [
                    'name' => 'Mandiri',
                    'type' => 'bank',
                    'icon' => 'bank-mandiri',
                    'color' => '#6B8E23',
                    'opening_balance' => 0,
                    'current_balance' => 0,
                ],
                'bri' => [
                    'name' => 'BRI',
                    'type' => 'bank',
                    'icon' => 'bank-bri',
                    'color' => '#557153',
                    'opening_balance' => 0,
                    'current_balance' => 0,
                ],
                'ewallet' => [
                    'name' => 'E-Wallet',
                    'type' => 'ewallet',
                    'icon' => 'e-wallet',
                    'color' => '#B19CD9',
                    'opening_balance' => 0,
                    'current_balance' => 0,
                ],
            ];

            $userAccountIds = [];
            foreach ($userAccounts as $key => $acc) {
                $accId = DB::table('accounts')->insertGetId([
                    'user_id' => $user->id,
                    'name' => $acc['name'],
                    'type' => $acc['type'],
                    'icon' => $acc['icon'],
                    'color' => $acc['color'],
                    'opening_balance' => $acc['opening_balance'],
                    'current_balance' => $acc['current_balance'],
                    'currency' => 'IDR',
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $userAccountIds[$key] = $accId;
                $userAccountIds[strtolower($acc['name'])] = $accId;
            }

            // Helper to get matching account ID for user
            $getAccountId = function ($bankName) use ($userAccountIds) {
                $normalized = strtolower(trim($bankName ?? ''));
                if (isset($userAccountIds[$normalized])) {
                    return $userAccountIds[$normalized];
                }
                if (str_contains($normalized, 'bca')) return $userAccountIds['bca'];
                if (str_contains($normalized, 'mandiri')) return $userAccountIds['mandiri'];
                if (str_contains($normalized, 'bri')) return $userAccountIds['bri'];
                if (str_contains($normalized, 'wallet') || str_contains($normalized, 'gopay') || str_contains($normalized, 'ovo') || str_contains($normalized, 'dana')) return $userAccountIds['ewallet'];
                return $userAccountIds['cash'];
            };

            // Helper to get category ID
            $getCategoryId = function ($catName, $defaultType) use ($categoryMap) {
                $normalized = strtolower(trim($catName ?? ''));
                if (isset($categoryMap[$normalized])) {
                    return $categoryMap[$normalized];
                }
                return $defaultType === 'income' ? ($categoryMap['lainnya'] ?? null) : ($categoryMap['pengeluaran lainnya'] ?? null);
            };

            // Migrate Incomes if table exists
            if (Schema::hasTable('incomes')) {
                $incomes = DB::table('incomes')->where('user_id', $user->id)->get();
                foreach ($incomes as $inc) {
                    $accId = $getAccountId($inc->nama_bank);
                    $catId = $getCategoryId($inc->kategori, 'income');

                    DB::table('transactions')->insert([
                        'user_id' => $user->id,
                        'type' => 'income',
                        'account_id' => $accId,
                        'destination_account_id' => null,
                        'category_id' => $catId,
                        'amount' => $inc->nominal,
                        'date' => $inc->tanggal ?? $now->format('Y-m-d'),
                        'description' => $inc->keterangan ?: ($inc->kategori ?: 'Pemasukan'),
                        'notes' => null,
                        'attachment' => $inc->bukti_transfer ?? null,
                        'created_at' => $inc->created_at ?? $now,
                        'updated_at' => $inc->updated_at ?? $now,
                    ]);

                    // Update account balance
                    DB::table('accounts')->where('id', $accId)->increment('current_balance', $inc->nominal);
                }
            }

            // Migrate Expenses if table exists
            if (Schema::hasTable('expenses')) {
                $expenses = DB::table('expenses')->where('user_id', $user->id)->get();
                foreach ($expenses as $exp) {
                    $accId = $getAccountId($exp->metode_pembayaran);
                    $catId = $getCategoryId($exp->kategori, 'expense');

                    DB::table('transactions')->insert([
                        'user_id' => $user->id,
                        'type' => 'expense',
                        'account_id' => $accId,
                        'destination_account_id' => null,
                        'category_id' => $catId,
                        'amount' => $exp->nominal,
                        'date' => $exp->tanggal ?? $now->format('Y-m-d'),
                        'description' => $exp->keterangan ?: ($exp->kategori ?: 'Pengeluaran'),
                        'notes' => null,
                        'attachment' => $exp->bukti_pembayaran ?? null,
                        'created_at' => $exp->created_at ?? $now,
                        'updated_at' => $exp->updated_at ?? $now,
                    ]);

                    // Update account balance
                    DB::table('accounts')->where('id', $accId)->decrement('current_balance', $exp->nominal);
                }
            }
        }
    }

    public function down(): void
    {
        // Safe rollback
        DB::table('transactions')->truncate();
        DB::table('budgets')->truncate();
        DB::table('debt_receivable_payments')->truncate();
        DB::table('debts_receivables')->truncate();
        DB::table('recurring_transactions')->truncate();
        DB::table('accounts')->truncate();
        DB::table('categories')->truncate();
    }
};
