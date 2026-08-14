<section class="space-y-6">
    <header>
        <div class="flex items-center gap-2">
            <x-icon name="delete-wilt" class="w-6 h-6 text-amber-500" />
            <h2 class="font-heading text-lg font-bold text-earth-800">
                Reset Semua Data Keuangan
            </h2>
        </div>

        <p class="mt-1 text-xs text-earth-600">
            Hapus seluruh transaksi, rekening, anggaran, hutang & piutang, dan riwayat keuangan Anda secara permanen. Akun profil dan autentikasi Anda akan tetap aman sehingga Anda dapat mulai mencatat dari awal tanpa perlu register ulang.
        </p>
    </header>

    <div>
        <button type="button"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-financial-reset')"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 active:bg-amber-800 text-white text-xs font-semibold rounded-xl shadow-sm hover:shadow transition-all duration-200">
            <x-icon name="delete-wilt" class="w-4 h-4 text-white" />
            <span>Reset Data Keuangan (Mulai dari Awal)</span>
        </button>
    </div>

    <x-modal name="confirm-financial-reset" :show="$errors->financialReset->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.reset-financial-data') }}" class="p-6">
            @csrf

            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <x-icon name="delete-wilt" class="w-6 h-6 text-amber-600" />
                </div>
                <div>
                    <h2 class="font-heading text-lg font-bold text-earth-800">
                        Reset Semua Data Keuangan?
                    </h2>
                    <p class="text-xs text-earth-500">Tindakan berbahaya dan permanen</p>
                </div>
            </div>

            <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 text-xs text-earth-700 space-y-2 mb-4">
                <p class="font-semibold text-amber-800">
                    Perhatian: Tindakan ini akan menghapus seluruh data finansial berikut secara permanen:
                </p>
                <ul class="list-disc list-inside space-y-1 text-earth-600 pl-1">
                    <li>Semua riwayat transaksi (pemasukan, pengeluaran, transfer)</li>
                    <li>Semua rekening & dompet (saldo kembali Rp 0)</li>
                    <li>Semua catatan hutang, piutang & riwayat pembayarannya</li>
                    <li>Semua target anggaran bulanan & transaksi rutin</li>
                    <li>Semua bukti transfer / nota yang pernah diunggah</li>
                </ul>
                <p class="text-[11px] text-amber-900 font-medium pt-1">
                    ✓ Akun login Anda (nama, email, dan kata sandi) akan <strong>tetap ada</strong>.
                </p>
            </div>

            <div class="space-y-2">
                <label for="reset_password" class="block text-xs font-semibold text-earth-700">
                    Masukkan kata sandi Anda untuk mengonfirmasi:
                </label>

                <input id="reset_password"
                       name="password"
                       type="password"
                       required
                       class="flora-input text-sm w-full @error('password', 'financialReset') border-coral-400 @enderror"
                       placeholder="Kata sandi akun Anda" />

                <x-input-error :messages="$errors->financialReset->get('password')" class="mt-1 text-xs" />
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-sage-100">
                <x-secondary-button x-on:click="$dispatch('close')" type="button">
                    Batal
                </x-secondary-button>

                <button type="submit" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-coral-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-coral-700 active:bg-coral-800 focus:outline-none focus:ring-2 focus:ring-coral-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md">
                    <x-icon name="delete-wilt" class="w-4 h-4 text-white" />
                    <span>Ya, Hapus Semua Data Keuangan</span>
                </button>
            </div>
        </form>
    </x-modal>
</section>
