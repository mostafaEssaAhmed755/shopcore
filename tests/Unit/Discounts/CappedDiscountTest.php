<?php

use App\Discounts\CappedDiscount;
use App\Discounts\FixedAmountDiscount;
use App\Discounts\PercentageDiscount;
use App\ValueObjects\Money;

it('caps the discount when it exceeds the cap', function () {
    $discount = (new CappedDiscount(new PercentageDiscount, 10000))
        ->calculate(new Money(200000, 'USD'), 20);

    expect($discount->amount)->toBe(10000);
});

it('keeps the discount when it is below the cap', function () {
    $discount = (new CappedDiscount(new PercentageDiscount, 10000))
        ->calculate(new Money(30000, 'USD'), 20);

    expect($discount->amount)->toBe(6000);
});

it('returns the cap when the discount equals it', function () {
    $discount = (new CappedDiscount(new PercentageDiscount, 10000))
        ->calculate(new Money(50000, 'USD'), 20);

    expect($discount->amount)->toBe(10000);
});

it('works with a fixed discount too', function () {
    $discount = (new CappedDiscount(new FixedAmountDiscount, 10000))
        ->calculate(new Money(200000, 'USD'), 15000);

    expect($discount->amount)->toBe(10000);
});

it('rejects a cap below 1', function (int $amount) {
    expect(fn () => new CappedDiscount(new FixedAmountDiscount, $amount)
    )->toThrow(InvalidArgumentException::class);
})->with([0, -1]);
