<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - {{ \App\Models\Setting::where('key', 'store_name')->value('value') ?? 'Rayzell Store' }}</title>
    
    <!-- Google Fonts - Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Style & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 selection:bg-indigo-500 selection:text-white">

    <div class="w-full max-w-md space-y-6">
        
        <!-- Logo / Title -->
        <div class="text-center">
            <h1 class="text-3xl font-extrabold bg-gradient-to-r from-blue-400 via-indigo-400 to-purple-500 bg-clip-text text-transparent tracking-tight">
                {{ \App\Models\Setting::where('key', 'store_name')->value('value') ?? 'Rayzell Store' }}
            </h1>
            <p class="text-xs text-slate-400 mt-1.5">Lupa Kata Sandi Akun</p>
        </div>

        <!-- Card Form -->
        <div class="w-full max-w-md space-y-8 bg-slate-800 rounded-2xl p-8 border border-slate-700 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-40 h-40 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <form method="POST" action="/forgot-password" class="space-y-4 relative z-10">
                @csrf
                
                @if (session('success'))
                    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-3 rounded-xl text-xs font-semibold">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-3 rounded-xl text-xs font-semibold">
                        {{ $errors->first() }}
                    </div>
                @endif

                <p class="text-xs text-slate-350 leading-relaxed mb-2">Masukkan nomor WhatsApp terdaftar Anda. Kami akan mengirimkan link untuk mengatur ulang kata sandi Anda melalui WhatsApp.</p>

                <!-- Phone Input -->
                <div class="space-y-1.5">
                    <label for="phone" class="text-xs font-bold text-slate-400 uppercase tracking-wide">Nomor WhatsApp</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required autofocus
                        class="w-full bg-slate-950 border border-slate-700 hover:border-slate-600 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none transition" 
                        placeholder="Contoh: 08123456789" />
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-sm py-3 rounded-xl shadow-lg shadow-indigo-600/10 hover:shadow-indigo-600/35 transition-all duration-200 mt-2">
                    Kirim Link Reset ke WhatsApp
                </button>
            </form>
        </div>

        <!-- Back Link -->
        <div class="text-center">
            <a href="/login" class="text-xs text-slate-400 hover:text-slate-300 transition duration-200 flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Halaman Masuk
            </a>
        </div>

    </div>

</body>
</html>
