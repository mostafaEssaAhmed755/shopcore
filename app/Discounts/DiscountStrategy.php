<?php

declare(strict_types=1);

namespace App\Discounts;

use App\ValueObjects\Money;

interface DiscountStrategy
{
    /** Returns the discount amount (not the new total). */
    public function calculate(Money $subtotal, int $value): Money;
}
