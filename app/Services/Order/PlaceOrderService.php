<?php

namespace App\Services\Order;

use App\Exceptions\CartEmptyException;
use App\Exceptions\DomainException;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\InactiveVariantException;
use App\Exceptions\VariantNotFoundException;
use App\Repositories\Cart\CartRepositoryInterface;
use App\Repositories\Order\OrderAddressRepositoryInterface;
use App\Repositories\Order\OrderItemRepositoryInterface;
use App\Repositories\Order\OrderRepositoryInterface;
use App\Repositories\Variant\VariantRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlaceOrderService
{
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
        private readonly VariantRepositoryInterface $variantRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderItemRepositoryInterface $orderItemRepository,
        private readonly OrderAddressRepositoryInterface $orderAddressRepository,
    ) {}

    public function execute(array $data)
    {
        $userId = (int) $data['user_id'];
        $shipping = (array) ($data['shipping'] ?? []);
        $notes = $data['notes'] ?? null;

        try {
            return DB::transaction(function () use ($userId, $shipping, $notes) {
                $cart = $this->cartRepository->getActiveCartWithItems($userId);

                if (!$cart || $cart->items->isEmpty()) {
                    throw new CartEmptyException();
                }

                $now = now();

                // create order first (cleaner)
                $order = $this->orderRepository->create([
                    'user_id' => $userId,
                    'status' => 'PENDING_PAYMENT',
                    'total_amount' => '0.00', // will update later
                    'currency' => 'TRY',
                    'notes' => $notes,
                ]);

                $itemsRows = [];
                $total = '0.00';

                foreach ($cart->items as $item) {
                    $variantId = (int) $item->product_variant_id;
                    $qty = (int) $item->quantity;

                    $variant = $this->variantRepository->findForUpdate($variantId);
                    if (!$variant) {
                        throw new VariantNotFoundException($variantId);
                    }
                    if (!(bool) $variant->status) {
                        throw new InactiveVariantException($variantId);
                    }

                    $available = (int) $variant->stock_quantity;
                    if ($available < $qty) {
                        throw new InsufficientStockException($variantId, $qty, $available);
                    }

                    $variant->stock_quantity = $available - $qty;
                    $this->variantRepository->save($variant);

                    $unitPrice = (string) $item->unit_price; // keep as string
                    $lineTotal = bcmul($unitPrice, (string) $qty, 2);
                    $total = bcadd($total, $lineTotal, 2);

                    $itemsRows[] = [
                        'order_id' => $order->id,
                        'product_variant_id' => $variant->id,
                        'sku_snapshot' => (string) $variant->sku,
                        'name_snapshot' => (string) ($variant->product?->name ?? ''),
                        'unit_price' => $unitPrice,
                        'quantity' => $qty,
                        'line_total' => $lineTotal,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                $this->orderItemRepository->bulkCreate($itemsRows);

                $this->orderAddressRepository->bulkCreate([[
                    'order_id' => $order->id,
                    'type' => 'shipping',
                    'full_name' => (string) ($shipping['full_name'] ?? ''),
                    'phone' => $shipping['phone'] ?? null,
                    'city' => (string) ($shipping['city'] ?? ''),
                    'district' => $shipping['district'] ?? null,
                    'address_line' => (string) ($shipping['address_line'] ?? ''),
                    'postal_code' => $shipping['postal_code'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]]);

                // update order total
                $this->orderRepository->updateTotals($order, [
                    'total_amount' => $total,
                ]);

                $this->cartRepository->markCheckedOut($cart);

                return $this->orderRepository->findByIdWithDetails($order->id);
            }, 3);
        } catch (DomainException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('PlaceOrder failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
            ]);

            throw new DomainException('Unexpected error while placing order', 500);
        }
    }
}