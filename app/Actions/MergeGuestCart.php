<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final readonly class MergeGuestCart
{
    public function __construct(private ResolveCart $resolveCart) {}

    public function execute(User $user, Cart $guestCart): Cart
    {
        if ($guestCart->user_id !== null) {
            throw new InvalidArgumentException('this cart belongs to a user');
        }

        return DB::transaction(function () use ($user, $guestCart) {
            $userCart = $this->resolveCart->execute(user: $user);
            $existingIds = $userCart->items()->pluck('product_variant_id');
            $guestCart->items()->whereNotIn('product_variant_id', $existingIds)->update(['cart_id' => $userCart->id]);
            $guestCart->delete();

            return $userCart;
        });
    }
}
