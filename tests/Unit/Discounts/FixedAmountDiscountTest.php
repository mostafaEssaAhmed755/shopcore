<?php

use App\Discounts\FixedAmountDiscount;
use App\ValueObjects\Money;

it('subtracts a fixed amount', function () {
    $discount = (new FixedAmountDiscount)->calculate(new Money(20000, 'EGP'), 3750);
    expect($discount->amount)->toBe(3750);
});

it('never exceeds the subtotal', function () {
    $discount = (new FixedAmountDiscount)->calculate(new Money(3000, 'EGP'), 5000);
    expect($discount->amount)->toBe(3000);
});

it('keeps the subtotal currency', function () {
    $discount = (new FixedAmountDiscount)->calculate(new Money(3000, 'USD'), 5000);
    expect($discount->currency)->toBe('USD');
});

it('rejects a value less than 1', function (int $value) {
    expect(fn () => (new FixedAmountDiscount)->calculate(new Money(3000, 'EGP'), $value))
        ->toThrow(InvalidArgumentException::class);
})->with([0, -100]);
