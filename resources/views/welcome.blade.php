<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flowra — Kelola Keuanganmu!</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icons/sprout.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/icons/sprout.png') }}">
    <meta name="description" content="Flowra adalah aplikasi manajemen keuangan pribadi modern dengan multi-rekening, anggaran bulanan, transaksi rutin, pencatatan hutang piutang, dan laporan arus kas yang intuitif.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Satisfy&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-ivory text-earth-800 font-sans antialiased selection:bg-sage-200 selection:text-sage-800 min-h-screen relative overflow-x-hidden">

    <!-- Ambient Botanical Background Ornaments -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden opacity-35">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-sage-200/50 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -right-24 w-80 h-80 bg-mint-200/40 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-1/4 w-96 h-96 bg-cream rounded-full blur-3xl"></div>
    </div>

    <!-- Navigation Bar -->
    <header class="sticky top-0 z-50 bg-ivory/80 backdrop-blur-md border-b border-sage-200/60 transition-all duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('welcome') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-2xl bg-sage-100 flex items-center justify-center shadow-inner group-hover:scale-105 transition-transform duration-300">
                    <x-icon name="sprout" class="w-7 h-7 text-sage-600 animate-grow" />
                </div>
                <div class="flex flex-col">
                    <span class="font-heading font-bold text-2xl text-sage-700 tracking-tight leading-none group-hover:text-sage-800 transition-colors">
                        Flowra
                    </span>
                    <span class="font-satisfy text-xs text-earth-500 tracking-wide mt-0.5">
                        Personal Finance Garden
                    </span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-earth-600">
                <a href="#fitur" class="hover:text-sage-600 transition-colors">Fitur Unggulan</a>
                <a href="#demo-live" class="hover:text-sage-600 transition-colors">Demo Dashboard</a>
                <a href="#simulasi" class="hover:text-sage-600 transition-colors">Kalkulator Kebun</a>
            </nav>

            <!-- Auth CTA Buttons -->
            <div class="flex items-center gap-3">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-flora-primary text-xs sm:text-sm py-2.5 px-5 flex items-center gap-2">
                            <x-icon name="flower-bloom" class="w-4 h-4 text-white" />
                            <span>Buka Kebun Saya</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs sm:text-sm font-semibold text-sage-700 hover:text-sage-800 px-3 py-2 rounded-xl hover:bg-sage-50 transition-colors">
                            Masuk
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-flora-primary text-xs sm:text-sm py-2.5 px-5 shadow-flora flex items-center gap-2">
                                <x-icon name="add-seed" class="w-4 h-4 text-white" />
                                <span>Mulai Gratis</span>
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="relative z-10">

        <!-- 1. Hero Section -->
        <section class="pt-12 pb-20 md:pt-20 md:pb-28 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative">

            <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl text-sage-800 font-bold max-w-4xl mx-auto leading-tight sm:leading-tight lg:leading-tight">
                Tumbuhkan & Rawat Finansial Anda Seperti <span class="text-sage-600 underline decoration-mint-400 decoration-wavy decoration-2">Kebun yang Asri</span>
            </h1>

            <p class="mt-6 text-base sm:text-lg text-earth-600 max-w-2xl mx-auto font-light leading-relaxed">
                Kelola multi-rekening, susun anggaran bulanan, jadwalkan tagihan rutin, serta pantau hutang & piutang dengan ketenangan estetika visual yang simpel dan intuitif.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="btn-flora-primary w-full sm:w-auto text-base py-3.5 px-8 shadow-flora-lg flex items-center justify-center gap-2">
                    <x-icon name="sprout" class="w-5 h-5 text-white" />
                    <span>Mulai Tanam Benih Finansial</span>
                </a>
                <a href="#demo-live" class="btn-flora-secondary w-full sm:w-auto text-base py-3.5 px-6 flex items-center justify-center gap-2">
                    <x-icon name="flower-bloom" class="w-5 h-5 text-sage-600" />
                    <span>Coba Demo Interaktif</span>
                </a>
            </div>

            <!-- Trust Highlights -->
            <div class="mt-12 pt-8 border-t border-sage-200/70 grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-4xl mx-auto text-left">
                <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/60 border border-sage-100">
                    <x-icon name="cash-leaf" class="w-8 h-8 text-sage-600 shrink-0" />
                    <div>
                        <div class="text-xs font-bold text-earth-800">Multi-Rekening</div>
                        <div class="text-[11px] text-earth-500">Bank, E-Wallet, Cash</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/60 border border-sage-100">
                    <x-icon name="transfer" class="w-8 h-8 text-sky-500 shrink-0" />
                    <div>
                        <div class="text-xs font-bold text-earth-800">Transfer Bersih</div>
                        <div class="text-[11px] text-earth-500">Tanpa distorsi arus kas</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/60 border border-sage-100">
                    <x-icon name="tree" class="w-8 h-8 text-leaf-600 shrink-0" />
                    <div>
                        <div class="text-xs font-bold text-earth-800">Anggaran Kategori</div>
                        <div class="text-[11px] text-earth-500">Meteran kesehatan belanja</div>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/60 border border-sage-100">
                    <x-icon name="leaf" class="w-8 h-8 text-coral-500 shrink-0" />
                    <div>
                        <div class="text-xs font-bold text-earth-800">Hutang & Piutang</div>
                        <div class="text-[11px] text-earth-500">Pelunasan bertahap</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. Interactive Live Showcase / Sandbox Dashboard -->
        <section id="demo-live" class="py-16 bg-sage-50/70 border-y border-sage-200/80 relative" 
                 x-data="{
                    activeTab: 'overview',
                    bcaBalance: 24500000,
                    cashBalance: 1750000,
                    gopayBalance: 850000,
                    foodSpent: 1250000,
                    foodBudget: 2000000,
                    simulatedTx: [
                        { desc: 'Gaji Bulanan', type: 'income', amount: 8500000, acc: 'BCA', cat: 'Gaji', time: 'Hari ini' },
                        { desc: 'Belanja Supermarket', type: 'expense', amount: 350000, acc: 'BCA', cat: 'Makanan', time: 'Kemarin' },
                        { desc: 'Kopi & Snack', type: 'expense', amount: 45000, acc: 'GoPay', cat: 'Kopi & Camilan', time: '2 hari lalu' }
                    ],
                    get netWorth() {
                        return this.bcaBalance + this.cashBalance + this.gopayBalance;
                    },
                    addMockIncome() {
                        this.bcaBalance += 500000;
                        this.simulatedTx.unshift({ desc: 'Bonus Proyek (Demo)', type: 'income', amount: 500000, acc: 'BCA', cat: 'Pendapatan Tambahan', time: 'Baru saja' });
                    },
                    addMockExpense() {
                        if (this.cashBalance >= 50000) {
                            this.cashBalance -= 50000;
                            this.foodSpent += 50000;
                            this.simulatedTx.unshift({ desc: 'Makan Siang (Demo)', type: 'expense', amount: 50000, acc: 'Tunai', cat: 'Makanan', time: 'Baru saja' });
                        }
                    },
                    resetDemo() {
                        this.bcaBalance = 24500000;
                        this.cashBalance = 1750000;
                        this.gopayBalance = 850000;
                        this.foodSpent = 1250000;
                        if (Array.isArray(this.simulatedTx)) {
                            this.simulatedTx.splice(0, this.simulatedTx.length,
                                { desc: 'Gaji Bulanan', type: 'income', amount: 8500000, acc: 'BCA', cat: 'Gaji', time: 'Hari ini' },
                                { desc: 'Belanja Supermarket', type: 'expense', amount: 350000, acc: 'BCA', cat: 'Makanan', time: 'Kemarin' },
                                { desc: 'Kopi & Snack', type: 'expense', amount: 45000, acc: 'GoPay', cat: 'Kopi & Camilan', time: '2 hari lalu' }
                            );
                        }
                    }
                 }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-10">
                    <span class="text-xs font-bold text-sage-600 uppercase tracking-widest bg-sage-100 px-3 py-1 rounded-full">Interactive Sandbox</span>
                    <h2 class="font-heading text-3xl sm:text-4xl text-sage-800 font-bold mt-2">
                        Pratinjau Dashboard Langsung
                    </h2>
                    <p class="text-earth-600 text-sm mt-2">
                        Coba simulasi interaktif di bawah ini untuk merasakan kemudahan dan kecepatan Flowra sebelum mendaftar.
                    </p>
                </div>

                <!-- Simulation Interactive Card -->
                <div class="flora-card bg-white p-6 sm:p-8 rounded-3xl shadow-flora-lg border border-sage-200 max-w-5xl mx-auto">
                    <!-- Dashboard Header Demo -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-sage-100">
                        <div>
                            <span class="text-xs font-semibold text-earth-500">Halo, Penjelajah Kebun! 🌿</span>
                            <h3 class="font-heading text-2xl text-sage-700 font-bold">Ringkasan Kebun Finansial</h3>
                        </div>

                        <!-- Live Demo Controls -->
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" @click="addMockIncome()" class="btn-flora-primary text-xs py-2 px-3 flex items-center gap-1.5" title="Simulasi Pemasukan">
                                <x-icon name="sprout" class="w-3.5 h-3.5 text-white" />
                                <span>+ Simulasi Pemasukan</span>
                            </button>
                            <button type="button" @click="addMockExpense()" class="bg-coral-500 hover:bg-coral-600 text-white font-medium text-xs py-2 px-3 rounded-xl transition-all flex items-center gap-1.5" title="Simulasi Pengeluaran">
                                <x-icon name="falling-leaves" class="w-3.5 h-3.5 text-white" />
                                <span>- Simulasi Pengeluaran</span>
                            </button>
                            <button type="button" @click.prevent="resetDemo()" class="btn-flora-secondary text-xs py-2 px-2.5" title="Reset Demo">
                                ↺ Reset
                            </button>
                        </div>
                    </div>

                    <!-- Net Worth & Account Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 my-6">
                        <!-- Net Worth -->
                        <div class="flora-card bg-flora-gradient text-white p-5 rounded-2xl flex flex-col justify-between shadow-flora relative overflow-hidden">
                            <div class="relative z-10">
                                <span class="text-xs font-bold uppercase tracking-wider text-white/80">Kekayaan Bersih (Net Worth)</span>
                                <div class="text-3xl font-bold font-heading text-white mt-1">
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(netWorth)"></span>
                                </div>
                            </div>
                            <div class="text-xs text-white/80 mt-3 pt-2 border-t border-white/20 flex items-center justify-between relative z-10">
                                <span>3 Sumber Rekening Aktif</span>
                                <span class="font-bold text-mint-100">Aktif & Terkendali</span>
                            </div>
                        </div>

                        <!-- Account 1: BCA -->
                        <div class="flora-card p-5 rounded-2xl border-l-4 border-l-[#5DADE2] bg-white flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <x-icon name="bank-bca" class="w-6 h-6 text-sky-600" />
                                    <span class="font-semibold text-sm text-earth-800">Bank BCA</span>
                                </div>
                                <span class="flora-badge flora-badge-info text-[10px]">Bank</span>
                            </div>
                            <div class="my-2">
                                <div class="text-xs text-earth-500 font-medium">Saldo</div>
                                <div class="text-xl font-bold text-sage-700">
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(bcaBalance)"></span>
                                </div>
                            </div>
                            <div class="text-[11px] text-earth-400">Gaji & Pengeluaran Utama</div>
                        </div>

                        <!-- Account 2: Dompet & GoPay -->
                        <div class="flora-card p-5 rounded-2xl border-l-4 border-l-leaf-400 bg-white flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <x-icon name="cash-leaf" class="w-6 h-6 text-leaf-600" />
                                    <span class="font-semibold text-sm text-earth-800">Dompet Tunai & E-Wallet</span>
                                </div>
                                <span class="flora-badge flora-badge-success text-[10px]">Harian</span>
                            </div>
                            <div class="my-2">
                                <div class="text-xs text-earth-500 font-medium">Total Uang Saku</div>
                                <div class="text-xl font-bold text-leaf-600">
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(cashBalance + gopayBalance)"></span>
                                </div>
                            </div>
                            <div class="text-[11px] text-earth-400">Uang Tunai: Rp <span x-text="new Intl.NumberFormat('id-ID').format(cashBalance)"></span> • GoPay: Rp <span x-text="new Intl.NumberFormat('id-ID').format(gopayBalance)"></span></div>
                        </div>
                    </div>

                    <!-- Lower Row: Live Budget Bar & Recent Transactions -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                        <!-- Budget Preview -->
                        <div class="p-5 rounded-2xl bg-sage-50/50 border border-sage-200">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <x-icon name="apple" class="w-5 h-5 text-coral-500" />
                                    <span class="font-bold text-sm text-earth-800">Anggaran Kategori Makanan</span>
                                </div>
                                <span class="text-xs font-bold" 
                                      :class="(foodSpent / foodBudget) >= 1 ? 'text-coral-600' : 'text-sage-700'"
                                      x-text="Math.round((foodSpent / foodBudget) * 100) + '%'">
                                </span>
                            </div>

                            <div class="w-full bg-sage-200/80 rounded-full h-3 overflow-hidden my-2">
                                <div class="h-3 rounded-full transition-all duration-500"
                                     :style="'width: ' + Math.min(100, Math.round((foodSpent / foodBudget) * 100)) + '%; background-color: ' + ((foodSpent / foodBudget) >= 1 ? '#FF6B6B' : '#6B8E23')"></div>
                            </div>

                            <div class="flex items-center justify-between text-xs text-earth-600 mt-2">
                                <span>Terpakai: <b class="text-earth-800">Rp <span x-text="new Intl.NumberFormat('id-ID').format(foodSpent)"></span></b></span>
                                <span>Batas: <b>Rp <span x-text="new Intl.NumberFormat('id-ID').format(foodBudget)"></span></b></span>
                            </div>
                        </div>

                        <!-- Recent Transactions Preview -->
                        <div class="p-5 rounded-2xl bg-sage-50/50 border border-sage-200">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-bold text-sm text-earth-800 flex items-center gap-1.5">
                                    <x-icon name="flower-bloom" class="w-4 h-4 text-sage-500" />
                                    Mutasi Transaksi Terakhir
                                </span>
                                <span class="text-[11px] text-earth-500">Live Feedback</span>
                            </div>

                            <div class="space-y-2 max-h-36 overflow-y-auto pr-1">
                                <template x-for="(tx, index) in simulatedTx.slice(0, 3)" :key="index">
                                    <div class="p-2 rounded-xl bg-white border border-sage-100 flex items-center justify-between text-xs">
                                        <div>
                                            <div class="font-semibold text-earth-800" x-text="tx.desc"></div>
                                            <div class="text-[10px] text-earth-400" x-text="tx.acc + ' • ' + tx.cat"></div>
                                        </div>
                                        <div class="font-bold" :class="tx.type === 'income' ? 'text-leaf-600' : 'text-coral-600'"
                                             x-text="(tx.type === 'income' ? '+ ' : '- ') + 'Rp ' + new Intl.NumberFormat('id-ID').format(tx.amount)">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-sage-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-earth-500">
                        <span>✨ Ini adalah contoh simulasi langsung. Data asli Anda akan tersimpan dengan aman dan terenkripsi.</span>
                        <a href="{{ route('register') }}" class="text-sage-700 font-bold hover:underline">
                            Daftar Sekarang untuk Menyimpan Kebun Anda →
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. Feature Highlights Deep-Dive Grid -->
        <section id="fitur" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold text-sage-600 uppercase tracking-widest bg-sage-100 px-3 py-1 rounded-full">Fitur Lengkap</span>
                <h2 class="font-heading text-3xl sm:text-4xl text-sage-800 font-bold mt-3">
                    Dirancang untuk Kebutuhan Finansial Nyata
                </h2>
                <p class="text-earth-600 text-sm mt-3">
                    Kompleksitas sistem keuangan yang andal di balik antarmuka pengguna yang anggun, tenang, dan mudah digunakan setiap hari.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="flora-card p-8 border-t-4 border-t-leaf-500 transition-all hover:shadow-flora-lg hover:-translate-y-1">
                    <div class="w-14 h-14 rounded-2xl bg-mint-100 flex items-center justify-center mb-5 text-leaf-600">
                        <x-icon name="cash-leaf" class="w-8 h-8" />
                    </div>
                    <h3 class="font-heading text-xl font-bold text-earth-800 mb-2">Multi-Rekening & Transfer Akurat</h3>
                    <p class="text-xs text-earth-600 leading-relaxed mb-4">
                        Kelola rekening bank (BCA, Mandiri, BRI), dompet digital (GoPay, OVO, DANA), hingga uang tunai. Transfer antar rekening tidak akan merusak kalkulasi pemasukan dan pengeluaran Anda.
                    </p>
                    <span class="text-[11px] font-semibold text-leaf-600 bg-mint-50 px-2.5 py-1 rounded-lg">Zero-Distortion Cash Flow</span>
                </div>

                <!-- Feature 2 -->
                <div class="flora-card p-8 border-t-4 border-t-sage-500 transition-all hover:shadow-flora-lg hover:-translate-y-1">
                    <div class="w-14 h-14 rounded-2xl bg-sage-100 flex items-center justify-center mb-5 text-sage-600">
                        <x-icon name="tree" class="w-8 h-8" />
                    </div>
                    <h3 class="font-heading text-xl font-bold text-earth-800 mb-2">Anggaran Kategori & Rollover</h3>
                    <p class="text-xs text-earth-600 leading-relaxed mb-4">
                        Tetapkan batas belanja per kategori dengan indikator warna kesehatan anggaran. Gandakan anggaran bulan lalu ke bulan baru hanya dengan 1 klik mudah.
                    </p>
                    <span class="text-[11px] font-semibold text-sage-600 bg-sage-50 px-2.5 py-1 rounded-lg">Visual Health Indicator</span>
                </div>

                <!-- Feature 3 -->
                <div class="flora-card p-8 border-t-4 border-t-sky-500 transition-all hover:shadow-flora-lg hover:-translate-y-1">
                    <div class="w-14 h-14 rounded-2xl bg-sky-100 flex items-center justify-center mb-5 text-sky-600">
                        <x-icon name="leaf-wind" class="w-8 h-8" />
                    </div>
                    <h3 class="font-heading text-xl font-bold text-earth-800 mb-2">Transaksi Rutin & Tagihan</h3>
                    <p class="text-xs text-earth-600 leading-relaxed mb-4">
                        Jadwalkan sewa kos, langganan internet, tagihan listrik, atau pemasukan gaji dengan frekuensi fleksibel (harian, mingguan, bulanan, tahunan) serta fitur auto-record.
                    </p>
                    <span class="text-[11px] font-semibold text-sky-600 bg-sky-50 px-2.5 py-1 rounded-lg">Smart Due Date Alerts</span>
                </div>

                <!-- Feature 4 -->
                <div class="flora-card p-8 border-t-4 border-t-coral-500 transition-all hover:shadow-flora-lg hover:-translate-y-1">
                    <div class="w-14 h-14 rounded-2xl bg-coral-100 flex items-center justify-center mb-5 text-coral-600">
                        <x-icon name="leaf" class="w-8 h-8" />
                    </div>
                    <h3 class="font-heading text-xl font-bold text-earth-800 mb-2">Pencatatan Hutang & Piutang</h3>
                    <p class="text-xs text-earth-600 leading-relaxed mb-4">
                        Pantau uang yang Anda pinjamkan atau pinjam dari orang lain. Lengkap dengan riwayat angsuran, progress bar pelunasan, dan jatuh tempo.
                    </p>
                    <span class="text-[11px] font-semibold text-coral-600 bg-coral-50 px-2.5 py-1 rounded-lg">Installment Tracking</span>
                </div>

                <!-- Feature 5 -->
                <div class="flora-card p-8 border-t-4 border-t-amber-500 transition-all hover:shadow-flora-lg hover:-translate-y-1">
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 flex items-center justify-center mb-5 text-amber-600">
                        <x-icon name="cherry-blossom" class="w-8 h-8" />
                    </div>
                    <h3 class="font-heading text-xl font-bold text-earth-800 mb-2">Laporan Arus Kas & Analisis</h3>
                    <p class="text-xs text-earth-600 leading-relaxed mb-4">
                        Dapatkan wawasan mendalam saldo awal, pemasukan, pengeluaran, rasio simpanan (savings rate), grafik tren, serta export instan ke format CSV / cetak.
                    </p>
                    <span class="text-[11px] font-semibold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg">Export CSV & Print</span>
                </div>

                <!-- Feature 6 -->
                <div class="flora-card p-8 border-t-4 border-t-purple-500 transition-all hover:shadow-flora-lg hover:-translate-y-1">
                    <div class="w-14 h-14 rounded-2xl bg-purple-100 flex items-center justify-center mb-5 text-purple-600">
                        <x-icon name="bouquet" class="w-8 h-8" />
                    </div>
                    <h3 class="font-heading text-xl font-bold text-earth-800 mb-2">Kategori Hierarkis & Ikon Botani</h3>
                    <p class="text-xs text-earth-600 leading-relaxed mb-4">
                        Kelompokkan transaksi ke kategori utama dan subkategori dengan ikon botani yang artistik dan palet warna yang memanjakan mata.
                    </p>
                    <span class="text-[11px] font-semibold text-purple-600 bg-purple-50 px-2.5 py-1 rounded-lg">Botanical Theme</span>
                </div>
            </div>
        </section>

        <!-- 4. Interactive Financial Savings Growth Simulator -->
        <section id="simulasi" class="py-16 bg-cream/60 border-y border-sage-200 relative"
                 x-data="{
                    monthlyIncome: 8000000,
                    monthlyExpense: 5000000,
                    get monthlySavings() {
                        return Math.max(0, this.monthlyIncome - this.monthlyExpense);
                    },
                    get savingsRate() {
                        return this.monthlyIncome > 0 ? Math.round((this.monthlySavings / this.monthlyIncome) * 100) : 0;
                    },
                    get annualSavings() {
                        return this.monthlySavings * 12;
                    },
                    get fiveYearSavings() {
                        return this.monthlySavings * 60;
                    }
                 }">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-10">
                    <span class="text-xs font-bold text-sage-600 uppercase tracking-widest bg-sage-100 px-3 py-1 rounded-full">Kalkulator Pertumbuhan</span>
                    <h2 class="font-heading text-3xl sm:text-4xl text-sage-800 font-bold mt-2">
                        Simulasi Potensi Tabungan Kebun Anda
                    </h2>
                    <p class="text-earth-600 text-sm mt-2">
                        Geser nominal pemasukan dan pengeluaran Anda untuk melihat estimasi akumulasi kekayaan di masa depan.
                    </p>
                </div>

                <div class="flora-card bg-white p-8 rounded-3xl shadow-flora-lg border border-sage-200 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <!-- Left: Sliders -->
                    <div class="space-y-6">
                        <div>
                            <div class="flex justify-between items-center text-xs font-bold text-earth-800 mb-2">
                                <span>Pemasukan Bulanan Rata-rata</span>
                                <span class="text-leaf-600 text-sm">Rp <span x-text="new Intl.NumberFormat('id-ID').format(monthlyIncome)"></span></span>
                            </div>
                            <input type="range" min="2000000" max="50000000" step="500000" x-model="monthlyIncome" class="w-full accent-leaf-500 cursor-pointer">
                        </div>

                        <div>
                            <div class="flex justify-between items-center text-xs font-bold text-earth-800 mb-2">
                                <span>Pengeluaran Bulanan Terkendali</span>
                                <span class="text-coral-600 text-sm">Rp <span x-text="new Intl.NumberFormat('id-ID').format(monthlyExpense)"></span></span>
                            </div>
                            <input type="range" min="1000000" max="40000000" step="500000" x-model="monthlyExpense" class="w-full accent-coral-400 cursor-pointer">
                        </div>

                        <div class="p-4 rounded-2xl bg-sage-50 border border-sage-100 flex items-center justify-between text-xs">
                            <span class="text-earth-600 font-medium">Rasio Tabungan Sehat (Savings Rate):</span>
                            <span class="font-bold text-sage-700 text-base" x-text="savingsRate + '%'"></span>
                        </div>
                    </div>

                    <!-- Right: Projections -->
                    <div class="p-6 rounded-2xl bg-flora-gradient text-white flex flex-col justify-between shadow-flora">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-white/80">Estimasi Tabungan Bersih</span>
                            <div class="text-2xl sm:text-3xl font-bold font-heading text-white mt-1">
                                + Rp <span x-text="new Intl.NumberFormat('id-ID').format(monthlySavings)"></span> / bulan
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 my-6 pt-4 border-t border-white/20">
                            <div>
                                <div class="text-[11px] text-white/80">Proyeksi 1 Tahun</div>
                                <div class="text-lg font-bold font-heading text-white mt-0.5">
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(annualSavings)"></span>
                                </div>
                            </div>
                            <div>
                                <div class="text-[11px] text-white/80">Proyeksi 5 Tahun</div>
                                <div class="text-lg font-bold font-heading text-white mt-0.5">
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(fiveYearSavings)"></span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('register') }}" class="w-full text-center py-2.5 px-4 bg-white text-sage-800 hover:bg-ivory rounded-xl text-xs font-bold transition-all shadow-sm">
                            Mulai Kumpulkan Tabungan Ini di Flowra →
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. Final CTA Garden Banner -->
        <section class="py-20 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flora-card bg-flora-gradient text-white p-8 sm:p-12 rounded-3xl shadow-flora-lg relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 opacity-15 pointer-events-none">
                    <x-icon name="tree" class="w-64 h-64 text-white" />
                </div>
                <div class="relative z-10 max-w-2xl mx-auto">
                    <x-icon name="sprout" class="w-12 h-12 text-white mx-auto mb-3 animate-grow" />
                    <h2 class="font-heading text-3xl sm:text-4xl font-bold text-white mb-4">
                        Siap Merawat Kebun Finansial Anda?
                    </h2>
                    <p class="text-white/90 text-sm leading-relaxed mb-8">
                        Daftar dalam waktu kurang dari 1 menit. Tanpa biaya, tanpa iklan membingungkan, dan 100% fokus pada kejernihan keuangan Anda.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <a href="{{ route('register') }}" class="w-full sm:w-auto py-3.5 px-8 bg-white text-sage-800 hover:bg-ivory rounded-xl text-sm font-bold shadow-lg transition-all">
                            Buat Akun Flowra Sekarang
                        </a>
                        <a href="{{ route('login') }}" class="w-full sm:w-auto py-3.5 px-6 border border-white/40 hover:bg-white/10 text-white rounded-xl text-sm font-semibold transition-all">
                            Sudah Memiliki Akun? Masuk
                        </a>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="border-t border-sage-200/80 bg-white/70 py-10 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-earth-500">
            <div class="flex items-center gap-2">
                <x-icon name="sprout" class="w-4 h-4 text-sage-600" />
                <span class="font-heading font-semibold text-earth-700">Flowra</span>
                <span>— Personal Finance Management System</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="#fitur" class="hover:text-sage-600 transition-colors">Fitur</a>
                <a href="#demo-live" class="hover:text-sage-600 transition-colors">Demo</a>
                <a href="{{ route('login') }}" class="hover:text-sage-600 transition-colors">Masuk</a>
                <a href="{{ route('register') }}" class="hover:text-sage-600 transition-colors">Daftar</a>
            </div>
            <div>
                &copy; {{ date('Y') }} Flowra. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>
