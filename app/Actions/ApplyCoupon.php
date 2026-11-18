<?php

declare(strict_types=1);

namespace App\Actions;

use App\Exceptions\InvalidCouponException;
use App\Models\Coupon;
use App\ValueObjects\Money;

final readonly class ApplyCoupon
{
    public function execute(Coupon $coupon, Money $subtotal): Money
    {
        if (! $coupon->is_active) {
            throw InvalidCouponException::inactive();
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            throw InvalidCouponException::expired();
        }

        if ($coupon->starts_at && $coupon->starts_at->isFuture()) {
            throw InvalidCouponException::notStarted();
        }

        if ($coupon->max_uses !== null && $coupon->max_uses <= $coupon->used_count) {
            throw InvalidCouponException::usageLimitReached();
        }

        if ($coupon->min_subtotal && $coupon->min_subtotal > $subtotal->amount) {
            throw InvalidCouponException::belowMinimum(new Money($coupon->min_subtotal, $subtotal->currency));
        }

        return $coupon->type->strategy()->calculate($subtotal, $coupon->value);
    }
}
