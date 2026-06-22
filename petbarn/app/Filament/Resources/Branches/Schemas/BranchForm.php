<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('type')
                    ->options(['retail' => 'Retail', 'wholesale' => 'Wholesale', 'both' => 'Both'])
                    ->required(),
                Toggle::make('is_warehouse')
                    ->required(),
                TextInput::make('address')
                    ->default(null),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
            ]);
    }
}
