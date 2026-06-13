<?php

namespace App\Livewire\Frontend;

use Livewire\Component;

class Home extends Component
{
    public $search = '';
    public $searchResults = [];

    public function updatedSearch()
    {
        if (strlen($this->search) >= 2) {
            $this->searchResults = \App\Models\Category::where('name', 'like', '%' . $this->search . '%')
                ->where('is_active', true)
                ->limit(5)->get()->toArray();
        } else {
            $this->searchResults = [];
        }
    }

    public function render()
    {
        return view('livewire.frontend.home', [
            'banners' => \App\Models\Banner::where('is_active', true)->orderBy('order', 'asc')->get(),
            'settings' => \App\Models\Setting::first(),
            'categories' => \App\Models\Category::where('is_active', true)->get(),
        ]);
    }
}
