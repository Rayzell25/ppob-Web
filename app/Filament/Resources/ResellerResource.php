<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResellerResource\Pages;
use App\Models\User;
use App\Models\WalletTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ResellerResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Reseller';

    protected static ?string $modelLabel = 'Reseller';

    protected static ?string $pluralModelLabel = 'Reseller';

    protected static ?string $slug = 'resellers';

    protected static ?int $navigationSort = 3;

    /**
     * Scope the resource to only reseller accounts.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'reseller');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('WhatsApp')
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),
                Forms\Components\TextInput::make('telegram_username')
                    ->label('ID Telegram')
                    ->maxLength(100),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context): bool => $context === 'create'),
                Forms\Components\Hidden::make('role')
                    ->default('reseller'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('WhatsApp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telegram_username')
                    ->label('Telegram')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('balance')
                    ->money('IDR')
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

                // Custom Action: Tambah Saldo
                Tables\Actions\Action::make('tambah_saldo')
                    ->label('Tambah Saldo')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah Saldo (Rp)')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        Forms\Components\TextInput::make('description')
                            ->label('Keterangan')
                            ->placeholder('e.g., Top-up via Transfer BCA')
                            ->maxLength(255),
                    ])
                    ->action(function (User $record, array $data): void {
                        $amount = (float) $data['amount'];

                        $record->increment('balance', $amount);

                        WalletTransaction::create([
                            'user_id' => $record->id,
                            'type' => 'topup',
                            'amount' => $amount,
                            'status' => 'success',
                            'description' => $data['description'] ?? "Top-up saldo reseller oleh admin. Jumlah: Rp " . number_format($amount, 0, ',', '.'),
                        ]);

                        Notification::make()
                            ->title('Saldo Berhasil Ditambahkan')
                            ->body("Saldo {$record->name} bertambah Rp " . number_format($amount, 0, ',', '.'))
                            ->success()
                            ->send();
                    }),

                // Custom Action: Kurangi Saldo
                Tables\Actions\Action::make('kurangi_saldo')
                    ->label('Kurangi Saldo')
                    ->icon('heroicon-o-minus-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah Pengurangan (Rp)')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        Forms\Components\TextInput::make('description')
                            ->label('Keterangan')
                            ->placeholder('e.g., Koreksi saldo / Refund')
                            ->maxLength(255),
                    ])
                    ->action(function (User $record, array $data): void {
                        $amount = (float) $data['amount'];

                        if ($record->balance < $amount) {
                            Notification::make()
                                ->title('Saldo Tidak Cukup')
                                ->body("Saldo {$record->name} (Rp " . number_format($record->balance, 0, ',', '.') . ") tidak mencukupi untuk pengurangan Rp " . number_format($amount, 0, ',', '.'))
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->decrement('balance', $amount);

                        WalletTransaction::create([
                            'user_id' => $record->id,
                            'type' => 'deduction',
                            'amount' => $amount,
                            'status' => 'success',
                            'description' => $data['description'] ?? "Pengurangan saldo reseller oleh admin. Jumlah: Rp " . number_format($amount, 0, ',', '.'),
                        ]);

                        Notification::make()
                            ->title('Saldo Berhasil Dikurangi')
                            ->body("Saldo {$record->name} berkurang Rp " . number_format($amount, 0, ',', '.'))
                            ->success()
                            ->send();
                    }),

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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResellers::route('/'),
            'create' => Pages\CreateReseller::route('/create'),
            'edit' => Pages\EditReseller::route('/{record}/edit'),
        ];
    }
}
