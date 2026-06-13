<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-sm" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
        
        <!-- Left: Logo & Branding -->
        <div class="flex items-center gap-3 flex-shrink-0">
            <a href="/" class="flex items-center gap-2">
                @php
                    $logo = \App\Models\Setting::where('key', 'logo')->value('value');
                    $storeName = \App\Models\Setting::where('key', 'store_name')->value('value') ?? \App\Models\Setting::where('key', 'web_name')->value('value') ?? 'Rayzell Store';
                @endphp
                @if($logo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($logo) }}" alt="{{ $storeName }}" class="h-8 w-auto">
                @else
                    <span class="text-xl font-black bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 bg-clip-text text-transparent tracking-tight">
                        {{ $storeName }}
                    </span>
                @endif
            </a>
            <span class="hidden sm:inline-block px-2.5 py-0.5 text-[9px] font-extrabold tracking-wider uppercase bg-blue-50 text-blue-600 rounded-full border border-blue-100">
                PPOB Premium
            </span>
        </div>

        <!-- Center: Search Bar -->
        <div class="hidden md:block flex-grow max-w-md">
            <div class="relative w-full">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari game atau pulsa..." class="w-full bg-slate-50 hover:bg-slate-100 focus:bg-white text-slate-900 placeholder-slate-400 rounded-full pl-9 pr-4 py-2 border border-slate-200/60 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition duration-200 text-xs font-semibold">
                </div>
                
                @if(!empty($search))
                    <div class="absolute w-full mt-2 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50 divide-y divide-slate-100">
                        @forelse($searchResults as $result)
                            <a href="/category/{{ $result['slug'] }}" class="flex items-center px-4 py-3 hover:bg-slate-50/80 transition duration-150">
                                @if(!empty($result['icon']))
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($result['icon']) }}" class="w-9 h-9 rounded-xl object-cover mr-3 border border-slate-100" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($result['name']) }}'">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($result['name']) }}" class="w-9 h-9 rounded-xl object-cover mr-3 border border-slate-100">
                                @endif
                                <div>
                                    <div class="font-bold text-xs text-slate-800">{{ $result['name'] }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium">Top Up Instan</div>
                                </div>
                            </a>
                        @empty
                            <div class="px-4 py-6 text-xs text-slate-400 text-center flex flex-col items-center justify-center">
                                <span class="text-base mb-1">🔍</span>
                                <span>Produk tidak ditemukan.</span>
                            </div>
                        @endforelse
                        @if(count($searchResults) > 0)
                            <a href="/search?q={{ urlencode($search) }}" class="block text-center px-4 py-2.5 text-xs text-blue-600 font-bold hover:bg-blue-50/50 transition">
                                Lihat semua hasil untuk "{{ $search }}" &rarr;
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Auth / Action Buttons -->
        <nav class="hidden md:flex items-center gap-4">
            <a href="/" class="text-xs font-bold text-slate-650 hover:text-slate-900 transition">Home</a>
            @auth
                <!-- Profile & Balance Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 text-slate-700 hover:text-slate-900 focus:outline-none bg-slate-50 hover:bg-slate-100 px-3 py-1.5 rounded-xl border border-slate-200/50 transition">
                        <div class="w-7 h-7 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold text-xs">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="text-left">
                            <p class="text-[10px] font-bold text-slate-800 leading-none mb-0.5">{{ auth()->user()->name }}</p>
                            <p class="text-[9px] text-emerald-600 font-extrabold leading-none">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-52 rounded-xl bg-white border border-slate-200 shadow-xl z-50 overflow-hidden divide-y divide-slate-100" 
                         style="display: none;">
                        <div class="p-3 bg-slate-50/50">
                            <p class="text-[9px] uppercase tracking-wider font-bold text-slate-400">Saldo Anda</p>
                            <p class="text-sm font-black text-emerald-600 mt-0.5">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-1.5 space-y-0.5">
                            @if(auth()->user()->role === 'admin')
                                <a href="/admin" class="flex items-center gap-2 px-3 py-2 text-xs font-bold text-blue-600 hover:bg-blue-50/50 rounded-lg transition">
                                    Panel Admin
                                </a>
                            @endif
                            <a href="/logout" class="flex items-center gap-2 px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50/50 rounded-lg transition">
                                Keluar
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <a href="/login" class="text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl shadow-md shadow-blue-600/10 hover:shadow-blue-600/25 transition duration-200">
                    Masuk
                </a>
            @endauth
        </nav>

        <!-- Mobile Menu Controls -->
        <div class="flex items-center gap-2 md:hidden flex-shrink-0">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-500 hover:text-slate-800 focus:outline-none p-2 rounded-lg bg-slate-50 border border-slate-200/50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!mobileMenuOpen">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="mobileMenuOpen" style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" x-transition class="md:hidden border-t border-slate-100 bg-white px-4 py-4 space-y-4" style="display: none;">
        <!-- Search inside mobile menu -->
        <div class="relative w-full">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari game atau pulsa..." class="w-full bg-slate-50 hover:bg-slate-100 focus:bg-white text-slate-900 placeholder-slate-400 rounded-full pl-9 pr-4 py-2 border border-slate-200/60 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition duration-200 text-xs font-semibold">
            </div>
            
            @if(!empty($search))
                <div class="absolute w-full mt-2 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50 divide-y divide-slate-100">
                    @forelse($searchResults as $result)
                        <a href="/category/{{ $result['slug'] }}" class="flex items-center px-4 py-3 hover:bg-slate-50/80 transition duration-150">
                            @if(!empty($result['icon']))
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($result['icon']) }}" class="w-9 h-9 rounded-xl object-cover mr-3 border border-slate-100" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($result['name']) }}'">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($result['name']) }}" class="w-9 h-9 rounded-xl object-cover mr-3 border border-slate-100">
                            @endif
                            <div>
                                <div class="font-bold text-xs text-slate-800">{{ $result['name'] }}</div>
                                <div class="text-[10px] text-slate-400 font-medium">Top Up Instan</div>
                            </div>
                        </a>
                    @empty
                        <div class="px-4 py-6 text-xs text-slate-400 text-center flex flex-col items-center justify-center">
                            <span class="text-base mb-1">🔍</span>
                            <span>Produk tidak ditemukan.</span>
                        </div>
                    @endforelse
                    @if(count($searchResults) > 0)
                        <a href="/search?q={{ urlencode($search) }}" class="block text-center px-4 py-2.5 text-xs text-blue-600 font-bold hover:bg-blue-50/50 transition">
                            Lihat semua hasil untuk "{{ $search }}" &rarr;
                        </a>
                    @endif
                </div>
            @endif
        </div>

        <a href="/" class="block text-xs font-bold text-slate-650 hover:text-slate-900 transition py-1">Home</a>
        @auth
            <div class="pt-2 border-t border-slate-100 space-y-2">
                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/50 mb-2">
                    <p class="text-[9px] uppercase tracking-wider font-bold text-slate-400">Akun Anda</p>
                    <p class="text-xs font-bold text-slate-700 mt-0.5">{{ auth()->user()->name }}</p>
                    <p class="text-xs font-black text-emerald-600 mt-0.5">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</p>
                </div>
                @if(auth()->user()->role === 'admin')
                    <a href="/admin" class="flex items-center gap-2 text-xs font-bold text-blue-600 hover:text-blue-850 transition py-1">
                        Panel Admin
                    </a>
                @endif
                <a href="/logout" class="flex items-center gap-2 text-xs font-bold text-rose-600 hover:text-rose-850 transition py-1">
                    Keluar
                </a>
            </div>
        @else
            <a href="/login" class="block text-center text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl shadow-md shadow-blue-600/10 transition">
                Masuk
            </a>
        @endauth
    </div>
</header>
