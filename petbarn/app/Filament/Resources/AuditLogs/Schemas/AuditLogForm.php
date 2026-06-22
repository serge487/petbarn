<?php

namespace App\Filament\Resources\AuditLogs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AuditLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(null),
                Select::make('branch_id')
                    ->relationship('branch', 'name')
                    ->default(null),
                TextInput::make('action')
                    ->required(),
                TextInput::make('model_type')
                    ->required(),
                TextInput::make('model_id')
                    ->required()
                    ->numeric(),
                Textarea::make('before')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('after')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
