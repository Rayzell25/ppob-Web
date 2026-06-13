<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ColorPicker;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Pengaturan Web PPOB')->tabs([
                
                Tabs\Tab::make('Identitas Toko')
                    ->icon('heroicon-o-building-storefront')
                    ->schema([
                        // Grid Responsif: 1 kolom di HP (default), 2 kolom di Tablet (md), 2 kolom di PC (lg)
                        Grid::make(['default' => 1, 'md' => 2, 'lg' => 2])->schema([
                            TextInput::make('web_name')->label('Nama Web/Toko')->required(),
                            TextInput::make('admin_whatsapp')->label('WhatsApp Admin')->tel(),
                        ]),
                        FileUpload::make('logo')
                            ->label('Logo Web')
                            ->disk('public')
                            ->directory('logos')
                            ->visibility('public')
                            ->image()
                            ->maxSize(2048)
                            ->columnSpanFull(),
                    ]),

                Tabs\Tab::make('Markup Harga')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Grid::make(['default' => 1, 'md' => 2])->schema([
                            TextInput::make('default_member_markup')->label('Untung Global Member')->numeric()->prefix('Rp'),
                            TextInput::make('default_reseller_markup')->label('Untung Global Reseller')->numeric()->prefix('Rp'),
                        ])
                    ]),

                Tabs\Tab::make('Popup Notifikasi')
                    ->icon('heroicon-o-megaphone')
                    ->schema([
                        Section::make('Status & Konten Popup')
                            ->description('Atur tampilan popup saat user membuka web.')
                            ->schema([
                                Toggle::make('popup_active')->label('Aktifkan Popup?')->onColor('success'),
                                TextInput::make('popup_title')->label('Judul Popup')->placeholder('Informasi Penting'),
                                FileUpload::make('popup_image')->label('Gambar Promosi/Info')->directory('popups')->image(),
                                Textarea::make('popup_text')->label('Isi Pesan/Deskripsi')->rows(3),
                            ]),
                        Section::make('Desain Tombol Popup')
                            ->schema([
                                // Grid Responsif: 1 kolom di HP, 3 kolom sejajar di Tablet/PC
                                Grid::make(['default' => 1, 'md' => 3])->schema([
                                    TextInput::make('popup_button_text')->label('Teks Tombol (Cth: Saya Paham)'),
                                    ColorPicker::make('popup_button_bg_color')->label('Warna Background Tombol'),
                                    ColorPicker::make('popup_button_color')->label('Warna Teks Tombol'),
                                ])
                            ])->collapsible(),
                    ]),

                Tabs\Tab::make('Halaman Bawah (Footer)')
                    ->icon('heroicon-o-link')
                    ->schema([
                        Section::make('Teks Deskripsi Footer')
                            ->description('Teks yang akan muncul di bagian paling bawah website.')
                            ->schema([
                                Textarea::make('footer_text')->label('Teks Copyright / Deskripsi Singkat')->rows(2)->placeholder('Contoh: © 2026 Rayzell Store. All rights reserved.'),
                            ]),
                        Section::make('Tautan Sosial Media')
                            ->description('Isi link sosial media. Kosongkan jika tidak ingin menampilkannya di halaman depan.')
                            ->schema([
                                Grid::make(['default' => 1, 'md' => 3])->schema([
                                    TextInput::make('social_instagram')
                                        ->label('Link Instagram')
                                        ->prefixIcon('heroicon-o-camera')
                                        ->placeholder('https://instagram.com/namakamu')
                                        ->url(),
                                    TextInput::make('social_telegram')
                                        ->label('Link Telegram')
                                        ->prefixIcon('heroicon-o-paper-airplane')
                                        ->placeholder('https://t.me/namakamu')
                                        ->url(),
                                    TextInput::make('social_whatsapp')
                                        ->label('Link WhatsApp')
                                        ->prefixIcon('heroicon-o-chat-bubble-left-ellipsis')
                                        ->placeholder('https://wa.me/6281234567890')
                                        ->url(),
                                ]),
                            ]),
                    ]),
            ])->columnSpanFull()
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\EditSetting::route('/'),
        ];
    }
}
