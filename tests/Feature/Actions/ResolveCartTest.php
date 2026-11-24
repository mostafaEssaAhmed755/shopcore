<?php

use App\Actions\ResolveCart;
use App\Contracts\CartTokenGenerator;
use App\Models\Cart;
use App\Models\User;

beforeEach(function () {
    $this->app->instance(CartTokenGenerator::class, new class implements CartTokenGenerator
    {
        #[Override]
        public function generate(): string
        {
            return '453cf585-9a69-4c0e-84cd-236514cbe50e';
        }
    });
});

it('returns the existing cart of a user', function () {
    $user = User::factory()->create();
    $userCart = Cart::factory()->create(['user_id' => $user->id]);

    $cart = app(ResolveCart::class)->execute(user: $user);

    expect($cart->user_id)->toBe($user->id)
        ->and($cart->id)->toBe($userCart->id);

});

it('creates a cart for a user without one', function () {
    $user = User::factory()->create();

    $cart = app(ResolveCart::class)->execute(user: $user);

    expect($cart->user_id)->toBe($user->id)
        ->and($cart->token)->toBeNull();
});

it('returns the guest cart matching the token', function () {
    $token = (app(CartTokenGenerator::class))->generate();
    Cart::factory()->create(['token' => $token, 'user_id' => null]);

    $cart = app(ResolveCart::class)->execute(token: $token);

    expect($cart->token)->toBe($token);
});

it('creates a new guest cart when the token is unknown', function () {
    $cart = app(ResolveCart::class)->execute(token: 'unknown-token');

    expect($cart->token)->toBe('453cf585-9a69-4c0e-84cd-236514cbe50e')
        ->and(Cart::count())->toBe(1);
});

it('creates a new guest cart when no token is given', function () {
    app(ResolveCart::class)->execute();

    expect(Cart::count())->toBe(1);
});

it('does not return a user cart for a guest token', function () {
    $user = User::factory()->create();
    $userCart = Cart::factory()->create([
        'user_id' => $user->id,
        'token' => '00000000-0000-0000-0000-000000000099',
    ]);

    $cart = app(ResolveCart::class)->execute(token: '00000000-0000-0000-0000-000000000099');

    expect($cart->id)->not()->toBe($userCart->id)
        ->and($cart->user_id)->toBeNull();
});

it('ignores the guest token when a user is given', function () {
    $guestCart = Cart::factory()->create([
        'user_id' => null,
        'token' => '00000000-0000-0000-0000-000000000099',
    ]);
    $user = User::factory()->create();

    $cart = app(ResolveCart::class)->execute(user: $user, token: $guestCart->token);

    expect($cart->user_id)->toBe($user->id)
        ->and($cart->id)->not->toBe($guestCart->id);
});
