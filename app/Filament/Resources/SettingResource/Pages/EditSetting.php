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
        $data['web_name'] = \App\Models\Setting::where('key', 'store_name')->value('value') ?? \App\Models\Setting::where('key', 'web_name')->value('value') ?? 'Rayzell Store';
        $data['admin_whatsapp'] = \App\Models\Setting::where('key', 'whatsapp_link')->value('value') ?? \App\Models\Setting::where('key', 'admin_whatsapp')->value('value') ?? '';
        $data['logo'] = \App\Models\Setting::where('key', 'logo')->value('value') ?? '';
        $data['default_member_markup'] = \App\Models\Setting::where('key', 'default_member_markup')->value('value') ?? 2000;
        $data['default_reseller_markup'] = \App\Models\Setting::where('key', 'default_reseller_markup')->value('value') ?? 1000;
        
        $data['popup_active'] = $data['popup_active'] ?? (bool)(\App\Models\Setting::where('key', 'popup_active')->value('value') ?? false);
        $data['popup_title'] = $data['popup_title'] ?? \App\Models\Setting::where('key', 'popup_title')->value('value') ?? '';
        $data['popup_image'] = $data['popup_image'] ?? \App\Models\Setting::where('key', 'popup_image')->value('value') ?? '';
        $data['popup_text'] = $data['popup_text'] ?? \App\Models\Setting::where('key', 'popup_text')->value('value') ?? '';
        $data['popup_button_text'] = $data['popup_button_text'] ?? \App\Models\Setting::where('key', 'popup_button_text')->value('value') ?? 'Saya Paham';
        $data['popup_button_color'] = $data['popup_button_color'] ?? \App\Models\Setting::where('key', 'popup_button_color')->value('value') ?? '#ffffff';
        $data['popup_button_bg_color'] = $data['popup_button_bg_color'] ?? \App\Models\Setting::where('key', 'popup_button_bg_color')->value('value') ?? '#3b82f6';
        
        $data['footer_text'] = \App\Models\Setting::where('key', 'store_footer')->value('value') ?? \App\Models\Setting::where('key', 'footer_text')->value('value') ?? '';
        $data['social_instagram'] = \App\Models\Setting::where('key', 'social_instagram')->value('value') ?? '';
        $data['social_telegram'] = \App\Models\Setting::where('key', 'social_telegram')->value('value') ?? '';
        $data['social_whatsapp'] = \App\Models\Setting::where('key', 'social_whatsapp')->value('value') ?? '';

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        \App\Models\Setting::updateOrCreate(['key' => 'store_name'], ['value' => $data['web_name']]);
        \App\Models\Setting::updateOrCreate(['key' => 'web_name'], ['value' => $data['web_name']]);
        \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_link'], ['value' => $data['admin_whatsapp']]);
        \App\Models\Setting::updateOrCreate(['key' => 'admin_whatsapp'], ['value' => $data['admin_whatsapp']]);
        \App\Models\Setting::updateOrCreate(['key' => 'logo'], ['value' => $data['logo']]);
        \App\Models\Setting::updateOrCreate(['key' => 'default_member_markup'], ['value' => $data['default_member_markup']]);
        \App\Models\Setting::updateOrCreate(['key' => 'default_reseller_markup'], ['value' => $data['default_reseller_markup']]);
        
        \App\Models\Setting::updateOrCreate(['key' => 'popup_active'], ['value' => $data['popup_active'] ? '1' : '0']);
        \App\Models\Setting::updateOrCreate(['key' => 'popup_title'], ['value' => $data['popup_title'] ?? '']);
        \App\Models\Setting::updateOrCreate(['key' => 'popup_image'], ['value' => $data['popup_image'] ?? '']);
        \App\Models\Setting::updateOrCreate(['key' => 'popup_text'], ['value' => $data['popup_text'] ?? '']);
        \App\Models\Setting::updateOrCreate(['key' => 'popup_button_text'], ['value' => $data['popup_button_text'] ?? '']);
        \App\Models\Setting::updateOrCreate(['key' => 'popup_button_color'], ['value' => $data['popup_button_color'] ?? '']);
        \App\Models\Setting::updateOrCreate(['key' => 'popup_button_bg_color'], ['value' => $data['popup_button_bg_color'] ?? '']);

        \App\Models\Setting::updateOrCreate(['key' => 'store_footer'], ['value' => $data['footer_text'] ?? '']);
        \App\Models\Setting::updateOrCreate(['key' => 'footer_text'], ['value' => $data['footer_text'] ?? '']);
        \App\Models\Setting::updateOrCreate(['key' => 'social_instagram'], ['value' => $data['social_instagram'] ?? '']);
        \App\Models\Setting::updateOrCreate(['key' => 'social_telegram'], ['value' => $data['social_telegram'] ?? '']);
        \App\Models\Setting::updateOrCreate(['key' => 'social_whatsapp'], ['value' => $data['social_whatsapp'] ?? '']);

        $data['value'] = 'site_settings';
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
