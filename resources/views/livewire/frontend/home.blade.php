<div class="bg-gray-50 min-h-screen pb-20">
    <livewire:frontend.navbar />

    <div class="max-w-md mx-auto bg-white min-h-screen shadow-sm">
        
        <div class="px-4 pt-4 pb-2 relative z-40">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" placeholder="Cari game atau pulsa..." class="w-full bg-gray-100 rounded-full pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>
        </div>

        @if($banners->count() > 0)
        <div class="px-4 py-2">
            <div class="flex overflow-x-auto snap-x snap-mandatory hide-scrollbar space-x-3 pb-2">
                @foreach($banners as $banner)
                <div class="snap-center shrink-0 w-full rounded-2xl overflow-hidden shadow-sm relative">
                    <img src="{{ asset('storage/'.$banner->image_path) }}" alt="Promo" class="w-full h-36 object-cover">
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="px-4 py-4">
            <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                <span class="text-orange-500 mr-2">🔥</span> Produk Unggulan
            </h2>
            <div class="grid grid-cols-3 gap-3">
                @foreach($categories as $category)
                <a href="/category/{{ $category->slug }}" class="block bg-white rounded-xl p-2 border border-gray-100 shadow-sm hover:shadow-md transition text-center">
                    <img src="{{ $category->icon ? asset('storage/'.$category->icon) : 'https://ui-avatars.com/api/?name='.urlencode($category->name).'&background=random' }}" class="w-12 h-12 mx-auto rounded-xl object-cover mb-2" alt="{{ $category->name }}">
                    <p class="text-xs font-semibold text-gray-700 truncate">{{ $category->name }}</p>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    @if($settings && $settings->popup_active)
    <div x-data="{ open: true }" x-show="open" class="fixed inset-0 z-[100] flex items-center justify-center bg-black bg-opacity-60 px-4" x-transition.opacity>
        <div class="bg-[#1e293b] rounded-2xl max-w-sm w-full overflow-hidden shadow-2xl relative" @click.away="open = false">
            <button @click="open = false" class="absolute top-3 right-3 text-gray-400 hover:text-white bg-gray-800 rounded-full p-1 z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            @if($settings->popup_image)
                <img src="{{ asset('storage/'.$settings->popup_image) }}" alt="Popup" class="w-full h-auto max-h-48 object-cover">
            @endif
            <div class="p-5 text-center">
                <h3 class="text-xl font-bold text-white mb-2">🔔 {{ $settings->popup_title ?? 'Informasi Penting' }}</h3>
                <p class="text-gray-300 text-sm mb-5">{{ $settings->popup_text }}</p>
                <button @click="open = false" style="background-color: {{ $settings->popup_button_bg_color ?? '#0ea5e9' }}; color: {{ $settings->popup_button_color ?? '#ffffff' }}" class="w-full font-bold py-3 px-4 rounded-xl shadow-lg transition transform hover:scale-105">
                    {{ $settings->popup_button_text ?? 'Saya Paham, Lanjutkan' }}
                </button>
            </div>
        </div>
    </div>
    @endif
    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</div>
