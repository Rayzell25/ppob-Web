<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi - {{ \App\Models\Setting::where('key', 'store_name')->value('value') ?? \App\Models\Setting::where('key', 'web_name')->value('value') ?? 'Rayzell Store' }}</title>
    
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
            <h1 class="text-3xl font-extrabold bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-600 dark:from-blue-400 dark:via-indigo-400 dark:to-purple-500 bg-clip-text text-transparent tracking-tight">
                {{ \App\Models\Setting::where('key', 'store_name')->value('value') ?? \App\Models\Setting::where('key', 'web_name')->value('value') ?? 'Rayzell Store' }}
            </h1>
            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1.5">Reset Kata Sandi Akun</p>
        </div>

        <!-- Card Form -->
        <div class="w-full max-w-md space-y-8 bg-white dark:bg-slate-800 rounded-2xl p-8 border border-gray-200 dark:border-slate-700 shadow-2xl relative overflow-hidden transition-colors duration-300">
            <div class="absolute -right-20 -top-20 w-40 h-40 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <form method="POST" action="/reset-password" class="space-y-4 relative z-10">
                @csrf
                
                <input type="hidden" name="token" value="{{ $token }}" />
                <input type="hidden" name="phone" value="{{ $phone }}" />

                @if ($errors->any())
                    <div class="bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 p-3 rounded-xl text-xs font-semibold">
                        {{ $errors->first() }}
                    </div>
                @endif

                <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed mb-2">Silakan masukkan kata sandi baru Anda di bawah ini untuk mereset kata sandi akun Anda.</p>

                <!-- Password Input -->
                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wide">Kata Sandi Baru</label>
                    <input type="password" id="password" name="password" required autofocus
                        class="w-full bg-gray-50 dark:bg-slate-950 border border-gray-300 dark:border-slate-700 hover:border-gray-400 dark:hover:border-slate-600 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none transition" 
                        placeholder="••••••••" />
                </div>

                <!-- Password Confirmation Input -->
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="text-xs font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wide">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full bg-gray-50 dark:bg-slate-950 border border-gray-300 dark:border-slate-700 hover:border-gray-400 dark:hover:border-slate-600 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none transition" 
                        placeholder="••••••••" />
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-sm py-3 rounded-xl shadow-lg shadow-indigo-600/10 hover:shadow-indigo-600/35 transition-all duration-200 mt-2">
                    Simpan Password Baru
                </button>
            </form>
        </div>

    </div>

</body>
</html>
