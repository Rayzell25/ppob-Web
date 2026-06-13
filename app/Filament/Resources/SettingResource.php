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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ColorPicker;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Pengaturan Web')
                    ->tabs([
                        Tabs\Tab::make('Identitas Toko')->schema([
                            TextInput::make('web_name')
                                ->label('Nama Toko')
                                ->required(),
                            TextInput::make('admin_whatsapp')
                                ->label('Link WhatsApp'),
                            FileUpload::make('logo')
                                ->label('Logo Toko')
                                ->directory('logos')
                                ->image(),
                        ]),
                        Tabs\Tab::make('Markup Global Default')->schema([
                            TextInput::make('default_member_markup')
                                ->numeric()
                                ->label('Untung Member'),
                            TextInput::make('default_reseller_markup')
                                ->numeric()
                                ->label('Untung Reseller'),
                        ]),
                        Tabs\Tab::make('Popup Notifikasi')->schema([
                            Toggle::make('popup_active')
                                ->label('Aktifkan Tampilan Popup?'),
                            TextInput::make('popup_title')
                                ->label('Judul Popup (cth: Informasi Penting)'),
                            FileUpload::make('popup_image')
                                ->label('Gambar/Foto Popup')
                                ->directory('popups')
                                ->image(),
                            Textarea::make('popup_text')
                                ->label('Isi Pesan/Deskripsi'),
                            TextInput::make('popup_button_text')
                                ->label('Teks Tombol (cth: Saya Paham)'),
                            ColorPicker::make('popup_button_bg_color')
                                ->label('Warna Background Tombol'),
                            ColorPicker::make('popup_button_color')
                                ->label('Warna Teks Tombol'),
                        ]),
                    ])
                    ->columnSpanFull()
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
