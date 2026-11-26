<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CreateVariantData;
use App\Events\VariantCreated;
use App\Exceptions\DuplicateAttributeException;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

final readonly class CreateProductVariant
{
    public function execute(Product $product, CreateVariantData $data): ProductVariant
    {
        $attributeIds = AttributeValue::whereIn('id', $data->valueIds)->pluck('attribute_id');

        $duplicates = $attributeIds->duplicates();

        if ($duplicates->isNotEmpty()) {
            throw new DuplicateAttributeException('A variant cannot have more than one value per attribute (attribute IDs: '.$duplicates->unique()->implode(', ').')');
        }

        return DB::transaction(function () use ($product, $data) {
            $variant = $product->variants()->create([
                'sku' => $data->sku,
                'stock' => $data->stock,
                'price' => $data->price->amount,
            ]);

            $variant->attributeValues()->attach($data->valueIds);

            event(new VariantCreated($variant));

            return $variant;
        });
    }
}
