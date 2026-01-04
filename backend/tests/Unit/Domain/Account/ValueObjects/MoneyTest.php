<?php

declare(strict_types=1);

use App\Domain\Account\ValueObjects\Money;

test('should create money from cents', function () {
    $money = Money::fromCents(1000);

    expect($money->cents())->toBe(1000)
        ->and($money->toDecimal())->toBe(10.0)
        ->and($money->currency())->toBe('BRL');
});

test('should create money from decimal', function () {
    $money = Money::fromDecimal(10.50);

    expect($money->cents())->toBe(1050)
        ->and($money->toDecimal())->toBe(10.5);
});

test('should create zero money', function () {
    $money = Money::zero();

    expect($money->cents())->toBe(0)
        ->and($money->isZero())->toBeTrue()
        ->and($money->isPositive())->toBeFalse()
        ->and($money->isNegative())->toBeFalse();
});

test('should add two money values', function () {
    $money1 = Money::fromCents(1000);
    $money2 = Money::fromCents(500);

    $result = $money1->add($money2);

    expect($result->cents())->toBe(1500);
});

test('should subtract two money values', function () {
    $money1 = Money::fromCents(1000);
    $money2 = Money::fromCents(300);

    $result = $money1->subtract($money2);

    expect($result->cents())->toBe(700);
});

test('should multiply money by factor', function () {
    $money = Money::fromCents(1000);

    $result = $money->multiply(1.5);

    expect($result->cents())->toBe(1500);
});

test('should check if money is negative', function () {
    $money = Money::fromCents(-100);

    expect($money->isNegative())->toBeTrue()
        ->and($money->isPositive())->toBeFalse()
        ->and($money->isZero())->toBeFalse();
});

test('should check if money is positive', function () {
    $money = Money::fromCents(100);

    expect($money->isPositive())->toBeTrue()
        ->and($money->isNegative())->toBeFalse()
        ->and($money->isZero())->toBeFalse();
});

test('should compare money values', function () {
    $money1 = Money::fromCents(1000);
    $money2 = Money::fromCents(500);
    $money3 = Money::fromCents(1000);

    expect($money1->greaterThan($money2))->toBeTrue()
        ->and($money2->lessThan($money1))->toBeTrue()
        ->and($money1->equals($money3))->toBeTrue();
});

test('should format money correctly', function () {
    $money = Money::fromCents(123456);

    expect($money->format())->toBe('1.234,56');
});

test('should throw exception when operating with different currencies', function () {
    $money1 = Money::fromCents(1000, 'BRL');
    $money2 = Money::fromCents(500, 'USD');

    expect(fn () => $money1->add($money2))
        ->toThrow(InvalidArgumentException::class, 'Cannot operate on different currencies');
});

test('should create money with custom currency', function () {
    $money = Money::fromCents(1000, 'USD');

    expect($money->currency())->toBe('USD');
});

