<div class="space-y-12">
    <!-- Quick Login Helper Bar (for local development testing) -->
    <div class="bg-slate-900/40 border border-slate-900 rounded-2xl p-4 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="flex h-3 w-3 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-indigo-500"></span>
            </span>
            <p class="text-sm text-slate-300 font-medium">
                @auth
                    Masuk sebagai: <strong class="text-indigo-400">{{ auth()->user()->name }}</strong> ({{ ucfirst(auth()->user()->role) }})
                @else
                    Mode Demo: Anda belum masuk. Gunakan tombol cepat di samping untuk uji coba.
                @endauth
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="quickLogin('user')" class="text-xs font-semibold px-3.5 py-1.5 bg-indigo-600/10 hover:bg-indigo-600/20 text-indigo-400 rounded-xl border border-indigo-500/20 transition">
                ⚡ Login Pembeli (User)
            </button>
            <button wire:click="quickLogin('admin')" class="text-xs font-semibold px-3.5 py-1.5 bg-purple-600/10 hover:bg-purple-600/20 text-purple-400 rounded-xl border border-purple-500/20 transition">
                ⚡ Login Pemilik (Admin)
            </button>
            @auth
                <button wire:click="quickLogin('logout')" class="text-xs font-semibold px-3.5 py-1.5 bg-rose-600/10 hover:bg-rose-600/20 text-rose-400 rounded-xl border border-rose-500/20 transition">
                    🚪 Logout
                </button>
            @endauth
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('success'))
        <div class="bg-emerald-950/40 border border-emerald-900 text-emerald-400 p-4 rounded-2xl flex items-start gap-3 shadow-lg shadow-emerald-950/10">
            <span class="text-lg">✅</span>
            <div>
                <p class="font-semibold">Sukses</p>
                <p class="text-sm text-slate-300 mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-rose-950/40 border border-rose-900 text-rose-400 p-4 rounded-2xl flex items-start gap-3 shadow-lg shadow-rose-950/10">
            <span class="text-lg">❌</span>
            <div>
                <p class="font-semibold">Gagal</p>
                <p class="text-sm text-slate-300 mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Catalog Sections -->
    @foreach($categories as $category)
        <div class="space-y-6">
            <div class="flex items-center gap-4">
                <h2 class="text-2xl font-bold tracking-tight text-white">{{ $category->name }}</h2>
                <div class="h-[1px] flex-grow bg-slate-900"></div>
            </div>

            @foreach($category->brands as $brand)
                @if($brand->products->isNotEmpty())
                    <div class="space-y-4">
                        <h3 class="text-sm font-semibold text-slate-400 tracking-wider uppercase">{{ $brand->name }}</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach($brand->products as $product)
                                <div class="bg-slate-900/40 border border-slate-900 rounded-2xl p-5 hover:border-slate-800 hover:shadow-xl hover:shadow-slate-950/40 transition flex flex-col justify-between group">
                                    <div class="space-y-2">
                                        <div class="flex items-start justify-between gap-2">
                                            <h4 class="font-bold text-white group-hover:text-indigo-400 transition">{{ $product->name }}</h4>
                                            <span class="text-[10px] font-mono bg-slate-900 text-slate-400 px-2 py-0.5 rounded border border-slate-800">
                                                {{ $product->sku }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-400 line-clamp-2">{{ $product->description ?? 'Layanan pengisian pulsa/data instan otomatis.' }}</p>
                                    </div>
                                    
                                    <div class="flex items-center justify-between mt-6 pt-4 border-t border-slate-900">
                                        <div>
                                            <p class="text-[10px] text-slate-500">Harga</p>
                                            <p class="text-lg font-extrabold text-indigo-400">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                        </div>
                                        <button wire:click="selectProduct({{ $product->id }})" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg shadow-indigo-600/10 hover:shadow-indigo-600/20 transition duration-150">
                                            Beli Sekarang
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach

    <!-- Modal Order Box -->
    @if($show_modal && $selected_product)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" wire:click="$set('show_modal', false)"></div>

            <!-- Modal Content -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl relative z-10 transform scale-100 transition duration-200">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-6">
                    <h3 class="text-lg font-bold text-white">Konfirmasi Pembelian</h3>
                    <button wire:click="$set('show_modal', false)" class="text-slate-400 hover:text-white transition">
                        ✕
                    </button>
                </div>

                <div class="space-y-4">
                    <!-- Product Details Summary -->
                    <div class="bg-slate-950/50 border border-slate-800/80 rounded-2xl p-4 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-xs text-slate-400">Produk</span>
                            <span class="text-xs font-bold text-white">{{ $selected_product->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-xs text-slate-400">Harga</span>
                            <span class="text-xs font-extrabold text-indigo-400">Rp {{ number_format($selected_product->price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Input Target Phone/ID -->
                    <div class="space-y-2">
                        <label for="target_number" class="text-xs font-semibold text-slate-300">Nomor Tujuan / ID Pelanggan</label>
                        <input type="text" id="target_number" wire:model="target_number" class="w-full bg-slate-950 border border-slate-800 hover:border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-sm text-white focus:outline-none transition" placeholder="Masukkan nomor handphone atau ID..." />
                        @error('target_number') <span class="text-[10px] text-rose-500 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 flex items-center gap-3">
                        <button wire:click="$set('show_modal', false)" class="flex-1 bg-slate-950 hover:bg-slate-900 border border-slate-800 text-slate-300 text-sm font-semibold py-3 rounded-xl transition">
                            Batal
                        </button>
                        <button wire:click="processOrder" class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold py-3 rounded-xl shadow-lg shadow-indigo-600/20 transition">
                            Bayar & Kirim
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
