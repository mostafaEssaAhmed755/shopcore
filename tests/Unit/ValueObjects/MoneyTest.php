<?php

use App\ValueObjects\Money;

it('adds tow amounts of the same currency', function() {
    $a = new Money(1050, 'EGP');
    $b = new Money(250, 'EGP');

    $result = $a->add($b);

    expect($result->amount)->toBe(1300)
        ->and($result->currency)->toBe('EGP');
});

it('formats the amount with two decimals and currency', function() {
    $a = new Money(1050, 'EGP');

    expect($a->format())->toBe('10.50 EGP');
});

it('throws when adding different currencies', function() {
    $a = new Money(1050, 'EGP');
    $b = new Money(250, 'USD');

    expect(fn() => $a->add($b))
        ->toThrow(InvalidArgumentException::class);
});

it('does not change the original money when adding', function() {
    $a = new Money(1050, 'EGP');
    $b = new Money(250, 'EGP');

    $a->add($b);

    expect($a->amount)->toBe(1050);
});