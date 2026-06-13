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
    <header class="sticky top-0 z-40 bg-slate-950/80 backdrop-blur-md border-b border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="/" class="flex items-center gap-2">
                    <span class="text-xl font-extrabold bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent tracking-tight">
                        {{ \App\Models\Setting::where('key', 'store_name')->value('value') ?? 'Rayzell Store' }}
                    </span>
                </a>
                <span class="hidden sm:inline-block px-2.5 py-0.5 text-xs font-semibold bg-indigo-500/10 text-indigo-400 rounded-full border border-indigo-500/20">
                    PPOB v11
                </span>
            </div>
            
            <nav class="flex items-center gap-6">
                <a href="/" class="text-sm font-medium text-slate-300 hover:text-white transition">Home</a>
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="/admin" class="text-sm font-medium text-indigo-400 hover:text-indigo-300 transition">Admin Dashboard</a>
                    @endif
                    <div class="flex items-center gap-3 pl-4 border-l border-slate-900">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs text-slate-400">Saldo Anda</p>
                            <p class="text-sm font-bold text-emerald-400">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</p>
                        </div>
                        <form method="POST" action="/logout" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-medium text-slate-400 hover:text-rose-400 border border-slate-900 hover:border-rose-950 px-3 py-1.5 rounded-lg transition bg-slate-900/50">
                                Keluar
                            </button>
                        </form>
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
