<?php

use App\Events\VariantCreated;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

it('is not dispatched when the transaction rolls back', function () {
    Event::fake();

    $variant = ProductVariant::factory()->create();

    try {
        DB::transaction(function () use ($variant) {
            event(new VariantCreated($variant));
            throw new RuntimeException('boom');
        });
    } catch (RuntimeException $th) {
        Event::assertNotDispatched(VariantCreated::class);
    }

});
