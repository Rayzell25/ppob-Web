<div class="min-h-screen flex flex-col justify-center items-center">
    <!-- Frame PPOB (Tengah Layar di Desktop) -->
    <div class="w-full max-w-md mx-auto bg-white dark:bg-slate-800 min-h-screen shadow-2xl relative transition-colors duration-300">
        
        <!-- Panggil Navbar di dalam Frame agar tidak bocor -->
        <livewire:frontend.navbar />

        <!-- Live Search Bar -->
        <div class="px-4 pt-4 pb-2 relative z-40">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 dark:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari game atau pulsa..." class="w-full bg-gray-100 dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 rounded-full pl-10 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border border-transparent dark:border-slate-600 transition">
            </div>
            
            @if(!empty($search))
                <div class="absolute left-4 right-4 mt-2 bg-white dark:bg-slate-750 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-700 overflow-hidden z-50 divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($searchResults as $result)
                        <a href="/category/{{ $result['slug'] }}" class="flex items-center px-4 py-3 hover:bg-gray-50 dark:hover:bg-slate-750 transition">
                            <img src="{{ asset('storage/'.$result['icon']) }}" class="w-10 h-10 rounded-md object-cover mr-3" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($result['name']) }}'">
                            <div>
                                <div class="font-semibold text-sm text-gray-800 dark:text-white">{{ $result['name'] }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Top Up Instan</div>
                            </div>
                        </a>
                    @empty
                        <div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">Produk tidak ditemukan.</div>
                    @endforelse
                    @if(count($searchResults) > 0)
                        <a href="/search?q={{ urlencode($search) }}" class="block text-center px-4 py-2.5 text-xs text-blue-650 dark:text-blue-400 font-bold hover:bg-blue-50 dark:hover:bg-slate-700">Lihat semua hasil untuk "{{ $search }}" &rarr;</a>
                    @endif
                </div>
            @endif
        </div>

        <!-- Slider Banner Promo -->
        @if($banners->count() > 0)
        <div class="px-4 py-2">
            <div class="flex overflow-x-auto snap-x snap-mandatory hide-scrollbar space-x-3 pb-2">
                @foreach($banners as $banner)
                <div class="snap-center shrink-0 w-full rounded-2xl overflow-hidden shadow-sm relative">
                    <img src="{{ asset('storage/'.$banner->image_path) }}" alt="Promo" class="w-full h-36 object-cover">
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Grid Kategori Produk -->
        <div class="px-4 py-4 pb-24">
            <h2 class="text-lg font-bold text-gray-800 dark:text-white mb-3 flex items-center">
                <span class="text-orange-500 mr-2">🔥</span> Produk Unggulan
            </h2>
            <div class="grid grid-cols-3 gap-3">
                @foreach($categories as $category)
                <a href="/category/{{ $category->slug }}" class="block bg-white dark:bg-slate-700 rounded-xl p-2 border border-gray-100 dark:border-slate-600 shadow-sm hover:shadow-md transition text-center">
                    <img src="{{ $category->icon ? asset('storage/'.$category->icon) : 'https://ui-avatars.com/api/?name='.urlencode($category->name).'&background=random' }}" class="w-12 h-12 mx-auto rounded-xl object-cover mb-2" alt="{{ $category->name }}">
                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-200 truncate">{{ $category->name }}</p>
                </a>
                @endforeach
            </div>
        </div>

        <livewire:frontend.footer />
    </div>

    <!-- Popup Alpine -->
    @if($settings && $settings->popup_active)
    <div x-data="{ open: true }" x-show="open" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-70 px-4" x-transition.opacity>
        <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-sm w-full overflow-hidden shadow-2xl relative" @click.away="open = false">
            <button @click="open = false" class="absolute top-3 right-3 text-gray-500 dark:text-gray-300 hover:text-black dark:hover:text-white bg-gray-100 dark:bg-slate-700 rounded-full p-1 z-10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            @if($settings->popup_image)
                <img src="{{ asset('storage/'.$settings->popup_image) }}" alt="Popup" class="w-full h-auto max-h-48 object-cover">
            @endif
            <div class="p-5 text-center">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">🔔 {{ $settings->popup_title ?? 'Informasi Penting' }}</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm mb-5">{{ $settings->popup_text }}</p>
                <button @click="open = false" style="background-color: {{ $settings->popup_button_bg_color ?? '#0ea5e9' }}; color: {{ $settings->popup_button_color ?? '#ffffff' }}" class="w-full font-bold py-3 px-4 rounded-xl shadow-lg transition transform hover:scale-105">
                    {{ $settings->popup_button_text ?? 'Saya Paham' }}
                </button>
            </div>
        </div>
    </div>
    @endif
    
    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</div>
