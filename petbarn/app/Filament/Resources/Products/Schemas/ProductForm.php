<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Identification')
                    ->description('SKU and barcode for POS scanning')
                    ->icon('heroicon-o-qr-code')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('barcode')
                            ->maxLength(255),
                        TextInput::make('item_name')
                            ->label('Product name')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->label('Product photo')
                            ->image()
                            ->directory('products')
                            ->disk('public')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),
                Section::make('Classification')
                    ->icon('heroicon-o-tag')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        Select::make('category')
                            ->options(['dog' => 'Dog', 'cat' => 'Cat'])
                            ->required()
                            ->native(false),
                        TextInput::make('subcategory')
                            ->required()
                            ->placeholder('e.g. Dry Food, Treats'),
                        TextInput::make('measurement_unit')
                            ->label('Unit')
                            ->required()
                            ->placeholder('kg, L, pcs'),
                        TextInput::make('measurement_value')
                            ->label('Size')
                            ->required()
                            ->numeric(),
                    ]),
                Section::make('Pricing')
                    ->icon('heroicon-o-currency-dollar')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('unit_price_usd')
                            ->label('Store price (USD)')
                            ->required()
                            ->numeric()
                            ->prefix('$'),
                        TextInput::make('toters_price')
                            ->label('Toters delivery price (USD)')
                            ->required()
                            ->numeric()
                            ->prefix('$'),
                        Select::make('source')
                            ->options(['excel_import' => 'Excel import', 'manual' => 'Manual entry'])
                            ->default('manual')
                            ->required()
                            ->native(false),
                        Toggle::make('is_active')
                            ->label('Active in catalog')
                            ->default(true),
                    ]),
            ]);
    }
}
