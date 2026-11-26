<?php

use App\Actions\MergeGuestCart;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;

it('moves guest-only items to the user cart', function () {
    $user = User::factory()->create();
    $userCart = Cart::factory()->create(['user_id' => $user->id]);
    $guestCart = Cart::factory()->create(['user_id' => null]);
    $guestItem = CartItem::factory()->create(['cart_id' => $guestCart->id]);

    app(MergeGuestCart::class)->execute(user: $user, guestCart: $guestCart);

    expect($guestItem->fresh()->cart_id)->toBe($userCart->id);
});

it('keeps the user quantity when the variant is in both carts', function () {
    $user = User::factory()->create();
    $variant = ProductVariant::factory()->create();

    $userCart = Cart::factory()->create(['user_id' => $user->id]);
    $userItem = CartItem::factory()->create([
        'cart_id' => $userCart->id,
        'product_variant_id' => $variant->id,
        'quantity' => 2,
    ]);

    $guestCart = Cart::factory()->create(['user_id' => null]);
    CartItem::factory()->create([
        'cart_id' => $guestCart->id,
        'product_variant_id' => $variant->id,
        'quantity' => 5,
    ]);

    app(MergeGuestCart::class)->execute(user: $user, guestCart: $guestCart);

    expect($userItem->fresh()->quantity)->toBe(2)
        ->and($userCart->items()->count())->toBe(1);
});

it('deletes the guest cart', function () {
    $user = User::factory()->create();
    Cart::factory()->create(['user_id' => $user->id]);
    $guestCart = Cart::factory()->create(['user_id' => null]);
    CartItem::factory()->create(['cart_id' => $guestCart->id]);

    app(MergeGuestCart::class)->execute(user: $user, guestCart: $guestCart);

    expect(Cart::find($guestCart->id))->toBeNull();
});

it('creates a user cart when the user has none', function () {
    $user = User::factory()->create();
    $guestCart = Cart::factory()->create(['user_id' => null]);
    CartItem::factory()->create(['cart_id' => $guestCart->id]);

    app(MergeGuestCart::class)->execute(user: $user, guestCart: $guestCart);

    $userCart = $user->fresh()->cart;

    expect($userCart)->not()->toBeNull()
        ->and($userCart->items)->toHaveCount(1);
});

it('throws when the given cart belongs to a user', function () {
    $user = User::factory()->create();
    $userCart = Cart::factory()->create();
    CartItem::factory()->create(['cart_id' => $userCart->id]);

    expect(fn () => app(MergeGuestCart::class)->execute(user: $user, guestCart: $userCart))
        ->toThrow(InvalidArgumentException::class);
});
