<?php

use App\Discounts\CappedDiscount;
use App\Discounts\DiscountStrategy;
use App\Discounts\FixedAmountDiscount;
use App\Discounts\PercentageDiscount;
use App\ValueObjects\Money;

dataset('strategies', [
    'percentage' => [new PercentageDiscount, 100],
    'fixed' => [new FixedAmountDiscount, 100],
    'capped percentage' => [new CappedDiscount(new PercentageDiscount, 5_000), 100],
]);

dataset('subtotals', [
    'tiny subtotal' => [50],
    'equal subtotal' => [100],
    'big subtotal' => [100_000],
]);

it('returns money in the subtotal currency', function (DiscountStrategy $discount, int $value, int $amount) {
    expect($discount->calculate(new Money($amount, 'USD'), $value)->currency)->toBe('USD');
})->with('strategies')->with('subtotals');

it('never discounts more than the subtotal', function (DiscountStrategy $discount, int $value, int $amount) {
    expect($discount->calculate(new Money($amount, 'USD'), $value)->amount)->toBeLessThanOrEqual($amount);
})->with('strategies')->with('subtotals');

it('never returns a negative discount', function (DiscountStrategy $discount, int $value, int $amount) {
    expect($discount->calculate(new Money($amount, 'USD'), $value)->amount)->toBeGreaterThanOrEqual(0);
})->with('strategies')->with('subtotals');
