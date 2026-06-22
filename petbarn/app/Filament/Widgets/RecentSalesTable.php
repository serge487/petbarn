<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentSalesTable extends TableWidget
{
    protected static ?string $heading = 'Recent Sales';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sale::query()
                    ->with(['branch', 'cashier'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('receipt_number')
                    ->label('Receipt')
                    ->searchable()
                    ->weight('bold')
                    ->color('primary'),
                TextColumn::make('branch.name')
                    ->label('Branch')
                    ->badge()
                    ->color('info'),
                TextColumn::make('cashier.name')
                    ->label('Cashier'),
                TextColumn::make('total_usd')
                    ->label('Total')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => $state === 'cash' ? 'success' : 'warning'),
                TextColumn::make('created_at')
                    ->label('When')
                    ->since()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
