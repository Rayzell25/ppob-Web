<div x-data="{ activeCategory: {{ $categories->first() ? $categories->first()->id : 'null' }} }" class="space-y-8">
    <!-- Popup Notification Modal -->
    @if(isset($settings['popup_active']) && $settings['popup_active'] === '1')
        <div x-data="{ 
                showPopup: false 
             }"
             x-init="
                if (sessionStorage.getItem('popup_dismissed') !== 'true') {
                    setTimeout(() => { showPopup = true; }, 800);
                }
             "
             x-show="showPopup"
             class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
             style="display: none;">
            
            <!-- Backdrop with glassmorphism -->
            <div x-show="showPopup"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-950/80 backdrop-blur-md"
                 @click="showPopup = false; sessionStorage.setItem('popup_dismissed', 'true')"></div>

            <!-- Modal Content -->
            <div x-show="showPopup"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                 class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl relative z-10 transform transition-all duration-300">
                
                <!-- Close Button -->
                <button @click="showPopup = false; sessionStorage.setItem('popup_dismissed', 'true')" 
                        class="absolute right-4 top-4 text-slate-400 hover:text-white bg-slate-950/60 hover:bg-slate-950 p-2 rounded-full border border-slate-800/80 z-20 transition">
                    ✕
                </button>

                <!-- Popup Content Header & Body -->
                <div class="flex flex-col">
                    @if(!empty($settings['popup_image']))
                        <div class="w-full aspect-video overflow-hidden border-b border-slate-800">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($settings['popup_image']) }}" 
                                 alt="Popup Promotion" 
                                 class="w-full h-full object-cover object-center" />
                        </div>
                    @endif

                    <div class="p-6 md:p-8 space-y-4">
                        @if(!empty($settings['popup_title']))
                            <h3 class="text-xl font-extrabold text-white tracking-tight">
                                {{ $settings['popup_title'] }}
                            </h3>
                        @endif

                        @if(!empty($settings['popup_text']))
                            <div class="text-sm text-slate-300 leading-relaxed whitespace-pre-line max-h-48 overflow-y-auto pr-2 scrollbar-thin">
                                {{ $settings['popup_text'] }}
                            </div>
                        @endif

                        <!-- Action Button -->
                        <div class="pt-2">
                            <button @click="showPopup = false; sessionStorage.setItem('popup_dismissed', 'true')"
                                    style="
                                        background-color: {{ $settings['popup_button_bg_color'] ?: '#4f46e5' }}; 
                                        color: {{ $settings['popup_button_color'] ?: '#ffffff' }};
                                    "
                                    class="w-full font-bold text-sm py-3.5 rounded-xl shadow-lg hover:brightness-110 active:scale-[0.98] transition-all duration-200">
                                {{ $settings['popup_button_text'] ?: 'Saya Paham' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Banner Slider -->
    @if(isset($banners) && $banners->isNotEmpty())
        <div x-data="{ 
                activeSlide: 0,
                slidesCount: {{ $banners->count() }},
                autoplayInterval: null,
                startAutoplay() {
                    this.autoplayInterval = setInterval(() => {
                        this.activeSlide = (this.activeSlide + 1) % this.slidesCount;
                    }, 5000);
                },
                stopAutoplay() {
                    if (this.autoplayInterval) {
                        clearInterval(this.autoplayInterval);
                    }
                }
             }"
             x-init="startAutoplay()"
             @mouseenter="stopAutoplay()"
             @mouseleave="startAutoplay()"
             class="relative rounded-3xl overflow-hidden shadow-2xl border border-slate-800 bg-slate-900 group">
            
            <!-- Slides wrapper -->
            <div class="relative w-full overflow-hidden aspect-[21/9] sm:aspect-[21/8] md:aspect-[21/7]">
                @foreach($banners as $index => $banner)
                    <div x-show="activeSlide === {{ $index }}"
                         x-transition:enter="transition ease-out duration-700"
                         x-transition:enter-start="opacity-0 transform translate-x-full"
                         x-transition:enter-end="opacity-100 transform translate-x-0"
                         x-transition:leave="transition ease-in duration-700"
                         x-transition:leave-start="opacity-100 transform translate-x-0"
                         x-transition:leave-end="opacity-0 transform -translate-x-full"
                         class="absolute inset-0 w-full h-full">
                        
                        <!-- Image with subtle overlay -->
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($banner->image_path) }}" 
                             alt="{{ $banner->title ?? 'Banner Slide' }}"
                             class="w-full h-full object-cover object-center" />
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent flex flex-col justify-end p-6 md:p-10">
                            @if($banner->title)
                                <h2 class="text-xl md:text-3xl font-extrabold text-white tracking-tight drop-shadow-md">
                                    {{ $banner->title }}
                                </h2>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Navigation Arrows -->
            <button @click="activeSlide = (activeSlide - 1 + slidesCount) % slidesCount"
                    class="absolute left-4 top-1/2 -translate-y-1/2 bg-slate-950/60 hover:bg-slate-950/90 text-white p-3 rounded-full border border-slate-800 opacity-0 group-hover:opacity-100 transition-all duration-300 focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="activeSlide = (activeSlide + 1) % slidesCount"
                    class="absolute right-4 top-1/2 -translate-y-1/2 bg-slate-950/60 hover:bg-slate-950/90 text-white p-3 rounded-full border border-slate-800 opacity-0 group-hover:opacity-100 transition-all duration-300 focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- Navigation Dots -->
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex items-center space-x-2">
                @foreach($banners as $index => $banner)
                    <button @click="activeSlide = {{ $index }}"
                            :class="activeSlide === {{ $index }} ? 'w-6 bg-blue-500' : 'w-2 bg-slate-500/60 hover:bg-slate-400'"
                            class="h-2 rounded-full transition-all duration-300 focus:outline-none"></button>
                @endforeach
            </div>
        </div>
    @endif

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
