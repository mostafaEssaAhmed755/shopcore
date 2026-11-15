<?php

use App\Actions\CreateProductVariant;
use App\Exceptions\DuplicateAttributeException;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Product;
use App\Models\ProductVariant;
use App\ValueObjects\Money;
use Illuminate\Database\QueryException;

it('creates a variant with its attribute values', function () {
    $product = Product::factory()->create();
    $red = AttributeValue::factory()->create();
    $xl = AttributeValue::factory()->create();

    $price = new Money(15000, 'EGP');
    $variant = (new CreateProductVariant)->execute(
        product: $product,
        sku: 'TS-RED-XL',
        price: $price,
        stock: 10,
        valueIds: [$red->id, $xl->id]
    );

    expect($variant)->not()->toBeNull()
        ->and($variant->product->id)->toBe($product->id)
        ->and($variant->sku)->toBe('TS-RED-XL')
        ->and($variant->price)->toBe($price->amount)
        ->and($variant->stock)->toBe(10)
        ->and($variant->attributeValues)->toHaveCount(2);
});

it('does not create the variant if attaching values fails', function () {
    $product = Product::factory()->create();

    expect(fn () => (new CreateProductVariant)->execute(
        product: $product,
        sku: 'TS-RED-XL',
        price: new Money(15000, 'EGP'),
        stock: 10,
        valueIds: [999]
    ))->toThrow(QueryException::class);

    expect(ProductVariant::count())->toBe(0);
});

it('rejects two values of the same attribute', function () {
    $product = Product::factory()->create();
    $attribute = Attribute::factory()->create(['name' => 'Color']);
    $value1 = AttributeValue::factory()->create(['attribute_id' => $attribute->id, 'value' => 'Blue']);
    $value2 = AttributeValue::factory()->create(['attribute_id' => $attribute->id, 'value' => 'Red']);

    expect(fn () => (new CreateProductVariant)->execute(
        product: $product,
        sku: 'TS-RED-XL',
        price: new Money(15000, 'EGP'),
        stock: 10,
        valueIds: [$value1->id, $value2->id]
    ))->toThrow(DuplicateAttributeException::class);

    expect(ProductVariant::count())->toBe(0);
});
