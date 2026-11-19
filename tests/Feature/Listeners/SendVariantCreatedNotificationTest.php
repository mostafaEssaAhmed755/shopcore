<?php

use App\Events\VariantCreated;
use App\Listeners\SendVariantCreatedNotification;
use App\Mail\VariantCreatedMail;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

it('listens to VariantCreated', function () {
    Event::fake();
    Event::assertListening(VariantCreated::class, SendVariantCreatedNotification::class);
});

it('emails the admin about the new variant', function () {
    Mail::fake();

    config(['shop.admin_email' => 'boss@shop.test']);

    $variant = ProductVariant::factory()->create();

    (new SendVariantCreatedNotification)->handle(new VariantCreated($variant));

    Mail::assertSent(VariantCreatedMail::class, function ($mail) use ($variant) {
        return $mail->hasTo('boss@shop.test') && $mail->variant->is($variant);
    });
});
