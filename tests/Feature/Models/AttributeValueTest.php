<?php

use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\QueryException;

it('belongs to an attribute', function () {
    $attribute = Attribute::factory()->create();

    $value = AttributeValue::factory()->create(['attribute_id' => $attribute->id]);

    expect($value->attribute->id)->toBe($attribute->id);
});

it('rejects duplicate values for the same attribute', function () {
    $attribute = Attribute::factory()->create();

    expect(fn() => AttributeValue::factory()
            ->count(2)
            ->create(['attribute_id' => $attribute->id, 'value' => 'red'])
        )->toThrow(QueryException::class);
});

it('allows the same value under different attributes', function () {
    $attribute1 = Attribute::factory()->create(['name' => 'Size']);
    $attribute2 = Attribute::factory()->create(['name' => 'Fit']);

    AttributeValue::factory()->create(['attribute_id' => $attribute1->id,'value' => 'L']);
    AttributeValue::factory()->create(['attribute_id' => $attribute2->id,'value' => 'L']);

    expect(AttributeValue::where('value', 'L')->count())->toBe(2);
});

it('has many variants', function () {
    $value = AttributeValue::factory()->create();

    $variants = ProductVariant::factory()->count(3)->create();

    $value->variants()->attach($variants->pluck('id'));

    expect($value->variants)->toHaveCount(3);
});

it('stores timestamps on the attribute value product variant pivot', function () {
    $attributeValue = AttributeValue::factory()->create();
    $variant = ProductVariant::factory()->create();

    $attributeValue->variants()->attach($variant->id);

    expect($attributeValue->variants->first()->pivot->created_at)->not->toBeNull();
});

it('cannot delete a value used by a variant', function () {
    $attributeValue = AttributeValue::factory()->create();
    $variant = ProductVariant::factory()->create();

    $attributeValue->variants()->attach($variant->id);

    expect(fn() => $attributeValue->delete())->toThrow(QueryException::class);
});
