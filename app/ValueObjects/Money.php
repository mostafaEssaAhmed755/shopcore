<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;

final readonly class Money
{
    public function __construct(
        public int $amount,
        public string $currency,
    ) {}

    public function add(Money $other): Money
    {
        if ($other->currency !== $this->currency) {
            throw new InvalidArgumentException("Cannot add {$other->currency} to {$this->currency}");
        }

        return new Money(amount: $this->amount + $other->amount, currency: $this->currency);
    }

    public function multiply(int $quantity): Money
    {
        if ($quantity < 1) {
            throw new InvalidArgumentException("Quantity must be at least 1, got {$quantity}");
        }

        return new Money(amount: $this->amount * $quantity, currency: $this->currency);
    }

    public function format(): string
    {

        $result = number_format($this->amount / 100, 2, '.', '');

        return "{$result} {$this->currency}";
    }

    public static function fromDecimal(string $amount, string $currency): self
    {
        if (preg_match('/^\d+(\.\d{1,2})?\z/', $amount) !== 1) {
            throw new InvalidArgumentException("Invalid decimal amount format: {$amount}");
        }

        $parts = explode('.', $amount);
        $whole = $parts[0];
        $fraction = $parts[1] ?? '';

        $paddedFraction = str_pad($fraction, 2, '0', STR_PAD_RIGHT);
        $minorUnits = (int) ($whole.$paddedFraction);

        return new self($minorUnits, $currency);
    }
}
