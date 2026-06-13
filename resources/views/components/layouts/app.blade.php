<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ \App\Models\Setting::where('key', 'web_name')->value('value') ?? \App\Models\Setting::where('key', 'store_name')->value('value') ?? 'PPOB Store' }}</title>
    
    <!-- Google Fonts - Plus Jakarta Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;850;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased min-h-screen flex flex-col selection:bg-blue-500 selection:text-white">

    <header>
        <!-- Navbar is rendered inside components directly -->
    </header>

    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-100 py-10 text-center text-xs text-slate-500">
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
                           class="text-slate-400 hover:text-pink-500 transition duration-300 transform hover:scale-110">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                            </svg>
                        </a>
                    @endif
                    @if(!empty($telegram))
                        <a href="{{ $telegram }}" target="_blank" rel="noopener noreferrer" 
                           class="text-slate-400 hover:text-sky-400 transition duration-300 transform hover:scale-110">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.944 0C5.347 0 0 5.347 0 11.944c0 6.596 5.347 11.944 11.944 11.944 6.596 0 11.944-5.348 11.944-11.944C23.888 5.347 18.54 0 11.944 0zm5.836 8.243l-2.029 9.563c-.15.681-.557.848-1.127.525l-3.094-2.28-1.492 1.435c-.165.165-.303.303-.62.303l.222-3.148 5.733-5.18c.249-.222-.054-.345-.387-.123L7.02 14.542l-3.05-.953c-.663-.207-.677-.663.138-.982l11.93-4.6c.552-.2.1.3-.138.236z"/>
                            </svg>
                        </a>
                    @endif
                    @if(!empty($whatsapp))
                        <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" 
                           class="text-slate-400 hover:text-emerald-500 transition duration-300 transform hover:scale-110">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 11.996.01c3.2 0 6.203 1.246 8.46 3.507 2.256 2.262 3.498 5.27 3.496 8.472-.003 6.649-5.34 11.987-11.942 11.987-2.005-.001-3.973-.5-5.734-1.45L0 24zm6.59-4.846c1.66.986 3.288 1.447 4.887 1.448 5.4 0 9.79-4.387 9.793-9.783.002-2.613-1.012-5.07-2.857-6.918C16.575 2.053 14.12 1.037 11.5 1.037c-5.4 0-9.79 4.389-9.793 9.786-.001 2.023.529 4.004 1.535 5.748l-.997 3.642 3.73-.978c1.6.877 3.167 1.294 4.545 1.294v-.002zm11.758-7.904c-.31-.155-1.838-.907-2.122-1.01-.284-.104-.49-.155-.696.155-.206.31-.798.907-.978 1.11-.18.203-.36.227-.67.072-.31-.155-1.312-.483-2.5-1.543-.924-.824-1.548-1.842-1.73-2.152-.18-.31-.02-.477.136-.632.14-.14.31-.36.465-.54.155-.18.206-.31.31-.516.104-.206.05-.387-.025-.54-.077-.155-.696-1.678-.954-2.3-.25-.6-.525-.515-.72-.525-.18-.01-.387-.01-.593-.01-.206 0-.54.077-.824.387-.284.31-1.082 1.058-1.082 2.58 0 1.52 1.11 2.99 1.26 3.197.155.206 2.185 3.336 5.292 4.68.74.32 1.315.51 1.765.65.743.236 1.418.203 1.95.123.595-.088 1.838-.75 2.1-.144.26-.72.26-1.34 1.765-1.6z"/>
                            </svg>
                        </a>
                    @endif
                </div>
            @endif

            <p class="text-xs text-slate-400">
                {{ \App\Models\Setting::where('key', 'store_footer')->value('value') ?? \App\Models\Setting::where('key', 'footer_text')->value('value') ?? '© '.date('Y').' Rayzell Store PPOB. All rights reserved.' }}
            </p>
            <p class="text-slate-500">Built with Laravel 11 & Livewire 3</p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
