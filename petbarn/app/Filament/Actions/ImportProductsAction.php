<?php

namespace App\Filament\Actions;

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;

class ImportProductsAction
{
    public static function make(): Action
    {
        return Action::make('importProducts')
            ->label('Import Excel / CSV')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('gray')
            ->modalHeading('Import Products from Excel / CSV')
            ->modalDescription('Upload your product sheet. The first row must be the header row.')
            ->modalSubmitActionLabel('Import')
            ->form([
                FileUpload::make('file')
                    ->label('Product sheet (.xlsx or .csv)')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'text/csv',
                        'application/csv',
                    ])
                    ->maxSize(10240)
                    ->required()
                    ->storeFiles(false),
            ])
            ->action(function (array $data) {
                $file = $data['file'];

                // $file is an UploadedFile instance when storeFiles(false)
                $path     = $file->getRealPath();
                $ext      = strtolower($file->getClientOriginalExtension());

                $rows = static::readFile($path, $ext);

                if (empty($rows)) {
                    Notification::make()->title('File appears empty')->warning()->send();
                    return;
                }

                $col = static::headerMap($rows[0]);

                if (! isset($col['barcode'])) {
                    Notification::make()
                        ->title('Missing required column: Barcode')
                        ->body('Your sheet must include a Barcode column. Barcode is used as SKU for scanning.')
                        ->danger()
                        ->send();

                    return;
                }

                $branches  = Branch::all();
                $imported  = 0;
                $skipped   = 0;

                DB::transaction(function () use ($rows, $col, $branches, &$imported, &$skipped) {
                    foreach (array_slice($rows, 1) as $row) {
                        $barcode = static::cell($row, $col, 'barcode');

                        if ($barcode === '') {
                            $skipped++;
                            continue;
                        }

                        $price = static::money(static::cell($row, $col, 'unit_price_usd'));

                        $data = [
                            'sku' => $barcode,
                            'barcode' => $barcode,
                            'item_name' => static::cell($row, $col, 'item_name') ?: $barcode,
                            'description' => static::cell($row, $col, 'description') ?: null,
                            'category' => static::category(static::cell($row, $col, 'category')),
                            'subcategory' => static::cell($row, $col, 'sub_category') ?: 'General',
                            'measurement_unit' => static::cell($row, $col, 'measurement_unit') ?: 'pcs',
                            'measurement_value' => (float) (static::cell($row, $col, 'measurement_value') ?: 1),
                            'unit_price_usd' => $price,
                            'toters_price' => static::money(static::cell($row, $col, 'toters_price')) ?: $price,
                            'source'           => 'excel_import',
                            'is_active'        => true,
                        ];

                        $product = Product::updateOrCreate(
                            ['sku' => $barcode],
                            $data,
                        );

                        // Temporary testing stock: every imported product starts with 2 units per branch.
                        foreach ($branches as $branch) {
                            Inventory::firstOrCreate(
                                ['branch_id' => $branch->id, 'product_id' => $product->id],
                                ['quantity' => 2],
                            );
                        }

                        $imported++;
                    }
                });

                Notification::make()
                    ->title("Import complete: {$imported} products imported" . ($skipped ? ", {$skipped} rows skipped" : ''))
                    ->success()
                    ->send();
            });
    }

    private static function readFile(string $path, string $ext): array
    {
        $rows = [];

        if ($ext === 'csv') {
            $reader = new CsvReader();
        } else {
            $reader = new XlsxReader();
        }

        $reader->open($path);

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = array_map(fn ($cell) => (string) $cell->getValue(), $row->getCells());
            }
            break; // only first sheet
        }

        $reader->close();

        return $rows;
    }

    private static function headerMap(array $headers): array
    {
        $map = [];

        foreach ($headers as $index => $header) {
            $key = strtolower((string) preg_replace('/[^a-z0-9]+/i', '_', trim((string) $header)));
            $key = trim($key, '_');

            if ($key !== '') {
                $map[$key] = $index;
            }
        }

        return $map;
    }

    private static function cell(array $row, array $col, string $key): string
    {
        if (! isset($col[$key])) {
            return '';
        }

        return trim((string) ($row[$col[$key]] ?? ''));
    }

    private static function money(string $value): float
    {
        $clean = preg_replace('/[^0-9.\\-]/', '', $value);

        return (float) ($clean ?: 0);
    }

    private static function category(string $value): string
    {
        $category = strtolower(trim($value));

        return in_array($category, ['dog', 'cat'], true) ? $category : 'dog';
    }
}
