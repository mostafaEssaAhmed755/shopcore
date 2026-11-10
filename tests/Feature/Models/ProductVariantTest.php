<?php

use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\QueryException;

it('belongs to a product', function () {
    $product = Product::factory()->create();

    $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

    expect($variant->product->id)->toBe($product->id);
});

it('rejects duplicate skus', function () {

    expect(fn() => 
        ProductVariant::factory()->count(2)->create(['sku' => 'SKU-1234-FD'])
    )->toThrow(QueryException::class);
});

it('is deleted when its product is deleted', function () {
    $product = Product::factory()->create();

    $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

    $product->delete();
    expect(ProductVariant::find($variant->id))->toBeNull();
});

it('has many attribute values', function () {
    $variant = ProductVariant::factory()->create();

    $values = AttributeValue::factory()->count(3)->create();

    $variant->attributeValues()->attach($values->pluck('id'));

    expect($variant->attributeValues)->toHaveCount(3);
});

it('stores timestamps on the attribute value product variant pivot', function () {
    $attributeValue = AttributeValue::factory()->create();
    $variant = ProductVariant::factory()->create();

    $variant->attributeValues()->attach($attributeValue->id);

    expect($variant->attributeValues->first()->pivot->created_at)->not->toBeNull();
});