<?php

use App\Discounts\DiscountStrategy;
use App\Enums\CouponType;

it('maps every coupon type to a discount strategy', function (CouponType $type) {
    expect($type->strategy())->toBeInstanceOf(DiscountStrategy::class);
})->with(CouponType::cases());
