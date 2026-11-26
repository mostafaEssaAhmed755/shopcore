<?php

declare(strict_types=1);

namespace App\Discounts;

use App\ValueObjects\Money;
use InvalidArgumentException;

final readonly class CappedDiscount implements DiscountStrategy
{
    public function __construct(
        private DiscountStrategy $inner,
        private int $cap,
    ) {
        if ($this->cap < 1) {
            throw new InvalidArgumentException("Capped discount must be at least 1, got {$this->cap}");
        }
    }

    public function calculate(Money $subtotal, int $value): Money
    {

        $discount = $this->inner->calculate($subtotal, $value);

        return new Money(min($discount->amount, $this->cap), $discount->currency);
    }
}
