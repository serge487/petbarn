<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Transfer;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todaySales = Sale::query()->whereDate('created_at', today())->sum('total_usd');
        $pendingTransfers = Transfer::query()->whereIn('status', ['pending', 'approved', 'in_transit'])->count();

        return [
            Stat::make('Today\'s Revenue', '$'.number_format($todaySales, 2))
                ->description('Across all branches')
                ->descriptionIcon(Heroicon::OutlinedBanknotes)
                ->color('success')
                ->chart([7, 12, 9, 14, 18, $todaySales > 0 ? 20 : 5, max($todaySales, 1)]),

            Stat::make('Active Products', Product::query()->where('is_active', true)->count())
                ->description('In catalog')
                ->descriptionIcon(Heroicon::OutlinedCube)
                ->color('primary'),

            Stat::make('Branches', Branch::query()->count())
                ->description(Branch::query()->where('is_warehouse', true)->count().' warehouse')
                ->descriptionIcon(Heroicon::OutlinedBuildingStorefront)
                ->color('info'),

            Stat::make('Pending Transfers', $pendingTransfers)
                ->description('Awaiting completion')
                ->descriptionIcon(Heroicon::OutlinedTruck)
                ->color($pendingTransfers > 0 ? 'warning' : 'success'),

            Stat::make('Active Employees', Employee::query()->where('status', 'active')->count())
                ->description('On payroll')
                ->descriptionIcon(Heroicon::OutlinedUserGroup)
                ->color('gray'),
        ];
    }
}
