<?php

namespace App\Filament\Resources\SalaryRecords;

use App\Filament\Resources\SalaryRecords\Pages\CreateSalaryRecord;
use App\Filament\Resources\SalaryRecords\Pages\EditSalaryRecord;
use App\Filament\Resources\SalaryRecords\Pages\ListSalaryRecords;
use App\Filament\Resources\SalaryRecords\Schemas\SalaryRecordForm;
use App\Filament\Resources\SalaryRecords\Tables\SalaryRecordsTable;
use App\Models\SalaryRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SalaryRecordResource extends Resource
{
    protected static ?string $model = SalaryRecord::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Payroll';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    public static function form(Schema $schema): Schema
    {
        return SalaryRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalaryRecordsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSalaryRecords::route('/'),
            'create' => CreateSalaryRecord::route('/create'),
            'edit' => EditSalaryRecord::route('/{record}/edit'),
        ];
    }
}
