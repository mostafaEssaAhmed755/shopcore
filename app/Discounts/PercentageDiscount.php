<?php

declare(strict_types=1);

namespace App\Discounts;

use App\ValueObjects\Money;
use InvalidArgumentException;
use Override;

final class PercentageDiscount implements DiscountStrategy
{
    #[Override]
    public function calculate(Money $subtotal, int $value): Money
    {
        if ($value < 1 || $value > 100) {
            throw new InvalidArgumentException("Percentage Discount must be in range 1-100, got {$value}");
        }

        return new Money(intdiv($subtotal->amount * $value, 100), $subtotal->currency);
    }
}
