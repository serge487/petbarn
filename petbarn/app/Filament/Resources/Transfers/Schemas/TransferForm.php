<?php

namespace App\Filament\Resources\Transfers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('from_branch_id')
                    ->relationship('fromBranch', 'name')
                    ->required(),
                Select::make('to_branch_id')
                    ->relationship('toBranch', 'name')
                    ->required(),
                TextInput::make('requested_by')
                    ->required()
                    ->numeric(),
                TextInput::make('approved_by')
                    ->numeric()
                    ->default(null),
                Select::make('status')
                    ->options([
            'pending' => 'Pending',
            'approved' => 'Approved',
            'in_transit' => 'In transit',
            'completed' => 'Completed',
            'rejected' => 'Rejected',
        ])
                    ->default('pending')
                    ->required(),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
