<?php

declare(strict_types=1);

namespace App\Discounts;

use App\ValueObjects\Money;
use InvalidArgumentException;
use Override;

final class FixedAmountDiscount implements DiscountStrategy
{
    #[Override]
    public function calculate(Money $subtotal, int $value): Money
    {
        if ($value < 1) {
            throw new InvalidArgumentException("Fixed discount must be at least 1, got {$value}");
        }

        return new Money(min($subtotal->amount, $value), $subtotal->currency);
    }
}
