<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    public function mount($record = null): void
    {
        $setting = \App\Models\Setting::firstOrCreate(
            ['key' => 'general'],
            ['value' => 'site_settings']
        );
        parent::mount($setting->key);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['store_name'] = \App\Models\Setting::where('key', 'store_name')->value('value') ?? 'Rayzell Store';
        $data['whatsapp_link'] = \App\Models\Setting::where('key', 'whatsapp_link')->value('value') ?? '';
        $data['logo'] = \App\Models\Setting::where('key', 'logo')->value('value') ?? '';
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        \App\Models\Setting::updateOrCreate(['key' => 'store_name'], ['value' => $data['store_name']]);
        \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_link'], ['value' => $data['whatsapp_link']]);
        \App\Models\Setting::updateOrCreate(['key' => 'logo'], ['value' => $data['logo']]);

        $data['value'] = 'site_settings';
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
