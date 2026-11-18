<?php

use App\Discounts\PercentageDiscount;
use App\ValueObjects\Money;

it('calculates a percentage of the subtotal', function () {
    $subtotal = new Money(10000, 'EGP');
    $discount = (new PercentageDiscount)->calculate($subtotal, 25);
    expect($discount->amount)->toBe(2500);
});

it('rounds down to the nearest minor unit', function () {
    $subtotal = new Money(999, 'EGP');
    $discount = (new PercentageDiscount)->calculate($subtotal, 15);
    expect($discount->amount)->toBe(149);
});

it('keeps the subtotal currency', function () {
    $subtotal = new Money(10000, 'USD');
    $discount = (new PercentageDiscount)->calculate($subtotal, 10);
    expect($discount->currency)->toBe('USD');
});

it('rejects a percentage outside 1–100', function (int $value) {
    expect(fn () => (new PercentageDiscount)->calculate(new Money(10000, 'USD'), $value))
        ->toThrow(InvalidArgumentException::class);
})->with([0, 101, -5]);
