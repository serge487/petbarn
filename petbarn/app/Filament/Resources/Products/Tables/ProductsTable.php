<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),
                TextColumn::make('item_name')
                    ->label('Product')
                    ->searchable()
                    ->description(fn ($record) => $record->subcategory),
                TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dog' => 'info',
                        'cat' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('measurement_value')
                    ->label('Size')
                    ->formatStateUsing(fn ($state, $record) => $state.' '.$record->measurement_unit),
                TextColumn::make('unit_price_usd')
                    ->label('Price')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('toters_price')
                    ->label('Toters')
                    ->money('USD')
                    ->toggleable(),
                TextColumn::make('source')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'manual' ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state): string => $state === 'excel_import' ? 'Excel' : 'Manual'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('item_name')
            ->filters([
                SelectFilter::make('category')
                    ->options(['dog' => 'Dog', 'cat' => 'Cat']),
                SelectFilter::make('is_active')
                    ->options([1 => 'Active', 0 => 'Inactive']),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
