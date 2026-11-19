<?php

use App\Mail\VariantCreatedMail;
use App\Models\ProductVariant;

it('shows the variant details', function () {
    $variant = ProductVariant::factory()->create(['sku' => 'TS-RED-XL', 'price' => 15000]);
    $mail = new VariantCreatedMail($variant);

    $mail->assertSeeInHtml('TS-RED-XL');
    $mail->assertSeeInHtml('150.00 EGP');
    $mail->assertSeeInHtml($variant->product->name);
});
