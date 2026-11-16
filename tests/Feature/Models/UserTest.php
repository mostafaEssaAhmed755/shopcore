<?php

use App\Models\Cart;
use App\Models\User;

it('has one cart', function () {
    $user = User::factory()->create();

    $cart = Cart::factory()->create(['user_id' => $user->id]);

    expect($user->cart->id)->toBe($cart->id);
});
