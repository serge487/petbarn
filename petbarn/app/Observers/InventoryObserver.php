<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Services\PawsNationNotifier;
use Illuminate\Support\Facades\DB;

class InventoryObserver
{
    public function __construct(private readonly PawsNationNotifier $notifier)
    {
    }

    public function saved(Inventory $inventory): void
    {
        $this->notify($inventory, (int) $inventory->quantity, 'inventory.updated');
    }

    public function deleted(Inventory $inventory): void
    {
        // Row removed => no stock for that product at that branch.
        $this->notify($inventory, 0, 'inventory.deleted');
    }

    private function notify(Inventory $inventory, int $quantity, string $event): void
    {
        $branchId = (int) $inventory->branch_id;
        $productId = (int) $inventory->product_id;

        // Fire only after the surrounding transaction commits (e.g. a POS sale
        // that decrements several rows) so PawsNation never sees uncommitted state.
        DB::afterCommit(fn () => $this->notifier->inventoryChanged($branchId, $productId, $quantity, $event));
    }
}
