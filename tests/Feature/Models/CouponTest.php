<?php

use App\Enums\CouponType;
use App\Models\Coupon;
use Illuminate\Database\QueryException;

it('casts type to CouponType enum', function () {
    $coupon = Coupon::factory()->create(['type' => CouponType::Percentage]);
    expect($coupon->fresh()->type)->toBe(CouponType::Percentage);
});

it('rejects duplicate codes', function () {
    expect(fn () => Coupon::factory()->count(2)->create(['code' => 'SDAD12']))
        ->toThrow(QueryException::class);
});
