<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Database\QueryException;

it('belongs to a cart', function () {
    $cart = Cart::factory()->create();

    $item = CartItem::factory()->create(['cart_id' => $cart->id]);

    expect($item->cart->id)->toBe($cart->id);
});

it('belongs to a product variant', function () {
    $variant = ProductVariant::factory()->create();

    $item = CartItem::factory()->create(['product_variant_id' => $variant->id]);

    expect($item->variant->id)->toBe($variant->id);
});

it('rejects duplicate product variant for the same cart', function () {
    $cart = Cart::factory()->create();
    $variant = ProductVariant::factory()->create();

    expect(fn () => CartItem::factory()->count(2)->create(['cart_id' => $cart->id, 'product_variant_id' => $variant->id])
    )->toThrow(QueryException::class);
});
