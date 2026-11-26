<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Database\QueryException;

it('has many items', function () {
    $cart = Cart::factory()->create();

    CartItem::factory()->count(3)->create(['cart_id' => $cart->id]);

    expect($cart->items)->toHaveCount(3);
});

it('belongs to a user', function () {
    $user = User::factory()->create();

    $cart = Cart::factory()->create(['user_id' => $user->id]);

    expect($cart->user->id)->toBe($user->id);
});

it('rejects duplicate tokens', function () {

    expect(fn () => Cart::factory()->count(2)->create(['token' => '453cf585-9a69-4c0e-84cd-236514cbe50e'])
    )->toThrow(QueryException::class);
});

it('allows many guest carts', function () {

    Cart::factory()->count(3)->create();

    expect(Cart::count())->toBe(3);
});

it('rejects a second cart for the same user', function () {
    $user = User::factory()->create();

    expect(fn () => Cart::factory()->count(2)->create(['user_id' => $user->id]))
        ->toThrow(QueryException::class);
});

it('guest scope returns only carts without a user', function () {
    Cart::factory()->create();
    $guestCart = Cart::factory()->create(['user_id' => null]);

    expect(Cart::guest()->pluck('id')->all())->toBe([$guestCart->id]);
});
