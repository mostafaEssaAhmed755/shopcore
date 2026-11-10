<?php

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Database\QueryException;

it('has many values', function () {
    $attribute = Attribute::factory()->create();

    AttributeValue::factory()->count(3)->create(['attribute_id' => $attribute->id]);

    expect($attribute->values)->toHaveCount(3);
});

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
