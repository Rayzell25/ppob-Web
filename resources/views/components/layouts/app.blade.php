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
    <header class="sticky top-0 z-40 bg-slate-900/80 backdrop-blur-md border-b border-slate-800">
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
            
            <nav class="flex items-center gap-6">
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
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 border-t border-slate-900 py-6 text-center text-xs text-slate-500">
        <p class="mb-1">
            {{ \App\Models\Setting::where('key', 'store_footer')->value('value') ?? '© '.date('Y').' Rayzell Store PPOB. All rights reserved.' }}
        </p>
        <p class="text-slate-600">Built with Laravel 11 & Livewire 3</p>
    </footer>

    @livewireScripts
</body>
</html>
