<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - {{ \App\Models\Setting::where('key', 'store_name')->value('value') ?? \App\Models\Setting::where('key', 'web_name')->value('value') ?? 'Rayzell Store' }}</title>
    
    <!-- Dark Mode Init Script -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    <!-- Google Fonts - Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Style & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-slate-100 transition-colors duration-300 min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 selection:bg-indigo-500 selection:text-white relative">

    <!-- Theme Toggle at Top Right -->
    <div class="fixed top-4 right-4 z-50">
        <button x-data="{ theme: localStorage.theme || 'light' }" @click="
            if (localStorage.theme === 'dark') {
                localStorage.theme = 'light';
                theme = 'light';
                document.documentElement.classList.remove('dark');
            } else {
                localStorage.theme = 'dark';
                theme = 'dark';
                document.documentElement.classList.add('dark');
            }
        " class="p-2.5 rounded-full bg-white dark:bg-slate-800 text-gray-600 dark:text-yellow-400 hover:bg-gray-100 dark:hover:bg-slate-700 transition shadow border border-gray-200 dark:border-slate-700">
            <!-- Moon icon (when theme is light, toggle to dark) -->
            <svg x-show="theme === 'light'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            <!-- Sun icon (when theme is dark, toggle to light) -->
            <svg x-show="theme === 'dark'" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </button>
    </div>

    <div class="w-full max-w-md space-y-6">
        
        <!-- Logo / Title -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-blue-600 dark:text-blue-400 tracking-tight">
                {{ \App\Models\Setting::where('key', 'store_name')->value('value') ?? \App\Models\Setting::where('key', 'web_name')->value('value') ?? 'Rayzell Store' }}
            </h1>
            <p class="text-xs text-gray-600 dark:text-gray-300 mt-1.5">Daftar Akun Baru</p>
        </div>

        <!-- Card Form -->
        <div class="w-full max-w-md space-y-8 bg-white dark:bg-slate-800 rounded-2xl p-8 border border-gray-200 dark:border-slate-700 shadow-2xl relative overflow-hidden transition-colors duration-300">
            <div class="absolute -right-20 -top-20 w-40 h-40 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <form method="POST" action="/register" class="space-y-4 relative z-10">
                @csrf
                
                @if ($errors->any())
                    <div class="bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 p-3 rounded-xl text-xs font-semibold">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Name Input -->
                <div class="space-y-1.5">
                    <label for="name" class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none transition" 
                        placeholder="Nama lengkap Anda..." />
                </div>

                <!-- Email Input -->
                <div class="space-y-1.5">
                    <label for="email" class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none transition" 
                        placeholder="nama@email.com" />
                </div>

                <!-- Phone Input -->
                <div class="space-y-1.5">
                    <label for="phone" class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide">No. WhatsApp (Aktif)</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required
                        class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none transition" 
                        placeholder="e.g. 08123456789" />
                </div>

                <!-- Password Input -->
                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Kata Sandi</label>
                    <input type="password" id="password" name="password" required
                        class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none transition" 
                        placeholder="••••••••" />
                </div>

                <!-- Password Confirmation Input -->
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Konfirmasi Kata Sandi</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none transition" 
                        placeholder="••••••••" />
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-sm py-3 rounded-xl shadow-lg shadow-indigo-600/10 hover:shadow-indigo-600/35 transition-all duration-200 mt-2">
                    Daftar Sekarang
                </button>
                
                <div class="text-center mt-4">
                    <p class="text-xs text-gray-600 dark:text-gray-300">
                        Sudah punya akun? <a href="/login" class="text-blue-600 dark:text-blue-400 hover:text-blue-550 dark:hover:text-blue-300 font-semibold transition">Masuk di sini</a>
                    </p>
                </div>
            </form>

            <!-- Separator OR -->
            <div class="relative flex items-center justify-center my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200 dark:border-slate-700"></div>
                </div>
                <span class="relative px-3 bg-white dark:bg-slate-800 text-xs font-bold text-gray-500 dark:text-slate-500 uppercase tracking-wider transition-colors duration-300">ATAU</span>
            </div>

            <!-- SSO Buttons -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 relative z-10">
                <!-- Login with Google -->
                <a href="/auth/google/redirect" class="flex items-center justify-center gap-2.5 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
                    </svg>
                    <span>Daftar dengan Google</span>
                </a>

                <!-- Login with Telegram -->
                <a href="/auth/telegram/redirect" class="flex items-center justify-center gap-2.5 px-4 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-200">
                    <svg class="w-4 h-4 text-[#229ED9]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69.01-.03.01-.14-.07-.2-.08-.06-.19-.04-.27-.02-.12.02-1.98 1.25-5.59 3.69-.53.36-1 .54-1.42.53-.46-.01-1.35-.26-2.01-.48-.81-.27-1.46-.41-1.4-.87.03-.24.37-.49 1.02-.75 3.99-1.74 6.66-2.88 7.99-3.43 3.8-1.57 4.59-1.85 5.1-.19.11.08.13.25.14.36z"/>
                    </svg>
                    <span>Daftar dengan Telegram</span>
                </a>
            </div>
        </div>

        <!-- Back Link -->
        <div class="text-center">
            <a href="/" class="text-xs text-gray-500 dark:text-slate-400 hover:text-gray-750 dark:hover:text-slate-350 transition duration-200 inline-flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Beranda
            </a>
        </div>

    </div>

</body>
</html>
