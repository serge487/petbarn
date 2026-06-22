<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;

class POS extends Page
{
    protected string $view = 'filament.pages.pos';

    protected static string | \UnitEnum | null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Point of Sale';

    protected static ?string $slug = 'pos';

    protected static string | \BackedEnum | null $navigationIcon = Heroicon::OutlinedShoppingCart;

    public string $sku = '';

    public string $search = '';

    public string $category_filter = 'all';

    public array $cart = [];

    public string $payment_method = 'cash';

    public ?int $branch_id = null;

    public function getTitle(): string
    {
        return 'Point of Sale';
    }

    public function mount(): void
    {
        $user = Auth::user();
        $this->branch_id = $user->branch_id ?? Branch::query()->where('is_warehouse', true)->value('id');
    }

    #[Computed]
    public function branchName(): string
    {
        return Branch::query()->whereKey($this->branch_id)->value('name') ?? '—';
    }

    #[Computed]
    public function products(): Collection
    {
        return Product::query()
            ->where('is_active', true)
            ->when($this->category_filter !== 'all', fn ($q) => $q->where('category', $this->category_filter))
            ->when($this->search !== '', function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($q) use ($term) {
                    $q->where('item_name', 'like', $term)
                        ->orWhere('sku', 'like', $term)
                        ->orWhere('barcode', 'like', $term)
                        ->orWhere('subcategory', 'like', $term);
                });
            })
            ->orderBy('item_name')
            ->get()
            ->map(function (Product $product) {
                $stock = Inventory::query()
                    ->where('branch_id', $this->branch_id)
                    ->where('product_id', $product->id)
                    ->value('quantity') ?? 0;

                return [
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->item_name,
                    'category' => $product->category,
                    'subcategory' => $product->subcategory,
                    'price' => (float) $product->unit_price_usd,
                    'size' => $product->measurement_value . ' ' . $product->measurement_unit,
                    'stock' => (int) $stock,
                    'image' => $product->image ? Storage::disk('public')->url($product->image) : null,
                    'in_cart' => $this->cart[$product->id]['qty'] ?? 0,
                ];
            });
    }

    public function updatedCategoryFilter(): void
    {
        unset($this->products);
    }

    public function updatedSearch(): void
    {
        unset($this->products);
    }

    public function scanSku(): void
    {
        $query = trim($this->sku);
        $this->sku = '';

        if ($query === '') {
            return;
        }

        $product = Product::query()
            ->where('is_active', true)
            ->where(fn ($q) => $q->where('sku', $query)->orWhere('barcode', $query))
            ->first();

        $product ??= Product::query()
            ->where('is_active', true)
            ->where('item_name', 'like', '%' . $query . '%')
            ->orderByRaw('item_name = ? desc', [$query])
            ->orderBy('item_name')
            ->first();

        if (! $product) {
            Notification::make()
                ->title('Product not found')
                ->body('No product matches "' . $query . '"')
                ->danger()
                ->send();

            return;
        }

        $this->addProduct($product->id);
    }

    public function addProduct(int $productId): void
    {
        $product = Product::query()->find($productId);

        if (! $product || ! $product->is_active) {
            return;
        }

        $stock = Inventory::query()
            ->where('branch_id', $this->branch_id)
            ->where('product_id', $product->id)
            ->value('quantity') ?? 0;

        if ($stock < 1) {
            Notification::make()
                ->title('Out of stock')
                ->body($product->item_name . ' is not available at this branch.')
                ->warning()
                ->send();

            return;
        }

        $key = $product->id;

        if (isset($this->cart[$key])) {
            if ($this->cart[$key]['qty'] >= $stock) {
                Notification::make()->title('Stock limit reached')->warning()->send();

                return;
            }
            $this->cart[$key]['qty']++;
        } else {
            $this->cart[$key] = [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->item_name,
                'category' => $product->category,
                'subcategory' => $product->subcategory,
                'size' => $product->measurement_value . ' ' . $product->measurement_unit,
                'price' => (float) $product->unit_price_usd,
                'qty' => 1,
                'stock' => (int) $stock,
                'image' => $product->image ? Storage::disk('public')->url($product->image) : null,
            ];
        }

        unset($this->products);
    }

    public function incrementQty(int $productId): void
    {
        if (! isset($this->cart[$productId])) {
            return;
        }

        if ($this->cart[$productId]['qty'] >= $this->cart[$productId]['stock']) {
            Notification::make()->title('Stock limit reached')->warning()->send();

            return;
        }

        $this->cart[$productId]['qty']++;
        unset($this->products);
    }

    public function decrementQty(int $productId): void
    {
        if (! isset($this->cart[$productId])) {
            return;
        }

        if ($this->cart[$productId]['qty'] <= 1) {
            $this->removeItem($productId);
        } else {
            $this->cart[$productId]['qty']--;
            unset($this->products);
        }
    }

    public function removeItem(int $productId): void
    {
        unset($this->cart[$productId]);
        unset($this->products);
    }

    public function clearCart(): void
    {
        $this->cart = [];
        unset($this->products);
    }

    public function getTotal(): float
    {
        return collect($this->cart)->sum(fn ($item) => $item['price'] * $item['qty']);
    }

    public function getItemCount(): int
    {
        return (int) collect($this->cart)->sum('qty');
    }

    public function checkout(): void
    {
        if (empty($this->cart)) {
            Notification::make()->title('Cart is empty')->warning()->send();

            return;
        }

        $total = $this->getTotal();
        $receipt = 'PB-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

        DB::transaction(function () use ($total, $receipt) {
            $sale = Sale::query()->create([
                'branch_id' => $this->branch_id,
                'cashier_id' => Auth::id(),
                'total_usd' => $total,
                'payment_method' => $this->payment_method,
                'receipt_number' => $receipt,
            ]);

            foreach ($this->cart as $item) {
                SaleItem::query()->create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'unit_price_usd' => $item['price'],
                    'subtotal_usd' => $item['price'] * $item['qty'],
                ]);

                Inventory::query()
                    ->where('branch_id', $this->branch_id)
                    ->where('product_id', $item['id'])
                    ->decrement('quantity', $item['qty']);
            }
        });

        $this->clearCart();

        Notification::make()
            ->title('Sale complete')
            ->body('Receipt ' . $receipt . ' · $' . number_format($total, 2))
            ->success()
            ->duration(5000)
            ->send();
    }
}
