<nav class="sticky top-0 z-50 bg-white dark:bg-slate-800 border-b border-gray-100 dark:border-slate-700 shadow-sm transition-colors duration-300">
    <div class="px-4 py-3 flex justify-between items-center">
        <!-- Logo / Nama Web -->
        <a href="/" class="text-xl font-bold text-blue-600 dark:text-blue-400">
            {{ \App\Models\Setting::where('key', 'store_name')->value('value') ?? \App\Models\Setting::where('key', 'web_name')->value('value') ?? 'Rayzell Store' }}
        </a>

        <!-- Menu Kanan: Switcher & Tombol Login -->
        <div class="flex items-center space-x-3">
            <!-- Theme Switcher Button -->
            <button @click="theme = theme === 'dark' ? 'light' : 'dark'" class="p-2 rounded-full bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-yellow-400 hover:bg-gray-200 dark:hover:bg-slate-600 transition">
                <!-- Ikon Bulan (Tampil saat Light Mode) -->
                <svg x-show="theme === 'light'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                <!-- Ikon Matahari (Tampil saat Dark Mode) -->
                <svg x-show="theme === 'dark'" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </button>
            
            @auth
                <!-- Profile / Logout or Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" class="flex items-center space-x-2 text-gray-700 dark:text-gray-200 focus:outline-none">
                        <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-slate-700 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-xs">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                    </button>
                    <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-gray-100 dark:border-slate-700 py-1 z-50" style="display: none;">
                        <div class="px-4 py-2 border-b border-gray-100 dark:border-slate-700">
                            <p class="text-xs font-semibold text-gray-800 dark:text-white">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-emerald-500 font-bold">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</p>
                        </div>
                        @if(auth()->user()->role === 'admin')
                            <a href="/admin" class="block px-4 py-2 text-xs text-blue-600 dark:text-blue-400 hover:bg-gray-50 dark:hover:bg-slate-700">Panel Admin</a>
                        @endif
                        <a href="/logout" class="block px-4 py-2 text-xs text-rose-600 dark:text-rose-400 hover:bg-gray-50 dark:hover:bg-slate-700">Keluar</a>
                    </div>
                </div>
            @else
                <a href="/login" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-full text-sm font-semibold transition shadow-sm">Masuk</a>
            @endauth
        </div>
    </div>
</nav>
