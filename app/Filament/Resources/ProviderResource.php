<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderResource\Pages;
use App\Models\Provider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;

    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    protected static ?string $navigationLabel = 'API Provider';

    protected static ?string $modelLabel = 'API Provider';

    protected static ?string $pluralModelLabel = 'API Provider';

    protected static ?int $navigationSort = 5;

    /**
     * Supported API types — used across form and ProductResource inline create.
     */
    public static function typeOptions(): array
    {
        return [
            'digiflazz'   => 'Digiflazz',
            'vip_reseller' => 'VIP Reseller',
            'mobilepulsa' => 'MobilePulsa',
            'tripay'      => 'Tripay PPOB',
            'rajabiller'  => 'Rajabiller',
            'serpul'      => 'Serpul',
            'manual'      => 'API Lain / Manual',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Profil API (Bebas)')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('code')
                    ->label('Kode Unik (Identifier)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),

                Forms\Components\Select::make('type')
                    ->label('Jenis API')
                    ->options(static::typeOptions())
                    ->required()
                    ->live(), // reactive to show/hide base_url

                Forms\Components\TextInput::make('api_username')
                    ->label('API Username / Member ID')
                    ->maxLength(255),

                Forms\Components\TextInput::make('api_key')
                    ->label('API Key / Username')
                    ->maxLength(255),

                Forms\Components\TextInput::make('secret_key')
                    ->label('Secret Key / Sign / Password')
                    ->maxLength(255),

                Forms\Components\TextInput::make('api_url')
                    ->label('API Endpoint URL')
                    ->url()
                    ->maxLength(500),

                Forms\Components\TextInput::make('base_url')
                    ->label('Endpoint URL (Khusus Manual / API Lain)')
                    ->url()
                    ->maxLength(500)
                    ->visible(fn(Get $get) => $get('type') === 'manual'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Profil')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis API')
                    ->badge()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProviders::route('/'),
            'create' => Pages\CreateProvider::route('/create'),
            'edit'   => Pages\EditProvider::route('/{record}/edit'),
        ];
    }
}
