<?php

use App\ValueObjects\Money;

it('adds two amounts of the same currency', function () {
    $a = new Money(1050, 'EGP');
    $b = new Money(250, 'EGP');

    $result = $a->add($b);

    expect($result->amount)->toBe(1300)
        ->and($result->currency)->toBe('EGP');
});

it('formats the amount with two decimals and currency', function () {
    $a = new Money(1050, 'EGP');

    expect($a->format())->toBe('10.50 EGP');
});

it('throws when adding different currencies', function () {
    $a = new Money(1050, 'EGP');
    $b = new Money(250, 'USD');

    expect(fn () => $a->add($b))
        ->toThrow(InvalidArgumentException::class);
});

it('does not change the original money when adding', function () {
    $a = new Money(1050, 'EGP');
    $b = new Money(250, 'EGP');

    $a->add($b);

    expect($a->amount)->toBe(1050);
});

it('multiplies the amount by a quantity', function () {
    $a = new Money(15000, 'EGP');

    expect($a->multiply(3))->toEqual(new Money(45000, 'EGP'));
});

it('returns the same amount when multiplied by 1', function () {
    $a = new Money(15000, 'EGP');

    expect($a->multiply(1))->toEqual(new Money(15000, 'EGP'));
});

it('throws when multiply quantity that is less than 1', function (int $quantity) {
    $a = new Money(15000, 'EGP');

    expect(fn () => $a->multiply($quantity))->toThrow(InvalidArgumentException::class);
})->with([0, -1, -100]);

it('creates money from a decimal string', function (string $amount, int $result) {
    $money = Money::fromDecimal($amount, 'USD');
    expect($money->amount)->toBe($result)
        ->and($money->currency)->toBe('USD');
})->with([
    ['150', 15000],
    ['150.5', 15050],
    ['150.05', 15005],
    ['19.99', 1999],
    ['0.01', 1],
]);

it('rejects an invalid decimal string', function (string $amount) {
    expect(fn () => Money::fromDecimal($amount, 'USD'))->toThrow(InvalidArgumentException::class);
})->with([
    '150.005',
    '-5',
    'abc',
    '',
    '1,000',
    'trailing newline' => "150\n",
]);
