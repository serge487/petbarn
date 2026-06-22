<?php

namespace App\Filament\Resources\Branches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->address),
                TextColumn::make('type')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                IconColumn::make('is_warehouse')
                    ->label('Warehouse')
                    ->boolean(),
                TextColumn::make('phone')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('employees_count')
                    ->counts('employees')
                    ->label('Staff')
                    ->badge()
                    ->color('gray'),
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
