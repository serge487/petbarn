<?php

namespace App\Filament\Resources\Sales\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SaleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('branch_id')
                    ->relationship('branch', 'name')
                    ->required(),
                Select::make('cashier_id')
                    ->relationship('cashier', 'name')
                    ->required(),
                TextInput::make('total_usd')
                    ->required()
                    ->numeric(),
                Select::make('payment_method')
                    ->options(['cash' => 'Cash', 'card' => 'Card'])
                    ->required(),
                TextInput::make('receipt_number')
                    ->required(),
            ]);
    }
}
