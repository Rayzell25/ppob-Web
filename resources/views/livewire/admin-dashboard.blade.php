<div class="space-y-8" wire:poll.3s x-data="{ currentTab: 'dashboard' }">
    <!-- Header Page title -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-4 border-b border-slate-900">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight bg-gradient-to-r from-blue-400 to-indigo-400 bg-clip-text text-transparent">Panel Administrasi</h1>
            <p class="text-xs text-slate-400 mt-1">Sistem manajemen dan monitoring PPOB Enterprise</p>
        </div>
        <!-- Quick indicators / server time -->
        <div class="flex items-center gap-3 bg-slate-900/60 border border-slate-800/80 px-4 py-2 rounded-2xl">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-xs font-semibold text-slate-300 font-mono">Server Time: {{ now()->format('H:i:s') }}</span>
        </div>
    </div>

    <!-- Main Sidebar Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Sidebar Navigation (Left) -->
        <div class="lg:col-span-1 space-y-3">
            <div class="bg-slate-900/40 border border-slate-900/80 rounded-3xl p-4 space-y-1.5 sticky top-24">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 mb-3">Navigasi Menu</p>
                
                <!-- Tab Dashboard Button -->
                <button 
                    @click="currentTab = 'dashboard'"
                    :class="currentTab === 'dashboard' ? 'bg-gradient-to-r from-blue-600/20 to-indigo-600/20 text-blue-400 border-l-2 border-blue-500 font-bold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border-l-2 border-transparent'"
                    class="w-full flex items-center gap-3 px-4 py-3 text-xs rounded-xl transition-all duration-200 text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"/></svg>
                    <span>Dashboard</span>
                </button>

                <!-- Tab Transaksi Button -->
                <button 
                    @click="currentTab = 'transactions'"
                    :class="currentTab === 'transactions' ? 'bg-gradient-to-r from-blue-600/20 to-indigo-600/20 text-blue-400 border-l-2 border-blue-500 font-bold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border-l-2 border-transparent'"
                    class="w-full flex items-center gap-3 px-4 py-3 text-xs rounded-xl transition-all duration-200 text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Transaksi</span>
                </button>

                <!-- Tab Provider Button -->
                <button 
                    @click="currentTab = 'providers'"
                    :class="currentTab === 'providers' ? 'bg-gradient-to-r from-blue-600/20 to-indigo-600/20 text-blue-400 border-l-2 border-blue-500 font-bold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border-l-2 border-transparent'"
                    class="w-full flex items-center gap-3 px-4 py-3 text-xs rounded-xl transition-all duration-200 text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>Provider</span>
                </button>

                <!-- Tab Pengaturan Button -->
                <button 
                    @click="currentTab = 'settings'"
                    :class="currentTab === 'settings' ? 'bg-gradient-to-r from-blue-600/20 to-indigo-600/20 text-blue-400 border-l-2 border-blue-500 font-bold' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border-l-2 border-transparent'"
                    class="w-full flex items-center gap-3 px-4 py-3 text-xs rounded-xl transition-all duration-200 text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>Pengaturan</span>
                </button>
            </div>
        </div>

        <!-- Sidebar Content (Right) -->
        <div class="lg:col-span-3 space-y-8">
            
            <!-- ====== TAB: DASHBOARD ====== -->
            <div x-show="currentTab === 'dashboard'" class="space-y-8" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                
                <!-- Welcome Banner -->
                <div class="bg-gradient-to-r from-blue-600/15 to-indigo-600/15 border border-indigo-500/20 rounded-3xl p-6 relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-lg font-bold text-white">Selamat Datang di Panel Kontrol Premium</h3>
                        <p class="text-xs text-slate-300 mt-1.5 max-w-xl leading-relaxed">Kelola transaksi, monitor provider, dan konfigurasikan halaman storefront Anda dengan efisien dan aman dari satu tempat.</p>
                    </div>
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 opacity-[0.03] hidden sm:block">
                        <svg class="w-32 h-32 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>

                <!-- Statistics Widgets -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Total Transactions -->
                    <div class="bg-gradient-to-br from-indigo-500/10 via-slate-900/40 to-slate-900 border border-slate-800 rounded-3xl p-6 relative overflow-hidden group hover:border-indigo-500/30 transition duration-300">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-550 uppercase tracking-wider font-semibold">Total Transaksi</p>
                                <h3 class="text-3xl font-extrabold text-white mt-2">{{ number_format($stats['total_transactions']) }}</h3>
                                <p class="text-[10px] text-slate-400 mt-1">Order masuk keseluruhan</p>
                            </div>
                            <div class="p-3 bg-indigo-500/10 rounded-2xl border border-indigo-500/20">
                                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Total User Balances -->
                    <div class="bg-gradient-to-br from-emerald-500/10 via-slate-900/40 to-slate-900 border border-slate-800 rounded-3xl p-6 relative overflow-hidden group hover:border-emerald-500/30 transition duration-300">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-550 uppercase tracking-wider font-semibold">Saldo Pengguna</p>
                                <h3 class="text-3xl font-extrabold text-emerald-400 mt-2">Rp {{ number_format($stats['total_user_balance'], 0, ',', '.') }}</h3>
                                <p class="text-[10px] text-slate-400 mt-1">Akumulasi deposit member</p>
                            </div>
                            <div class="p-3 bg-emerald-500/10 rounded-2xl border border-emerald-500/20">
                                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 16v1m-7-4h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Active Providers -->
                    <div class="bg-gradient-to-br from-purple-500/10 via-slate-900/40 to-slate-900 border border-slate-800 rounded-3xl p-6 relative overflow-hidden group hover:border-purple-500/30 transition duration-300">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs text-slate-550 uppercase tracking-wider font-semibold">Provider Aktif</p>
                                <h3 class="text-3xl font-extrabold text-white mt-2">{{ $stats['active_providers'] }}</h3>
                                <p class="text-[10px] text-slate-400 mt-1">Supplier operasional saat ini</p>
                            </div>
                            <div class="p-3 bg-purple-500/10 rounded-2xl border border-purple-500/20">
                                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities Card -->
                <div class="bg-slate-900/40 border border-slate-900 rounded-3xl p-6">
                    <h3 class="text-sm font-bold text-white mb-4">Ringkasan Sistem</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3.5 bg-slate-950/40 border border-slate-850 rounded-2xl">
                            <span class="text-xs text-slate-400">Total Provider Terdaftar</span>
                            <span class="text-xs font-bold text-white">{{ count($providers) }} Provider</span>
                        </div>
                        <div class="flex items-center justify-between p-3.5 bg-slate-950/40 border border-slate-850 rounded-2xl">
                            <span class="text-xs text-slate-400">Mode Sistem</span>
                            <span class="text-xs font-bold text-emerald-400">Production / Live</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ====== TAB: TRANSAKSI ====== -->
            <div x-show="currentTab === 'transactions'" class="space-y-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
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
                                <tr class="border-b border-slate-800 text-xs text-slate-500 font-semibold">
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
                                                @if($trx->status === 'success') bg-green-500/20 text-green-400 border-green-500/30
                                                @elseif($trx->status === 'failed') bg-red-500/20 text-red-400 border-red-500/30
                                                @elseif($trx->status === 'paid') bg-blue-500/20 text-blue-400 border-blue-500/30
                                                @else bg-yellow-500/20 text-yellow-400 border-yellow-500/30
                                                @endif">
                                                @if($trx->status === 'success') SUKSES
                                                @elseif($trx->status === 'failed') GAGAL
                                                @elseif($trx->status === 'paid') PAID
                                                @else PENDING
                                                @endif
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

            <!-- ====== TAB: PROVIDERS ====== -->
            <div x-show="currentTab === 'providers'" class="space-y-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <!-- PROVIDERS CRUD LIST -->
                <div class="bg-slate-900/40 border border-slate-900 rounded-3xl p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-md font-bold text-white">Manajemen Provider</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Kelola data gateway penyedia layanan PPOB</p>
                        </div>
                        <button wire:click="openNewProviderForm" class="text-xs font-bold bg-indigo-600/20 border border-indigo-500/30 text-indigo-300 hover:text-white hover:bg-indigo-600 px-3.5 py-1.5 rounded-xl transition">
                            + Tambah Provider
                        </button>
                    </div>

                    @if (session()->has('provider_success'))
                        <p class="text-xs text-emerald-400 bg-emerald-950/20 p-2.5 rounded-xl border border-emerald-900/40 font-semibold">{{ session('provider_success') }}</p>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($providers as $prov)
                            <div class="flex items-center justify-between p-4 bg-slate-950/60 border border-slate-900 rounded-2xl group hover:border-slate-800 transition">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <p class="text-xs font-bold text-white">{{ $prov->name }}</p>
                                        <span class="text-[8px] uppercase font-bold px-1.5 py-0.5 rounded border 
                                            @if($prov->is_active) bg-emerald-500/10 text-emerald-400 border-emerald-500/20
                                            @else bg-slate-900 text-slate-500 border-slate-800
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
                                    <button wire:click="toggleProviderActive({{ $prov->id }})" class="text-[10px] font-bold text-indigo-400 hover:text-indigo-350 transition">
                                        Toggle
                                    </button>
                                    <button wire:click="deleteProvider({{ $prov->id }})" class="text-[10px] font-bold text-rose-450 hover:text-rose-350 transition" onclick="confirm('Hapus provider ini?') || event.stopImmediatePropagation()">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-500 text-center py-4 col-span-2">Belum ada provider terdaftar.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- ====== TAB: SETTINGS ====== -->
            <div x-show="currentTab === 'settings'" class="space-y-6" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                <!-- SITE CONTENT SETTINGS -->
                <div class="bg-slate-900/40 border border-slate-900 rounded-3xl p-6 space-y-4">
                    <div>
                        <h3 class="text-md font-bold text-white">Pengaturan Halaman</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Konfigurasi teks, nama toko, dan data footer</p>
                    </div>
                    
                    @if (session()->has('settings_success'))
                        <p class="text-xs text-emerald-400 bg-emerald-950/20 p-2.5 rounded-xl border border-emerald-900/40 font-semibold">{{ session('settings_success') }}</p>
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

                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold py-3 rounded-xl transition">
                            Simpan Pengaturan
                        </button>
                    </form>
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
