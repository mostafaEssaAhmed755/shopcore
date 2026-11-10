<?php

use App\Models\Attribute;
use App\Models\AttributeValue;

it('has many values', function () {
    $attribute = Attribute::factory()->create();

    AttributeValue::factory()->count(3)->create(['attribute_id' => $attribute->id]);

    expect($attribute->values)->toHaveCount(3);
});


