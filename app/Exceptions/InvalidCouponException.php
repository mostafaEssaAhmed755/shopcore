<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\ValueObjects\Money;
use DomainException;

final class InvalidCouponException extends DomainException
{
    public static function inactive(): self
    {
        return new self('This coupon is no longer active.');
    }

    public static function expired(): self
    {
        return new self('This coupon is expired.');
    }

    public static function notStarted(): self
    {
        return new self('This coupon has not started yet.');
    }

    public static function usageLimitReached(): self
    {
        return new self('This coupon usage limit reached.');
    }

    public static function belowMinimum(Money $minimum): self
    {
        return new self("Minimum order is {$minimum->format()}");
    }
}
