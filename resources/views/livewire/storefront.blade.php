<div x-data="{ activeCategory: {{ $categories->first() ? $categories->first()->id : 'null' }} }" class="space-y-8">
    <!-- Alert Messages -->
    @if (session()->has('success'))
        <div class="bg-emerald-950/40 border border-emerald-900/60 text-emerald-400 p-4 rounded-2xl flex items-start gap-3 shadow-lg shadow-emerald-950/10">
            <span class="text-lg">✅</span>
            <div>
                <p class="font-bold text-sm text-emerald-300">Sukses</p>
                <p class="text-xs text-slate-300 mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-rose-950/40 border border-rose-900/60 text-rose-400 p-4 rounded-2xl flex items-start gap-3 shadow-lg shadow-rose-950/10">
            <span class="text-lg">❌</span>
            <div>
                <p class="font-bold text-sm text-rose-300">Gagal</p>
                <p class="text-xs text-slate-300 mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Category Tabs (Horizontal Scroll) -->
    <div class="bg-slate-900/60 border border-slate-800/80 p-2 rounded-2xl sticky top-20 z-30 backdrop-blur-md">
        <div class="flex items-center space-x-2 overflow-x-auto pb-1 scrollbar-none">
            @foreach($categories as $category)
                <button 
                    @click="activeCategory = {{ $category->id }}"
                    :class="activeCategory === {{ $category->id }} ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-indigo-600/35 border-transparent' : 'bg-slate-950/40 text-slate-400 hover:text-slate-200 hover:bg-slate-800 border-slate-800'"
                    class="px-5 py-2.5 rounded-xl text-xs font-bold tracking-wide uppercase transition duration-200 border whitespace-nowrap flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full" :class="activeCategory === {{ $category->id }} ? 'bg-white' : 'bg-slate-600'"></span>
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Catalog Sections -->
    @foreach($categories as $category)
        <div x-show="activeCategory === {{ $category->id }}" class="space-y-8" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
            @foreach($category->brands as $brand)
                @if($brand->products->isNotEmpty())
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <h3 class="text-xs font-bold tracking-wider text-slate-500 uppercase">{{ $brand->name }}</h3>
                            <div class="h-[1px] flex-grow bg-slate-900"></div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach($brand->products as $product)
                                <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5 hover:border-blue-500 hover:shadow-2xl hover:shadow-indigo-950/20 transition-all duration-305 flex flex-col justify-between group relative overflow-hidden">
                                    <div class="absolute -right-16 -top-16 w-32 h-32 bg-indigo-500/5 rounded-full blur-2xl group-hover:bg-indigo-500/10 transition-all duration-300"></div>
                                    <div class="space-y-3 relative z-10">
                                        <div class="flex items-start justify-between gap-2">
                                            <h4 class="font-extrabold text-sm text-white group-hover:text-blue-400 transition-colors duration-200">{{ $product->name }}</h4>
                                            <span class="text-[9px] font-mono font-bold bg-slate-900/80 text-blue-400 px-2 py-0.5 rounded-lg border border-slate-700/60 uppercase tracking-wider">
                                                {{ $product->sku }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-400 leading-relaxed">{{ $product->description ?? 'Layanan pengisian pulsa/data instan otomatis.' }}</p>
                                    </div>
                                    
                                    <div class="flex items-center justify-between mt-6 pt-4 border-t border-slate-700/50 relative z-10">
                                        <div>
                                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">Harga</p>
                                            <p class="text-base font-extrabold text-emerald-400">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                        </div>
                                        <button wire:click="selectProduct({{ $product->id }})" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs px-4 py-2.5 rounded-xl shadow-lg shadow-indigo-600/10 hover:shadow-indigo-600/35 transition-all duration-200">
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
                            <span class="text-xs font-extrabold text-emerald-400">Rp {{ number_format($selected_product->price, 0, ',', '.') }}</span>
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
