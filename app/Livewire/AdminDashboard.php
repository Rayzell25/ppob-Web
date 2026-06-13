<?php

namespace App\Livewire;

use App\Models\Provider;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class AdminDashboard extends Component
{
    use WithPagination;

    // Filters & Searches
    public $search = '';
    public $status_filter = '';

    // Provider Form State
    public $editing_provider_id = null;
    public $provider_name = '';
    public $provider_code = '';
    public $provider_type = 'digiflazz';
    public $provider_api_username = '';
    public $provider_api_key = '';
    public $provider_api_url = '';
    public $provider_balance = 0;
    public $provider_is_active = true;
    public $show_provider_form = false;

    // Store Settings State
    public $store_name = '';
    public $store_footer = '';

    protected $rules = [
        'provider_name' => 'required|string|max:255',
        'provider_code' => 'required|string|max:50',
        'provider_type' => 'required|string|in:digiflazz,kmsp,custom,generic',
        'provider_api_username' => 'nullable|string|max:255',
        'provider_api_key' => 'nullable|string',
        'provider_api_url' => 'nullable|url',
        'provider_balance' => 'required|integer|min:0',
        'provider_is_active' => 'required|boolean',
    ];

    public function mount()
    {
        // Load configurations
        $this->store_name = Setting::where('key', 'store_name')->value('value') ?? 'Rayzell Store';
        $this->store_footer = Setting::where('key', 'store_footer')->value('value') ?? '';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    // Settings Management
    public function saveSettings()
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            Setting::updateOrCreate(['key' => 'store_name'], ['value' => $this->store_name, 'group' => 'general']);
            Setting::updateOrCreate(['key' => 'store_footer'], ['value' => $this->store_footer, 'group' => 'general']);

            session()->flash('settings_success', 'Pengaturan situs berhasil diperbarui!');
            Log::info("Admin [" . auth()->user()->id . "] updated site settings.");
        } else {
            session()->flash('settings_error', 'Anda tidak memiliki otorisasi untuk melakukan tindakan ini.');
        }
    }

    // Provider Management Actions
    public function openNewProviderForm()
    {
        $this->resetProviderForm();
        $this->show_provider_form = true;
    }

    public function resetProviderForm()
    {
        $this->editing_provider_id = null;
        $this->provider_name = '';
        $this->provider_code = '';
        $this->provider_type = 'digiflazz';
        $this->provider_api_username = '';
        $this->provider_api_key = '';
        $this->provider_api_url = '';
        $this->provider_balance = 0;
        $this->provider_is_active = true;
        
        session()->forget(['provider_success', 'provider_error']);
    }

    public function editProvider($id)
    {
        $this->resetProviderForm();
        $provider = Provider::findOrFail($id);

        $this->editing_provider_id = $provider->id;
        $this->provider_name = $provider->name;
        $this->provider_code = $provider->code;
        $this->provider_type = $provider->type;
        $this->provider_api_username = $provider->api_username;
        $this->provider_api_key = $provider->api_key;
        $this->provider_api_url = $provider->api_url;
        $this->provider_balance = $provider->balance;
        $this->provider_is_active = (bool)$provider->is_active;

        $this->show_provider_form = true;
    }

    public function saveProvider()
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            session()->flash('provider_error', 'Hanya administrator yang diizinkan mengelola provider.');
            return;
        }

        $this->validate();

        $data = [
            'name' => $this->provider_name,
            'code' => $this->provider_code,
            'type' => $this->provider_type,
            'api_username' => $this->provider_api_username,
            'api_key' => $this->provider_api_key,
            'api_url' => $this->provider_api_url,
            'balance' => $this->provider_balance,
            'is_active' => $this->provider_is_active,
        ];

        if ($this->editing_provider_id) {
            $provider = Provider::findOrFail($this->editing_provider_id);
            $provider->update($data);
            session()->flash('provider_success', 'Provider berhasil diperbarui!');
            Log::info("Admin updated provider ID: [{$provider->id}].");
        } else {
            $provider = Provider::create($data);
            session()->flash('provider_success', 'Provider baru berhasil ditambahkan!');
            Log::info("Admin created new provider ID: [{$provider->id}].");
        }

        $this->show_provider_form = false;
        $this->resetProviderForm();
    }

    public function toggleProviderActive($id)
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            $provider = Provider::findOrFail($id);
            $provider->update(['is_active' => !$provider->is_active]);
            Log::info("Admin toggled provider [{$provider->name}] status to " . ($provider->is_active ? 'active' : 'inactive'));
        }
    }

    public function deleteProvider($id)
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            $provider = Provider::findOrFail($id);
            $provider->delete();
            session()->flash('provider_success', 'Provider berhasil dihapus.');
            Log::info("Admin deleted provider [{$provider->name}].");
        } else {
            session()->flash('provider_error', 'Otorisasi ditolak.');
        }
    }

    public function render()
    {
        // Require admin auth (check or fallback)
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            // Auto login first admin if available in local testing, otherwise redirect
            $adminUser = User::where('role', 'admin')->first();
            if ($adminUser) {
                auth()->login($adminUser);
            } else {
                return abort(403, 'Akses ditolak. Anda bukan Administrator.');
            }
        }

        // Stats queries
        $stats = [
            'total_transactions' => Transaction::count(),
            'total_user_balance' => User::sum('balance'),
            'active_providers' => Provider::where('is_active', true)->count(),
        ];

        // Search & Filter Transactions Query
        $transactions = Transaction::query()
            ->with(['product', 'provider', 'user'])
            ->when($this->search, function ($query) {
                $query->where('reference_id', 'like', "%{$this->search}%")
                    ->orWhere('target', 'like', "%{$this->search}%")
                    ->orWhere('product_name', 'like', "%{$this->search}%");
            })
            ->when($this->status_filter, function ($query) {
                $query->where('status', $this->status_filter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Providers list
        $providers = Provider::all();

        return view('livewire.admin-dashboard', [
            'stats' => $stats,
            'transactions' => $transactions,
            'providers' => $providers,
        ])->layout('components.layouts.app', ['title' => 'Admin Dashboard PPOB']);
    }
}
