<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Account')
                    ->description('Login credentials and access')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->helperText(fn (string $operation): string => $operation === 'edit'
                                ? 'Leave blank to keep the current password.'
                                : 'Minimum 8 characters recommended.'),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
                Section::make('Assignment')
                    ->description('Branch and role for this user')
                    ->icon('heroicon-o-building-office')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('role_id')
                            ->relationship('role', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('branch_id')
                            ->relationship('branch', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('HQ / All branches'),
                    ]),
            ]);
    }
}
