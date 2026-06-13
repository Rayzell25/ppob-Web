<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletTransactionResource\Pages;
use App\Models\WalletTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WalletTransactionResource extends Resource
{
    protected static ?string $model = WalletTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Riwayat Wallet';

    protected static ?string $modelLabel = 'Riwayat Wallet';

    protected static ?string $pluralModelLabel = 'Riwayat Wallet';

    protected static ?int $navigationSort = 4;

    /**
     * Scope to only wallet transactions belonging to reseller accounts.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('user', fn(Builder $q) => $q->where('role', 'reseller'))
            ->orderByDesc('created_at');
    }

    public static function form(Form $form): Form
    {
        // Read-only: form not actively used (no create/edit pages exposed)
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Reseller')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'topup' => 'success',
                        'pembelian' => 'info',
                        'deduction' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'success' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(60)
                    ->tooltip(fn(WalletTransaction $record) => $record->description),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'failed' => 'Failed',
                    ]),
                SelectFilter::make('type')
                    ->label('Tipe Transaksi')
                    ->options([
                        'topup' => 'Top-up',
                        'pembelian' => 'Pembelian',
                        'deduction' => 'Pengurangan',
                    ]),
            ])
            ->actions([
                // Read-only audit log — no edit or delete
            ])
            ->bulkActions([
                // No bulk actions on audit log
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWalletTransactions::route('/'),
        ];
    }

    /**
     * Disable create button in header.
     */
    public static function canCreate(): bool
    {
        return false;
    }
}
