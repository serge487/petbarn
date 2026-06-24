<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Pushes signed inventory webhooks to the PawsNation storefront so its
 * mirrored stock stays in sync in real time (POS sales, admin edits, transfers).
 */
class PawsNationNotifier
{
    public function inventoryChanged(int $petbarnBranchId, int $petbarnProductId, int $quantity, string $event = 'inventory.updated'): void
    {
        $url = (string) config('sync.pawsnation.webhook_url');
        $secret = (string) config('sync.webhook_secret');

        if ($url === '' || $secret === '') {
            return; // Sync not configured; nothing to do.
        }

        $payload = [
            'event'              => $event,
            'petbarn_branch_id'  => $petbarnBranchId,
            'petbarn_product_id' => $petbarnProductId,
            'quantity'           => $quantity,
            'occurred_at'        => now()->toIso8601String(),
        ];

        // Sign the exact JSON body so PawsNation can verify integrity + origin.
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $signature = hash_hmac('sha256', $body, $secret);

        try {
            Http::timeout((int) config('sync.pawsnation.timeout', 8))
                ->withHeaders([
                    'Content-Type'        => 'application/json',
                    'X-PetBarn-Signature' => 'sha256='.$signature,
                ])
                ->withBody($body, 'application/json')
                ->post($url)
                ->throw();
        } catch (\Throwable $e) {
            // Never let a storefront outage break a POS sale; the nightly
            // reconciliation pull will heal any missed webhook.
            Log::warning('PawsNation inventory webhook failed', [
                'url'   => $url,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
