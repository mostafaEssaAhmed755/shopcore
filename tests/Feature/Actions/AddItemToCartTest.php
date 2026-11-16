<?php

use App\Actions\AddItemToCart;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductNotAvailableException;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;

it('adds a new variant to the cart', function () {
    $cart = Cart::factory()->create();
    $variant = ProductVariant::factory()->create(['stock' => 5]);

    $item = (new AddItemToCart)->execute(cart: $cart, variant: $variant, quantity: 2);

    expect($item->quantity)->toBe(2)
        ->and($item->product_variant_id)->toBe($variant->id)
        ->and($item->cart_id)->toBe($cart->id);
});

it('increases the quantity when the variant is already in the cart', function () {
    $cart = Cart::factory()->create();
    $variant = ProductVariant::factory()->create(['stock' => 5]);

    (new AddItemToCart)->execute(cart: $cart, variant: $variant, quantity: 1);

    $item = (new AddItemToCart)->execute(cart: $cart, variant: $variant, quantity: 1);

    expect($item->quantity)->toBe(2);
});

it('rejects a quantity less than 1', function () {
    $cart = Cart::factory()->create();
    $variant = ProductVariant::factory()->create(['stock' => 5]);

    expect(fn () => (new AddItemToCart)->execute(cart: $cart, variant: $variant, quantity: 0))
        ->toThrow(InvalidArgumentException::class);
});

it('rejects a quantity greater than available stock', function () {
    $cart = Cart::factory()->create();
    $variant = ProductVariant::factory()->create(['stock' => 5]);

    expect(fn () => (new AddItemToCart)->execute(cart: $cart, variant: $variant, quantity: 6))
        ->toThrow(InsufficientStockException::class);
});

it('rejects when existing + new quantity exceeds stock', function () {
    $cart = Cart::factory()->create();
    $variant = ProductVariant::factory()->create(['stock' => 5]);

    (new AddItemToCart)->execute(cart: $cart, variant: $variant, quantity: 4);

    expect(fn () => (new AddItemToCart)->execute(cart: $cart, variant: $variant, quantity: 3))
        ->toThrow(InsufficientStockException::class);
});

it('rejects an unpublished product', function () {
    $cart = Cart::factory()->create();
    $product = Product::factory()->create(['is_published' => false]);
    $variant = ProductVariant::factory()->create(['stock' => 5, 'product_id' => $product->id]);

    expect(fn () => (new AddItemToCart)->execute(cart: $cart, variant: $variant, quantity: 2))
        ->toThrow(ProductNotAvailableException::class);
});

it('does not touch other carts that have the same variant', function () {
    $cart1 = Cart::factory()->create();
    $cart2 = Cart::factory()->create();
    $variant = ProductVariant::factory()->create(['stock' => 5]);

    (new AddItemToCart)->execute(cart: $cart1, variant: $variant, quantity: 2);
    $item = (new AddItemToCart)->execute(cart: $cart2, variant: $variant, quantity: 2);

    expect($item->quantity)
        ->toBe(2);
});

it('variant stock 3، cart stock 4', function () {
    $cart = Cart::factory()->create();
    $variant = ProductVariant::factory()->create(['stock' => 4]);

    (new AddItemToCart)->execute(cart: $cart, variant: $variant, quantity: 4);

    $variant->decrement('stock', 1);

    expect(fn () => (new AddItemToCart)->execute(cart: $cart, variant: $variant, quantity: 1))
        ->toThrow(InsufficientStockException::class);
});
