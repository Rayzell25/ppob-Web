<div class="space-y-8" wire:poll.3s>
    <!-- Statistics Widget Header -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Transactions -->
        <div class="bg-slate-900/40 border border-slate-900 rounded-3xl p-6 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-indigo-500/5 rounded-full blur-xl group-hover:bg-indigo-500/10 transition"></div>
            <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Total Transaksi</p>
            <h3 class="text-3xl font-extrabold text-white mt-2">{{ number_format($stats['total_transactions']) }}</h3>
            <p class="text-xs text-slate-400 mt-1">Order masuk keseluruhan</p>
        </div>

        <!-- Total User Balances -->
        <div class="bg-slate-900/40 border border-slate-900 rounded-3xl p-6 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-xl group-hover:bg-emerald-500/10 transition"></div>
            <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Saldo Pengguna</p>
            <h3 class="text-3xl font-extrabold text-emerald-400 mt-2">Rp {{ number_format($stats['total_user_balance'], 0, ',', '.') }}</h3>
            <p class="text-xs text-slate-400 mt-1">Akumulasi deposit member</p>
        </div>

        <!-- Active Providers -->
        <div class="bg-slate-900/40 border border-slate-900 rounded-3xl p-6 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-purple-500/5 rounded-full blur-xl group-hover:bg-purple-500/10 transition"></div>
            <p class="text-xs text-slate-500 uppercase tracking-wider font-semibold">Provider Aktif</p>
            <h3 class="text-3xl font-extrabold text-white mt-2">{{ $stats['active_providers'] }}</h3>
            <p class="text-xs text-slate-400 mt-1">Supplier operasional saat ini</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- LEFT COLUMN: TRANSACTION MONITOR (Real-time) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-slate-900/40 border border-slate-900 rounded-3xl p-6 space-y-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-white">Monitor Transaksi Real-time</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Memantau orderan masuk (otomatis diperbarui setiap 3 detik)</p>
                    </div>
                    
                    <!-- Search & Filter Actions -->
                    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                        <input type="text" wire:model.live="search" class="bg-slate-950 border border-slate-800 hover:border-slate-700 focus:border-indigo-500 rounded-xl px-3 py-2 text-xs text-white focus:outline-none w-full sm:w-48 transition" placeholder="Cari ref id, target..." />
                        
                        <select wire:model.live="status_filter" class="bg-slate-950 border border-slate-800 hover:border-slate-700 focus:border-indigo-500 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="success">Success</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-900 text-xs text-slate-500 font-semibold">
                                <th class="pb-3 pr-2">Tanggal / Ref ID</th>
                                <th class="pb-3 pr-2">Produk</th>
                                <th class="pb-3 pr-2">Tujuan</th>
                                <th class="pb-3 pr-2">Status</th>
                                <th class="pb-3">SN / Detail</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs divide-y divide-slate-900/50">
                            @forelse($transactions as $trx)
                                <tr class="hover:bg-slate-900/10 transition">
                                    <td class="py-4 pr-2">
                                        <p class="text-slate-400 font-mono">{{ $trx->created_at->format('d/m H:i') }}</p>
                                        <p class="font-bold text-slate-300 font-mono mt-0.5">{{ $trx->reference_id }}</p>
                                    </td>
                                    <td class="py-4 pr-2">
                                        <p class="font-bold text-white">{{ $trx->product_name }}</p>
                                        <p class="text-[10px] text-slate-500 mt-0.5">{{ $trx->provider ? $trx->provider->name : 'No Provider' }}</p>
                                    </td>
                                    <td class="py-4 pr-2">
                                        <p class="font-bold text-slate-300 font-mono">{{ $trx->target }}</p>
                                        <p class="text-[10px] text-indigo-400 mt-0.5">{{ $trx->user->name }}</p>
                                    </td>
                                    <td class="py-4 pr-2">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border 
                                            @if($trx->status === 'success') bg-emerald-500/10 text-emerald-400 border-emerald-500/20
                                            @elseif($trx->status === 'failed') bg-rose-500/10 text-rose-400 border-rose-500/20
                                            @elseif($trx->status === 'paid') bg-indigo-500/10 text-indigo-400 border-indigo-500/20
                                            @else bg-yellow-500/10 text-yellow-400 border-yellow-500/20
                                            @endif">
                                            {{ strtoupper($trx->status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 font-mono text-slate-400 max-w-xs truncate">
                                        <p class="text-white">{{ $trx->serial_number ?? '-' }}</p>
                                        <p class="text-[10px] text-slate-500 mt-0.5 truncate">{{ $trx->message ?? '' }}</p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-500">
                                        Tidak ada data transaksi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pt-4 border-t border-slate-900/60">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: SETTINGS & PROVIDERS CRUD -->
        <div class="space-y-8">
            <!-- SITE CONTENT SETTINGS -->
            <div class="bg-slate-900/40 border border-slate-900 rounded-3xl p-6 space-y-4">
                <h3 class="text-md font-bold text-white">Pengaturan Halaman</h3>
                
                @if (session()->has('settings_success'))
                    <p class="text-xs text-emerald-400 bg-emerald-950/20 p-2 rounded-xl border border-emerald-900/40 font-semibold">{{ session('settings_success') }}</p>
                @endif
                
                <form wire:submit.prevent="saveSettings" class="space-y-4">
                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-slate-500">Nama Toko</label>
                        <input type="text" wire:model="store_name" class="w-full bg-slate-950 border border-slate-800 hover:border-slate-700 focus:border-indigo-500 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none transition" />
                    </div>
                    
                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-slate-500">Footer Copyright</label>
                        <input type="text" wire:model="store_footer" class="w-full bg-slate-950 border border-slate-800 hover:border-slate-700 focus:border-indigo-500 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none transition" />
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold py-2.5 rounded-xl transition">
                        Simpan Pengaturan
                    </button>
                </form>
            </div>

            <!-- PROVIDERS CRUD LIST -->
            <div class="bg-slate-900/40 border border-slate-900 rounded-3xl p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-md font-bold text-white">Manajemen Provider</h3>
                    <button wire:click="openNewProviderForm" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 transition">
                        + Tambah
                    </button>
                </div>

                @if (session()->has('provider_success'))
                    <p class="text-xs text-emerald-400 bg-emerald-950/20 p-2 rounded-xl border border-emerald-900/40 font-semibold">{{ session('provider_success') }}</p>
                @endif

                <div class="space-y-3">
                    @forelse($providers as $prov)
                        <div class="flex items-center justify-between p-3.5 bg-slate-950/60 border border-slate-900 rounded-2xl group">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <p class="text-xs font-bold text-white">{{ $prov->name }}</p>
                                    <span class="text-[8px] uppercase font-bold px-1.5 py-0.5 rounded border 
                                        @if($prov->is_active) bg-emerald-500/10 text-emerald-400 border-emerald-500/20
                                        @else bg-slate-900 text-slate-500 border-slate-850
                                        @endif">
                                        {{ $prov->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <p class="text-[10px] text-slate-500 font-mono">{{ $prov->code }} | {{ strtoupper($prov->type) }}</p>
                            </div>
                            
                            <div class="flex items-center gap-2 opacity-50 group-hover:opacity-100 transition">
                                <button wire:click="editProvider({{ $prov->id }})" class="text-[10px] font-bold text-slate-400 hover:text-white transition">
                                    Edit
                                </button>
                                <button wire:click="toggleProviderActive({{ $prov->id }})" class="text-[10px] font-bold text-indigo-400 hover:text-indigo-300 transition">
                                    Toggle
                                </button>
                                <button wire:click="deleteProvider({{ $prov->id }})" class="text-[10px] font-bold text-rose-400 hover:text-rose-300 transition" onclick="confirm('Hapus provider ini?') || event.stopImmediatePropagation()">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 text-center py-4">Belum ada provider terdaftar.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- PROVIDER MODAL FORM -->
    @if($show_provider_form)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" wire:click="resetProviderForm"></div>

            <!-- Modal Content -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl relative z-10">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-6">
                    <h3 class="text-lg font-bold text-white">{{ $editing_provider_id ? 'Edit Provider' : 'Tambah Provider' }}</h3>
                    <button wire:click="$set('show_provider_form', false)" class="text-slate-400 hover:text-white transition">
                        ✕
                    </button>
                </div>

                <form wire:submit.prevent="saveProvider" class="space-y-4">
                    <!-- Name -->
                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-slate-500">Nama Provider</label>
                        <input type="text" wire:model="provider_name" class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none transition" />
                        @error('provider_name') <span class="text-[10px] text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Code & Type -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase font-bold text-slate-500">Kode Provider</label>
                            <input type="text" wire:model="provider_code" class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none transition" placeholder="e.g. df" />
                            @error('provider_code') <span class="text-[10px] text-rose-500 font-semibold">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] uppercase font-bold text-slate-500">Tipe / Gateway</label>
                            <select wire:model="provider_type" class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none transition">
                                <option value="digiflazz">Digiflazz</option>
                                <option value="kmsp">KMSP</option>
                                <option value="custom">Custom</option>
                                <option value="generic">Generic</option>
                            </select>
                            @error('provider_type') <span class="text-[10px] text-rose-500 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- API Username & API Key -->
                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-slate-500">API Username / Merchant ID</label>
                        <input type="text" wire:model="provider_api_username" class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none transition" />
                        @error('provider_api_username') <span class="text-[10px] text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-slate-500">API Key / Secret</label>
                        <input type="password" wire:model="provider_api_key" class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none transition" />
                        @error('provider_api_key') <span class="text-[10px] text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- API URL -->
                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-slate-500">Base API URL</label>
                        <input type="text" wire:model="provider_api_url" class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none transition" placeholder="https://..." />
                        @error('provider_api_url') <span class="text-[10px] text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Balance -->
                    <div class="space-y-1">
                        <label class="text-[10px] uppercase font-bold text-slate-500">Saldo Awal</label>
                        <input type="number" wire:model="provider_balance" class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none transition" />
                        @error('provider_balance') <span class="text-[10px] text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status Active Toggle -->
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="provider_is_active" wire:model="provider_is_active" class="bg-slate-950 border border-slate-800 rounded focus:ring-0 focus:ring-offset-0 text-indigo-600 h-4 w-4" />
                        <label for="provider_is_active" class="text-xs font-semibold text-slate-300">Set Aktif (Operasional)</label>
                        @error('provider_is_active') <span class="text-[10px] text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 flex items-center gap-3">
                        <button type="button" wire:click="resetProviderForm" class="flex-1 bg-slate-950 hover:bg-slate-900 border border-slate-800 text-slate-300 text-xs font-semibold py-3 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold py-3 rounded-xl shadow-lg shadow-indigo-600/20 transition">
                            Simpan Provider
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
