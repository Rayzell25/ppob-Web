<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Rayzell Store PPOB' }}</title>
    
    <!-- Google Fonts - Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Style & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col selection:bg-indigo-500 selection:text-white">

    <!-- Header Navigation -->
    <header class="sticky top-0 z-40 bg-slate-900/80 backdrop-blur-md border-b border-slate-800" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="/" class="flex items-center gap-2">
                    <span class="text-xl font-extrabold bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-500 bg-clip-text text-transparent tracking-tight">
                        {{ \App\Models\Setting::where('key', 'store_name')->value('value') ?? 'Rayzell Store' }}
                    </span>
                </a>
                <span class="hidden sm:inline-block px-2.5 py-0.5 text-[10px] font-bold tracking-wider uppercase bg-blue-500/10 text-blue-400 rounded-full border border-blue-500/20">
                    PPOB Premium
                </span>
            </div>
            
            <!-- Desktop Navigation menu -->
            <nav class="hidden md:flex items-center gap-6">
                <a href="/" class="text-sm font-medium text-slate-300 hover:text-white transition">Home</a>
                @auth
                    <!-- Profile & Balance Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 text-slate-300 hover:text-white focus:outline-none bg-slate-800/40 hover:bg-slate-800/85 px-3 py-1.5 rounded-xl border border-slate-700/60 transition">
                            <div class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-300 flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <div class="text-left hidden sm:block">
                                <p class="text-xs font-semibold text-slate-200 leading-none mb-0.5">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] text-emerald-400 font-bold leading-none">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</p>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                             class="absolute right-0 mt-2 w-56 rounded-xl bg-slate-800 border border-slate-700 shadow-xl z-50 overflow-hidden" 
                             style="display: none;">
                            <div class="p-3 border-b border-slate-700 bg-slate-900/50">
                                <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-400">Saldo Anda</p>
                                <p class="text-base font-extrabold text-emerald-400 mt-0.5">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</p>
                            </div>
                            <div class="p-1.5 space-y-0.5">
                                @if(auth()->user()->role === 'admin')
                                    <a href="/admin" class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-indigo-300 hover:text-white hover:bg-indigo-600/30 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                        </svg>
                                        Panel Admin
                                    </a>
                                @endif
                                <a href="/" class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-slate-300 hover:text-white hover:bg-slate-700/50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    Storefront
                                </a>
                                <a href="/logout" class="flex items-center gap-2 px-3 py-2 text-xs font-semibold text-rose-400 hover:text-rose-200 hover:bg-rose-500/20 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Keluar
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="/login" class="text-sm font-medium bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl shadow-lg shadow-indigo-600/20 transition">
                        Masuk
                    </a>
                @endauth
            </nav>

            <!-- Mobile Hamburger Button -->
            <div class="flex md:hidden items-center">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-400 hover:text-white focus:outline-none p-2 rounded-lg bg-slate-800/40 border border-slate-700/60 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!mobileMenuOpen">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="mobileMenuOpen" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-show="mobileMenuOpen" x-transition class="md:hidden border-t border-slate-850 bg-slate-900 px-4 py-4 space-y-3" style="display: none;">
            <a href="/" class="block text-sm font-medium text-slate-300 hover:text-white transition py-1">Home</a>
            @auth
                <div class="pt-2 border-t border-slate-800 space-y-2">
                    <div class="bg-slate-950/40 p-3 rounded-xl border border-slate-800/80 mb-2">
                        <p class="text-[10px] uppercase tracking-wider font-semibold text-slate-500">Akun Anda</p>
                        <p class="text-xs font-semibold text-slate-300 mt-1">{{ auth()->user()->name }}</p>
                        <p class="text-xs font-extrabold text-emerald-400 mt-0.5">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</p>
                    </div>
                    @if(auth()->user()->role === 'admin')
                        <a href="/admin" class="flex items-center gap-2 text-sm font-semibold text-indigo-455 hover:text-indigo-300 transition py-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                            Panel Admin
                        </a>
                    @endif
                    <a href="/logout" class="flex items-center gap-2 text-sm font-semibold text-rose-455 hover:text-rose-350 transition py-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </a>
                </div>
            @else
                <a href="/login" class="block text-center text-sm font-medium bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2.5 rounded-xl shadow-lg shadow-indigo-600/20 transition">
                    Masuk
                </a>
            @endauth
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 border-t border-slate-900 py-10 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Social Media Links (Dynamic & Optional) -->
            @php
                $instagram = \App\Models\Setting::where('key', 'social_instagram')->value('value');
                $telegram = \App\Models\Setting::where('key', 'social_telegram')->value('value');
                $whatsapp = \App\Models\Setting::where('key', 'social_whatsapp')->value('value');
            @endphp
            
            @if(!empty($instagram) || !empty($telegram) || !empty($whatsapp))
                <div class="flex justify-center items-center space-x-6">
                    @if(!empty($instagram))
                        <a href="{{ $instagram }}" target="_blank" rel="noopener noreferrer" 
                           class="text-slate-500 hover:text-pink-500 transition duration-300 transform hover:scale-110">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                            </svg>
                        </a>
                    @endif
                    @if(!empty($telegram))
                        <a href="{{ $telegram }}" target="_blank" rel="noopener noreferrer" 
                           class="text-slate-500 hover:text-sky-400 transition duration-300 transform hover:scale-110">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.944 0C5.347 0 0 5.347 0 11.944c0 6.596 5.347 11.944 11.944 11.944 6.596 0 11.944-5.348 11.944-11.944C23.888 5.347 18.54 0 11.944 0zm5.836 8.243l-2.029 9.563c-.15.681-.557.848-1.127.525l-3.094-2.28-1.492 1.435c-.165.165-.303.303-.62.303l.222-3.148 5.733-5.18c.249-.222-.054-.345-.387-.123L7.02 14.542l-3.05-.953c-.663-.207-.677-.663.138-.982l11.93-4.6c.552-.2.1.3-.138.236z"/>
                            </svg>
                        </a>
                    @endif
                    @if(!empty($whatsapp))
                        <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" 
                           class="text-slate-500 hover:text-emerald-500 transition duration-300 transform hover:scale-110">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 11.996.01c3.2 0 6.203 1.246 8.46 3.507 2.256 2.262 3.498 5.27 3.496 8.472-.003 6.649-5.34 11.987-11.942 11.987-2.005-.001-3.973-.5-5.734-1.45L0 24zm6.59-4.846c1.66.986 3.288 1.447 4.887 1.448 5.4 0 9.79-4.387 9.793-9.783.002-2.613-1.012-5.07-2.857-6.918C16.575 2.053 14.12 1.037 11.5 1.037c-5.4 0-9.79 4.389-9.793 9.786-.001 2.023.529 4.004 1.535 5.748l-.997 3.642 3.73-.978c1.6.877 3.167 1.294 4.545 1.294v-.002zm11.758-7.904c-.31-.155-1.838-.907-2.122-1.01-.284-.104-.49-.155-.696.155-.206.31-.798.907-.978 1.11-.18.203-.36.227-.67.072-.31-.155-1.312-.483-2.5-1.543-.924-.824-1.548-1.842-1.73-2.152-.18-.31-.02-.477.136-.632.14-.14.31-.36.465-.54.155-.18.206-.31.31-.516.104-.206.05-.387-.025-.54-.077-.155-.696-1.678-.954-2.3-.25-.6-.525-.515-.72-.525-.18-.01-.387-.01-.593-.01-.206 0-.54.077-.824.387-.284.31-1.082 1.058-1.082 2.58 0 1.52 1.11 2.99 1.26 3.197.155.206 2.185 3.336 5.292 4.68.74.32 1.315.51 1.765.65.743.236 1.418.203 1.95.123.595-.088 1.838-.75 2.1-.144.26-.72.26-1.34 1.765-1.6z"/>
                            </svg>
                        </a>
                    @endif
                </div>
            @endif

            <p class="text-xs text-slate-400">
                {{ \App\Models\Setting::where('key', 'store_footer')->value('value') ?? \App\Models\Setting::where('key', 'footer_text')->value('value') ?? '© '.date('Y').' Rayzell Store PPOB. All rights reserved.' }}
            </p>
            <p class="text-slate-600 hover:text-slate-500 transition duration-300">Built with Laravel 11 & Livewire 3</p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
