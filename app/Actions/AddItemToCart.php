<?php

declare(strict_types=1);

namespace App\Actions;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductNotAvailableException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use InvalidArgumentException;

final readonly class AddItemToCart
{
    public function execute(Cart $cart, ProductVariant $variant, int $quantity): CartItem
    {
        if ($quantity < 1) {
            throw new InvalidArgumentException("A quantity must be at least 1, your quantity: $quantity");
        }

        if (! $variant->product->is_published) {
            throw new ProductNotAvailableException('this product variant is not available for now');
        }

        $item = $cart->items()->where('product_variant_id', $variant->id)->first();

        $alreadyInCart = $item?->quantity ?? 0;

        if ($quantity + $alreadyInCart > $variant->stock) {
            $availableToAdd = max(0, $variant->stock - $alreadyInCart);

            throw new InsufficientStockException(
                "Only {$availableToAdd} more can be added (stock: {$variant->stock}, already in cart: {$alreadyInCart})"
            );
        }

        if ($item) {
            $item->increment('quantity', $quantity);

            return $item;
        }

        return $cart->items()->create(['product_variant_id' => $variant->id, 'quantity' => $quantity]);
    }
}
