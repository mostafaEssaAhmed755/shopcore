<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\VariantCreated;
use App\Mail\VariantCreatedMail;
use Illuminate\Support\Facades\Mail;

final class SendVariantCreatedNotification
{
    /**
     * Handle the event.
     */
    public function handle(VariantCreated $event): void
    {
        Mail::to(config('shop.admin_email'))->send(new VariantCreatedMail($event->variant));
    }
}
