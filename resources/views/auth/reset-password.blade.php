<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi - {{ \App\Models\Setting::where('key', 'store_name')->value('value') ?? 'Rayzell Store' }}</title>
    
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
            <p class="text-xs text-slate-400 mt-1.5">Reset Kata Sandi Akun</p>
        </div>

        <!-- Card Form -->
        <div class="w-full max-w-md space-y-8 bg-slate-800 rounded-2xl p-8 border border-slate-700 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-40 h-40 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>
            
            <form method="POST" action="/reset-password" class="space-y-4 relative z-10">
                @csrf
                
                <input type="hidden" name="token" value="{{ $token }}" />
                <input type="hidden" name="phone" value="{{ $phone }}" />

                @if ($errors->any())
                    <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-3 rounded-xl text-xs font-semibold">
                        {{ $errors->first() }}
                    </div>
                @endif

                <p class="text-xs text-slate-350 leading-relaxed mb-2">Silakan masukkan kata sandi baru Anda di bawah ini untuk mereset kata sandi akun Anda.</p>

                <!-- Password Input -->
                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-bold text-slate-400 uppercase tracking-wide">Kata Sandi Baru</label>
                    <input type="password" id="password" name="password" required autofocus
                        class="w-full bg-slate-950 border border-slate-700 hover:border-slate-600 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none transition" 
                        placeholder="••••••••" />
                </div>

                <!-- Password Confirmation Input -->
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="text-xs font-bold text-slate-400 uppercase tracking-wide">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full bg-slate-950 border border-slate-700 hover:border-slate-600 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none transition" 
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
