<?php

use App\Actions\ApplyCoupon;
use App\Enums\CouponType;
use App\Exceptions\InvalidCouponException;
use App\Models\Coupon;
use App\ValueObjects\Money;

it('returns the discount from the coupon strategy', function () {
    $coupon = Coupon::factory()->create(['type' => CouponType::Percentage, 'value' => 25]);
    $subtotal = new Money(10000, 'EGP');
    $discount = (new ApplyCoupon)->execute($coupon, $subtotal);
    expect($discount->amount)->toBe(2500);
});

it('rejects an inactive coupon', function () {
    $coupon = Coupon::factory()->create(['is_active' => false]);
    $subtotal = new Money(10000, 'EGP');
    expect(fn () => (new ApplyCoupon)->execute($coupon, $subtotal))
        ->toThrow(InvalidCouponException::class, InvalidCouponException::inactive()->getMessage());
});

it('rejects an expired coupon', function () {
    $coupon = Coupon::factory()->create(['expires_at' => now()->addDay()]);
    $subtotal = new Money(10000, 'EGP');

    $this->travel(2)->days();

    expect(fn () => (new ApplyCoupon)->execute($coupon, $subtotal))
        ->toThrow(InvalidCouponException::class, InvalidCouponException::expired()->getMessage());
});

it('rejects a coupon that has not started yet', function () {
    $coupon = Coupon::factory()->create(['starts_at' => now()->addDay()]);
    $subtotal = new Money(10000, 'EGP');

    expect(fn () => (new ApplyCoupon)->execute($coupon, $subtotal))
        ->toThrow(InvalidCouponException::class, InvalidCouponException::notStarted()->getMessage());
});

it('rejects a coupon that reached its usage limit', function (int $usedCount) {
    $coupon = Coupon::factory()->create(['max_uses' => 5, 'used_count' => $usedCount]);
    $subtotal = new Money(10000, 'EGP');

    expect(fn () => (new ApplyCoupon)->execute($coupon, $subtotal))
        ->toThrow(InvalidCouponException::class, InvalidCouponException::usageLimitReached()->getMessage());
})->with([5, 6]);

it('allows a coupon with uses remaining', function () {
    $coupon = Coupon::factory()->create(['type' => CouponType::Percentage, 'value' => 25, 'max_uses' => 5, 'used_count' => 4]);
    $subtotal = new Money(10000, 'EGP');
    $discount = (new ApplyCoupon)->execute($coupon, $subtotal);
    expect($discount->amount)->toBe(2500);
});

it('rejects a subtotal below the minimum', function () {
    $coupon = Coupon::factory()->create(['type' => CouponType::Percentage, 'value' => 25, 'min_subtotal' => 50000]);
    $subtotal = new Money(10000, 'EGP');
    expect(fn () => (new ApplyCoupon)->execute($coupon, $subtotal))
        ->toThrow(InvalidCouponException::class, InvalidCouponException::belowMinimum(new Money($coupon->min_subtotal, $subtotal->currency))->getMessage());
});

it('allows a subtotal exactly at the minimum', function () {
    $coupon = Coupon::factory()->create(['type' => CouponType::Percentage, 'value' => 25, 'min_subtotal' => 50000]);
    $subtotal = new Money(50000, 'EGP');
    $discount = (new ApplyCoupon)->execute($coupon, $subtotal);
    expect($discount->amount)
        ->toBe(12500);
});

it('allows unlimited uses when max_uses is null', function () {
    $coupon = Coupon::factory()->create(['type' => CouponType::Percentage, 'value' => 25, 'max_uses' => null, 'used_count' => 1000]);
    $subtotal = new Money(50000, 'EGP');
    $discount = (new ApplyCoupon)->execute($coupon, $subtotal);
    expect($discount->amount)
        ->toBe(12500);
});

it('rejects a coupon with max_uses of zero', function () {
    $coupon = Coupon::factory()->create(['type' => CouponType::Percentage, 'value' => 25, 'max_uses' => 0, 'used_count' => 0]);
    $subtotal = new Money(50000, 'EGP');

    expect(fn () => (new ApplyCoupon)->execute($coupon, $subtotal))
        ->toThrow(InvalidCouponException::class, InvalidCouponException::usageLimitReached()->getMessage());
});
