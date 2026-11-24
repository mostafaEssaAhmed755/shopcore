<?php

declare(strict_types=1);

namespace App\Actions;

use App\Contracts\CartTokenGenerator;
use App\Models\Cart;
use App\Models\User;

final readonly class ResolveCart
{
    public function __construct(private CartTokenGenerator $tokens) {}

    public function execute(?User $user = null, ?string $token = null): Cart
    {
        if ($user !== null) {
            return $user->cart ?? $user->cart()->create();
        }

        if ($token !== null) {
            $cart = Cart::whereNull('user_id')->where('token', $token)->first();

            if ($cart !== null) {
                return $cart;
            }
        }

        return Cart::create(['token' => $this->tokens->generate()]);
    }
}
