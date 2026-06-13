<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Str;

class TransactionService
{
    /**
     * Generate a unique transaction reference ID.
     *
     * @return string
     */
    public function generateTrxId(): string
    {
        $setting = Setting::where('key', 'trx_prefix')->first();
        $prefix = $setting ? $setting->value : 'TRX-';

        return $prefix . date('YmdHis') . strtoupper(Str::random(4));
    }
}
