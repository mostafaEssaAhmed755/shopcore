<?php

declare(strict_types=1);

namespace App\Data;

use App\ValueObjects\Money;

final readonly class CreateVariantData
{
    /** @param list<int> $valueIds */
    public function __construct(
        public string $sku,
        public Money $price,
        public int $stock,
        public array $valueIds
    ) {}
}
