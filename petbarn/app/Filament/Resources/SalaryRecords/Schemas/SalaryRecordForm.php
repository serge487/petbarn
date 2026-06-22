<?php

namespace App\Filament\Resources\SalaryRecords\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SalaryRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employee_id')
                    ->relationship('employee', 'name')
                    ->required(),
                TextInput::make('recorded_by')
                    ->required()
                    ->numeric(),
                TextInput::make('month')
                    ->required()
                    ->numeric(),
                TextInput::make('year')
                    ->required()
                    ->numeric(),
                TextInput::make('amount_usd')
                    ->required()
                    ->numeric(),
                TextInput::make('bonus_usd')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('deduction_usd')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('net_usd')
                    ->required()
                    ->numeric(),
                Select::make('payment_status')
                    ->options(['pending' => 'Pending', 'paid' => 'Paid'])
                    ->default('pending')
                    ->required(),
                DatePicker::make('paid_at'),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
