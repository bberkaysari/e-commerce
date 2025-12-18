<?php

namespace App\Services\Cart;

use App\Exceptions\DomainException;
use App\Models\Cart;
use App\Repositories\Interfaces\CartItemRepositoryInterface;
use App\Repositories\Interfaces\CartRepositoryInterface;
use App\Services\Interfaces\RemoveCartItemServiceInterface;
use Illuminate\Support\Facades\Log;

class RemoveCartItemService implements RemoveCartItemServiceInterface
{
    public function __construct(
        private readonly CartRepositoryInterface $cartRepository,
        private readonly CartItemRepositoryInterface $cartItemRepository,
    ) {}

    public function execute(array $data): ?Cart
    {
        $userId = (int) $data['user_id'];
        $variantId = (int) $data['variant_id'];

        try {
            $cart = $this->cartRepository->getOrCreateActiveCart($userId);

            $item = $this->cartItemRepository
                ->findByCartAndVariant($cart->id, $variantId);

            if ($item) {
                $this->cartItemRepository->delete($item);
            }

            return $this->cartRepository->getActiveCartWithItems($userId);
        } catch (\Throwable $e) {
            Log::error('RemoveCartItem failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'variant_id' => $variantId,
            ]);

            throw new DomainException('Unexpected error while removing cart item', 500);
        }
    }
}
