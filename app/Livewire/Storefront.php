<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Livewire\Component;

class Storefront extends Component
{
    public $target_number = '';
    public $selected_product_id = null;
    public $selected_product = null;
    public $show_modal = false;

    // Quick Login Helper
    public function quickLogin($role)
    {
        if ($role === 'user') {
            $user = User::where('role', 'user')->first();
            if (!$user) {
                $user = User::create([
                    'name' => 'Test Buyer',
                    'email' => 'buyer@example.com',
                    'password' => bcrypt('password'),
                    'balance' => 100000,
                    'role' => 'user',
                ]);
            }
            Auth::login($user);
        } elseif ($role === 'admin') {
            $user = User::where('role', 'admin')->first();
            if (!$user) {
                $user = User::create([
                    'name' => 'Admin Owner',
                    'email' => 'admin@example.com',
                    'password' => bcrypt('password'),
                    'balance' => 500000,
                    'role' => 'admin',
                ]);
            }
            Auth::login($user);
        } else {
            Auth::logout();
        }

        return redirect()->to('/');
    }

    public function selectProduct($productId)
    {
        $this->selected_product_id = $productId;
        $this->selected_product = Product::findOrFail($productId);
        $this->show_modal = true;
        $this->target_number = '';
        
        session()->forget(['success', 'error']);
    }

    public function processOrder()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Silakan masuk terlebih dahulu menggunakan fitur quick login di pojok kanan atas.');
            return;
        }

        $this->validate([
            'target_number' => 'required|string|min:4',
            'selected_product_id' => 'required|exists:products,id',
        ]);

        try {
            // Call the TransactionController@store API internally
            $controller = app(TransactionController::class);
            $request = new Request([
                'product_id' => $this->selected_product_id,
                'target_number' => $this->target_number,
            ]);

            $response = $controller->store($request);
            $result = json_decode($response->getContent(), true);

            if ($response->getStatusCode() === 201 && ($result['success'] ?? false)) {
                $this->show_modal = false;
                $this->target_number = '';
                $this->selected_product_id = null;
                $this->selected_product = null;
                
                // Refresh auth user session to show updated balance
                Auth::user()->refresh();
                
                session()->flash('success', 'Pesanan berhasil dikirim ke antrean! Transaksi sedang diproses di background.');
            } else {
                session()->flash('error', $result['message'] ?? 'Gagal membuat pesanan.');
            }
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $categories = Category::with(['brands.products' => function ($query) {
            $query->where('status', 'active');
        }])->get();

        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        $banners = \App\Models\Banner::where('is_active', true)->orderBy('order', 'asc')->get();

        return view('livewire.storefront', [
            'categories' => $categories,
            'settings' => $settings,
            'banners' => $banners,
        ])->layout('components.layouts.app', ['title' => 'Storefront PPOB']);
    }
}
