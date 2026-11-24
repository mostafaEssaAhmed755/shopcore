<?php

declare(strict_types=1);

namespace App\Contracts;

interface CartTokenGenerator
{
    public function generate(): string;
}
