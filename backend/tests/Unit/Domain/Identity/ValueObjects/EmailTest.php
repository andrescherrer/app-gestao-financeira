<?php

declare(strict_types=1);

use App\Domain\Identity\ValueObjects\Email;

test('should create email from valid string', function () {
    $email = Email::fromString('user@example.com');

    expect($email->value())->toBe('user@example.com')
        ->and((string) $email)->toBe('user@example.com');
});

test('should throw exception for invalid email', function () {
    expect(fn () => Email::fromString('invalid-email'))
        ->toThrow(InvalidArgumentException::class, 'Invalid email address');
});

test('should throw exception for email without domain', function () {
    expect(fn () => Email::fromString('user@'))
        ->toThrow(InvalidArgumentException::class);
});

test('should throw exception for email without @ symbol', function () {
    expect(fn () => Email::fromString('userexample.com'))
        ->toThrow(InvalidArgumentException::class);
});

test('should check equality of emails', function () {
    $email1 = Email::fromString('user@example.com');
    $email2 = Email::fromString('user@example.com');
    $email3 = Email::fromString('other@example.com');

    expect($email1->equals($email2))->toBeTrue()
        ->and($email1->equals($email3))->toBeFalse();
});

test('should convert email to string', function () {
    $email = Email::fromString('test@example.com');

    expect((string) $email)->toBe('test@example.com');
});

