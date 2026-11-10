<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;

it('belongs to many categories', function () {
    $product = Product::factory()->create();
    
    $c1 = Category::factory()->create();
    $c2 = Category::factory()->create();

    $product->categories()->attach([$c1->id, $c2->id]);

    expect($product->categories)->toHaveCount(2);
});

it('has many variants', function () {
    $product = Product::factory()->create();

    ProductVariant::factory()->count(3)->create(['product_id' => $product->id]);

    expect($product->variants)->toHaveCount(3);
});
