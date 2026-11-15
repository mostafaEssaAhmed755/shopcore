<?php

declare(strict_types=1);

namespace App\Actions;

use App\Exceptions\DuplicateAttributeException;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;
use App\ValueObjects\Money;
use Illuminate\Support\Facades\DB;

final readonly class CreateProductVariant
{
    public function execute(Product $product, string $sku, Money $price, int $stock, array $valueIds): ProductVariant
    {
        $attributeIds = AttributeValue::whereIn('id', $valueIds)->pluck('attribute_id');

        $duplicates = collect($attributeIds)->duplicates();

        if ($duplicates->isNotEmpty()) {
            throw new DuplicateAttributeException('A variant cannot have more than one value per attribute (attribute IDs: '.$duplicates->unique()->implode(', ').')');
        }

        return DB::transaction(function () use ($product, $sku, $price, $stock, $valueIds) {
            $variant = $product->variants()->create([
                'sku' => $sku,
                'stock' => $stock,
                'price' => $price->amount,
            ]);

            $variant->attributeValues()->attach($valueIds);

            return $variant;
        });
    }
}
