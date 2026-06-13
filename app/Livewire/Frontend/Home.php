<?php

namespace App\Livewire\Frontend;

use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        return view('livewire.frontend.home', [
            'banners' => \App\Models\Banner::where('is_active', true)->orderBy('order', 'asc')->get(),
            'settings' => \App\Models\Setting::first(),
            'categories' => \App\Models\Category::where('is_active', true)->get(),
        ]);
    }
}
