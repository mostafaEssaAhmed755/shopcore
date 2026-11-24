<?php

declare(strict_types=1);

namespace App\Support;

use App\Contracts\CartTokenGenerator;
use Illuminate\Support\Str;

final class UuidCartTokenGenerator implements CartTokenGenerator
{
    public function generate(): string
    {
        return Str::uuid()->toString();
    }
}
