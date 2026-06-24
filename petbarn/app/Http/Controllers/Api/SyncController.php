<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

/**
 * Read-only endpoints the PawsNation storefront pulls from to mirror
 * PetBarn's catalogue and per-branch stock. PetBarn stays the source of truth.
 */
class SyncController extends Controller
{
    public function branches(): JsonResponse
    {
        $branches = Branch::query()
            ->orderBy('id')
            ->get(['id', 'name', 'type', 'is_warehouse', 'address', 'phone'])
            ->map(fn (Branch $b) => [
                'petbarn_id'   => $b->id,
                'name'         => $b->name,
                'type'         => $b->type,
                'is_warehouse' => (bool) $b->is_warehouse,
                'address'      => $b->address,
                'phone'        => $b->phone,
            ]);

        return response()->json(['data' => $branches]);
    }

    public function products(): JsonResponse
    {
        $products = Product::query()
            ->orderBy('id')
            ->get()
            ->map(fn (Product $p) => [
                'petbarn_id'        => $p->id,
                'sku'               => $p->sku,
                'barcode'           => $p->barcode,
                'item_name'         => $p->item_name,
                'description'       => $p->description,
                'category'          => $p->category,      // dog | cat
                'subcategory'       => $p->subcategory,
                'measurement_unit'  => $p->measurement_unit,
                'measurement_value' => $p->measurement_value,
                'unit_price_usd'    => $p->unit_price_usd,
                'is_active'         => (bool) $p->is_active,
                'updated_at'        => optional($p->updated_at)->toIso8601String(),
            ]);

        return response()->json(['data' => $products]);
    }

    /**
     * Per-branch stock levels. Keyed by PetBarn product + branch ids so the
     * storefront can resolve them via the ids it stored during product/branch sync.
     */
    public function inventory(): JsonResponse
    {
        $rows = Inventory::query()
            ->orderBy('branch_id')
            ->orderBy('product_id')
            ->get(['branch_id', 'product_id', 'quantity', 'updated_at'])
            ->map(fn (Inventory $i) => [
                'petbarn_branch_id'  => $i->branch_id,
                'petbarn_product_id' => $i->product_id,
                'quantity'           => (int) $i->quantity,
                'updated_at'         => optional($i->updated_at)->toIso8601String(),
            ]);

        return response()->json(['data' => $rows]);
    }
}
