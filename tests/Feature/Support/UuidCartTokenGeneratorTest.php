<?php

use App\Contracts\CartTokenGenerator;
use App\Support\UuidCartTokenGenerator;
use Illuminate\Support\Str;

it('resolves CartTokenGenerator to the UUID implementation', function () {
    expect(app(CartTokenGenerator::class))->toBeInstanceOf(UuidCartTokenGenerator::class);
});

it('generates a valid UUID', function () {
    expect(Str::isUuid(app(CartTokenGenerator::class)->generate()))->toBeTrue();
});

it('generates a different token each time', function () {
    $uuid1 = app(CartTokenGenerator::class)->generate();
    $uuid2 = app(CartTokenGenerator::class)->generate();
    expect($uuid1)->not()->toBe($uuid2);
});
