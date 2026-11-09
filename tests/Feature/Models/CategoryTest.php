<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\QueryException;

it('has many children', function () {
    $parent = Category::factory()->create();

    Category::factory()->count(2)->create(['parent_id' => $parent->id]);

    expect($parent->children)->toHaveCount(2);
});

it('belongs to a parent', function () {
    $parent = Category::factory()->create();

    $child = Category::factory()->create(['parent_id' => $parent->id]);


    expect($child->parent->id)->toBe($parent->id);
});

it('cannot delete a category that has children', function () {
    $parent = Category::factory()->create();

    Category::factory()->create(['parent_id' => $parent->id]);

    expect(fn() => $parent->delete())->toThrow(QueryException::class);

    expect(Category::find($parent->id))->not->toBeNull();
});

it('belongs to many products', function () {
    $products = Product::factory()->count(3)->create();
    $category = Category::factory()->create();

    $category->products()->attach($products->pluck('id'));

    expect($category->products)->toHaveCount(3);
});

it('stores timestamps on the category product pivot', function () {
    $product = Product::factory()->create();
    $category = Category::factory()->create();

    $product->categories()->attach($category->id);

    expect($product->categories->first()->pivot->created_at)->not->toBeNull();
});
