<x-filament-panels::page>
    <style>
        .pos { --pos-primary: #7A89C2; --pos-primary-dark: #5c6ba8; --pos-bg: #f4f5f8; --pos-card: #fff; --pos-border: #e8eaef; --pos-text: #1a1d26; --pos-muted: #6b7280; font-family: inherit; }
        .dark .pos { --pos-bg: #111318; --pos-card: #1a1d26; --pos-border: #2d3140; --pos-text: #f3f4f6; --pos-muted: #9ca3af; }
        .pos * { box-sizing: border-box; }
        .pos-shell { display: grid; grid-template-columns: 1fr 340px; gap: 16px; min-height: calc(100vh - 10rem); }
        @media (max-width: 1024px) { .pos-shell { grid-template-columns: 1fr; } }
        .pos-toolbar { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 14px; }
        .pos-scan-form { display: flex; flex: 1; min-width: 280px; gap: 8px; }
        .pos-scan-input { flex: 1; padding: 12px 16px; border-radius: 10px; border: 2px solid var(--pos-primary); background: var(--pos-card); color: var(--pos-text); font-size: 16px; font-family: ui-monospace, monospace; letter-spacing: .05em; outline: none; }
        .pos-scan-input:focus { box-shadow: 0 0 0 3px rgba(122,137,194,.25); }
        .pos-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 0 20px; min-height: 48px; border-radius: 12px; border: 1px solid transparent; font-weight: 800; font-size: 14px; letter-spacing: .01em; cursor: pointer; transition: transform .12s, box-shadow .12s, background .12s, border-color .12s; white-space: nowrap; }
        .pos-btn:active { transform: scale(.98); }
        .pos-btn-primary { background: linear-gradient(135deg, var(--pos-primary), var(--pos-primary-dark)); color: #fff; box-shadow: 0 10px 22px rgba(122,137,194,.28); }
        .pos-btn-primary:hover { box-shadow: 0 12px 28px rgba(122,137,194,.36); }
        .pos-btn-primary::before { content: '+'; width: 20px; height: 20px; border-radius: 999px; background: rgba(255,255,255,.18); display: inline-flex; align-items: center; justify-content: center; font-size: 16px; line-height: 1; }
        .pos-filters { display: flex; gap: 6px; }
        .pos-filter { min-width: 54px; padding: 10px 15px; border-radius: 12px; border: 1px solid var(--pos-border); background: var(--pos-card); color: var(--pos-muted); font-size: 13px; font-weight: 800; cursor: pointer; transition: .12s; box-shadow: inset 0 -1px 0 rgba(255,255,255,.04); }
        .pos-filter:hover { border-color: var(--pos-primary); color: var(--pos-primary); }
        .pos-filter.active { background: rgba(122,137,194,.18); color: var(--pos-primary); border-color: var(--pos-primary); box-shadow: 0 0 0 3px rgba(122,137,194,.12); }
        .pos-search { padding: 10px 14px; border-radius: 10px; border: 1px solid var(--pos-border); background: var(--pos-card); color: var(--pos-text); font-size: 14px; width: 200px; outline: none; }
        .pos-main { display: flex; flex-direction: column; min-height: 0; }
        .pos-grid-wrap { flex: 1; overflow-y: auto; padding-right: 4px; }
        .pos-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(148px, 1fr)); gap: 12px; }
        .pos-card { background: var(--pos-card); border: 1px solid var(--pos-border); border-radius: 14px; overflow: hidden; cursor: pointer; transition: transform .12s, box-shadow .12s, border-color .12s; text-align: left; padding: 0; width: 100%; }
        .pos-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.08); border-color: var(--pos-primary); }
        .pos-card:active { transform: scale(.98); }
        .pos-card.out-of-stock { opacity: .45; cursor: not-allowed; pointer-events: none; }
        .pos-card-img { aspect-ratio: 1; background: linear-gradient(145deg, #eef0f6, #e2e6f0); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden; }
        .dark .pos-card-img { background: linear-gradient(145deg, #252936, #1e212b); }
        .pos-card-img img { width: 100%; height: 100%; object-fit: cover; }
        .pos-card-placeholder { font-size: 42px; line-height: 1; }
        .pos-card-badge { position: absolute; top: 8px; left: 8px; font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 3px 8px; border-radius: 999px; }
        .pos-card-badge.dog { background: #dbeafe; color: #1d4ed8; }
        .pos-card-badge.cat { background: #fef3c7; color: #b45309; }
        .pos-card-cart-qty { position: absolute; top: 8px; right: 8px; background: var(--pos-primary); color: #fff; font-size: 11px; font-weight: 800; min-width: 22px; height: 22px; border-radius: 999px; display: flex; align-items: center; justify-content: center; }
        .pos-card-body { padding: 10px 12px 12px; }
        .pos-card-name { font-size: 13px; font-weight: 700; color: var(--pos-text); line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 2.6em; }
        .pos-card-meta { font-size: 11px; color: var(--pos-muted); margin-top: 4px; }
        .pos-card-price { font-size: 16px; font-weight: 800; color: var(--pos-primary); margin-top: 6px; font-variant-numeric: tabular-nums; }
        .pos-card-stock { font-size: 10px; color: var(--pos-muted); margin-top: 2px; }
        .pos-cart-panel { background: var(--pos-card); border: 1px solid var(--pos-border); border-radius: 16px; display: flex; flex-direction: column; max-height: calc(100vh - 10rem); position: sticky; top: 0; }
        @media (max-width: 1024px) { .pos-cart-panel { max-height: none; position: static; } }
        .pos-cart-header { padding: 16px 18px; border-bottom: 1px solid var(--pos-border); }
        .pos-cart-title { font-size: 15px; font-weight: 800; color: var(--pos-text); }
        .pos-cart-branch { font-size: 12px; color: var(--pos-muted); margin-top: 2px; }
        .pos-cart-items { flex: 1; overflow-y: auto; padding: 8px; min-height: 120px; }
        .pos-cart-empty { text-align: center; padding: 40px 16px; color: var(--pos-muted); font-size: 14px; }
        .pos-cart-item { display: flex; gap: 10px; padding: 10px; border-radius: 12px; margin-bottom: 6px; background: var(--pos-bg); align-items: center; }
        .pos-cart-thumb { width: 48px; height: 48px; border-radius: 10px; background: #e8eaef; flex-shrink: 0; overflow: hidden; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .pos-cart-thumb img { width: 100%; height: 100%; object-fit: cover; }
        .pos-cart-info { flex: 1; min-width: 0; }
        .pos-cart-name { font-size: 13px; font-weight: 700; color: var(--pos-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .pos-cart-sub { font-size: 11px; color: var(--pos-muted); }
        .pos-cart-qty-row { display: flex; align-items: center; gap: 6px; margin-top: 4px; }
        .pos-qty-btn { width: 28px; height: 28px; border-radius: 10px; border: 1px solid var(--pos-border); background: var(--pos-card); color: var(--pos-text); font-weight: 900; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 15px; transition: .12s; }
        .pos-qty-btn:hover { background: rgba(122,137,194,.12); border-color: var(--pos-primary); color: var(--pos-primary); }
        .pos-qty-num { font-size: 13px; font-weight: 800; min-width: 20px; text-align: center; color: var(--pos-text); }
        .pos-cart-line-total { font-size: 14px; font-weight: 800; color: var(--pos-text); font-variant-numeric: tabular-nums; white-space: nowrap; }
        .pos-cart-remove { background: none; border: none; color: #ef4444; cursor: pointer; font-size: 18px; padding: 4px; line-height: 1; opacity: .6; }
        .pos-cart-remove:hover { opacity: 1; }
        .pos-cart-footer { padding: 16px 18px; border-top: 1px solid var(--pos-border); }
        .pos-total-row { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 14px; }
        .pos-total-label { font-size: 13px; color: var(--pos-muted); }
        .pos-total-items { font-size: 12px; color: var(--pos-muted); }
        .pos-total-amount { font-size: 28px; font-weight: 900; color: var(--pos-primary); font-variant-numeric: tabular-nums; }
        .pos-pay-row { display: flex; gap: 8px; margin-bottom: 12px; }
        .pos-pay-btn { flex: 1; padding: 11px 10px; border-radius: 12px; border: 1px solid var(--pos-border); background: var(--pos-card); color: var(--pos-muted); font-weight: 800; font-size: 13px; cursor: pointer; transition: .12s; }
        .pos-pay-btn:hover { border-color: var(--pos-primary); color: var(--pos-primary); }
        .pos-pay-btn.active { border-color: var(--pos-primary); background: rgba(122,137,194,.18); color: var(--pos-primary); box-shadow: 0 0 0 3px rgba(122,137,194,.12); }
        .pos-charge { width: 100%; padding: 16px; border-radius: 14px; border: none; font-size: 17px; font-weight: 900; cursor: pointer; transition: transform .12s, box-shadow .12s, background .12s; letter-spacing: .01em; }
        .pos-charge.enabled { background: linear-gradient(135deg, var(--pos-primary), var(--pos-primary-dark)); color: #fff; box-shadow: 0 14px 30px rgba(122,137,194,.30); }
        .pos-charge.enabled:hover { box-shadow: 0 16px 36px rgba(122,137,194,.38); transform: translateY(-1px); }
        .pos-charge.enabled:active { transform: scale(.99); }
        .pos-charge.disabled { background: var(--pos-border); color: var(--pos-muted); cursor: not-allowed; }
        .pos-clear { display: block; width: 100%; margin-top: 8px; background: none; border: none; color: var(--pos-muted); font-size: 12px; cursor: pointer; text-decoration: underline; }
    </style>

    <div class="pos">
        <div class="pos-shell">
            {{-- LEFT: Products --}}
            <div class="pos-main">
                <div class="pos-toolbar">
                    <form wire:submit="scanSku" class="pos-scan-form">
                        <input
                            wire:model="sku"
                            type="text"
                            class="pos-scan-input"
                            placeholder="Scan barcode or type SKU…"
                            autofocus
                            autocomplete="off"
                        />
                        <button type="submit" class="pos-btn pos-btn-primary">Add Product</button>
                    </form>

                    <div class="pos-filters">
                        <button type="button" wire:click="$set('category_filter', 'all')"
                            class="pos-filter {{ $category_filter === 'all' ? 'active' : '' }}">All</button>
                        <button type="button" wire:click="$set('category_filter', 'dog')"
                            class="pos-filter {{ $category_filter === 'dog' ? 'active' : '' }}">Dog</button>
                        <button type="button" wire:click="$set('category_filter', 'cat')"
                            class="pos-filter {{ $category_filter === 'cat' ? 'active' : '' }}">Cat</button>
                    </div>

                    <input wire:model.live.debounce.300ms="search" type="text" class="pos-search" placeholder="Search…" />
                </div>

                <div class="pos-grid-wrap">
                    <div class="pos-grid">
                        @forelse($this->products as $product)
                            <button
                                type="button"
                                wire:click="addProduct({{ $product['id'] }})"
                                wire:key="product-{{ $product['id'] }}"
                                class="pos-card {{ $product['stock'] < 1 ? 'out-of-stock' : '' }}"
                            >
                                <div class="pos-card-img">
                                    @if($product['image'])
                                        <img src="{{ $product['image'] }}" alt="" loading="lazy" />
                                    @else
                                        <span class="pos-card-placeholder">{{ $product['category'] === 'cat' ? '🐈' : '🐕' }}</span>
                                    @endif
                                    <span class="pos-card-badge {{ $product['category'] }}">{{ $product['category'] }}</span>
                                    @if($product['in_cart'] > 0)
                                        <span class="pos-card-cart-qty">{{ $product['in_cart'] }}</span>
                                    @endif
                                </div>
                                <div class="pos-card-body">
                                    <div class="pos-card-name">{{ $product['name'] }}</div>
                                    <div class="pos-card-meta">{{ $product['subcategory'] }} · {{ $product['size'] }}</div>
                                    <div class="pos-card-price">${{ number_format($product['price'], 2) }}</div>
                                    <div class="pos-card-stock">{{ $product['stock'] }} in stock</div>
                                </div>
                            </button>
                        @empty
                            <div style="grid-column: 1/-1; text-align:center; padding: 60px 20px; color: var(--pos-muted);">
                                <p style="font-size:16px; font-weight:700; margin:0 0 8px;">No products found</p>
                                <p style="font-size:13px; margin:0;">Import your Excel sheet under Catalog → Products</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- RIGHT: Cart --}}
            <aside class="pos-cart-panel">
                <div class="pos-cart-header">
                    <div class="pos-cart-title">Current order</div>
                    <div class="pos-cart-branch">{{ $this->branchName }}</div>
                </div>

                <div class="pos-cart-items">
                    @if(empty($cart))
                        <div class="pos-cart-empty">
                            Tap a product or scan a barcode<br>to start the sale
                        </div>
                    @else
                        @foreach($cart as $item)
                            <div class="pos-cart-item" wire:key="cart-{{ $item['id'] }}">
                                <div class="pos-cart-thumb">
                                    @if(!empty($item['image']))
                                        <img src="{{ $item['image'] }}" alt="" />
                                    @else
                                        {{ $item['category'] === 'cat' ? '🐈' : '🐕' }}
                                    @endif
                                </div>
                                <div class="pos-cart-info">
                                    <div class="pos-cart-name">{{ $item['name'] }}</div>
                                    <div class="pos-cart-sub">${{ number_format($item['price'], 2) }} · {{ $item['size'] ?? '' }}</div>
                                    <div class="pos-cart-qty-row">
                                        <button type="button" class="pos-qty-btn" wire:click="decrementQty({{ $item['id'] }})">−</button>
                                        <span class="pos-qty-num">{{ $item['qty'] }}</span>
                                        <button type="button" class="pos-qty-btn" wire:click="incrementQty({{ $item['id'] }})">+</button>
                                    </div>
                                </div>
                                <div class="pos-cart-line-total">${{ number_format($item['price'] * $item['qty'], 2) }}</div>
                                <button type="button" class="pos-cart-remove" wire:click="removeItem({{ $item['id'] }})" title="Remove">×</button>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="pos-cart-footer">
                    <div class="pos-total-row">
                        <div>
                            <div class="pos-total-label">Total</div>
                            <div class="pos-total-items">{{ $this->getItemCount() }} items</div>
                        </div>
                        <div class="pos-total-amount">${{ number_format($this->getTotal(), 2) }}</div>
                    </div>

                    <div class="pos-pay-row">
                        <button type="button" wire:click="$set('payment_method', 'cash')"
                            class="pos-pay-btn {{ $payment_method === 'cash' ? 'active' : '' }}">Cash</button>
                        <button type="button" wire:click="$set('payment_method', 'card')"
                            class="pos-pay-btn {{ $payment_method === 'card' ? 'active' : '' }}">Card</button>
                    </div>

                    <button
                        type="button"
                        wire:click="checkout"
                        wire:loading.attr="disabled"
                        class="pos-charge {{ empty($cart) ? 'disabled' : 'enabled' }}"
                        @if(empty($cart)) disabled @endif
                    >
                        <span wire:loading.remove wire:target="checkout">Complete sale</span>
                        <span wire:loading wire:target="checkout">Processing…</span>
                    </button>

                    @if(!empty($cart))
                        <button type="button" wire:click="clearCart" class="pos-clear">Clear cart</button>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</x-filament-panels::page>
