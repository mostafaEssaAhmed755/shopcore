<?php

declare(strict_types=1);

namespace App\Enums;

use App\Discounts\DiscountStrategy;
use App\Discounts\FixedAmountDiscount;
use App\Discounts\PercentageDiscount;

enum CouponType: string
{
    case Percentage = 'percentage';
    case Fixed = 'fixed';

    public function strategy(): DiscountStrategy
    {
        return match ($this) {
            self::Percentage => new PercentageDiscount,
            self::Fixed => new FixedAmountDiscount
        };
    }
}
