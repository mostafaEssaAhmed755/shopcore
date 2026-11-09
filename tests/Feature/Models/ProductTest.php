<?php

use App\Models\Category;
use App\Models\Product;

it('belongs to many categories', function () {
    $product = Product::factory()->create();
    
    $c1 = Category::factory()->create();
    $c2 = Category::factory()->create();

    $product->categories()->attach([$c1->id, $c2->id]);

    expect($product->categories)->toHaveCount(2);
});
