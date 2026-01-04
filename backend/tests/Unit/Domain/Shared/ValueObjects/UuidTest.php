<?php

declare(strict_types=1);

use App\Domain\Shared\ValueObjects\Uuid;

test('should create UUID from valid string', function () {
    $uuidString = '550e8400-e29b-41d4-a716-446655440000';
    $uuid = Uuid::fromString($uuidString);

    expect($uuid->value())->toBe($uuidString)
        ->and($uuid->toString())->toBe($uuidString)
        ->and((string) $uuid)->toBe($uuidString);
});

test('should generate new UUID', function () {
    $uuid = Uuid::generate();

    expect($uuid->value())->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i');
});

test('should throw exception for invalid UUID', function () {
    expect(fn () => Uuid::fromString('invalid-uuid'))
        ->toThrow(InvalidArgumentException::class, 'Invalid UUID');
});

test('should check equality of UUIDs', function () {
    $uuidString = '550e8400-e29b-41d4-a716-446655440000';
    $uuid1 = Uuid::fromString($uuidString);
    $uuid2 = Uuid::fromString($uuidString);
    $uuid3 = Uuid::generate();

    expect($uuid1->equals($uuid2))->toBeTrue()
        ->and($uuid1->equals($uuid3))->toBeFalse();
});

test('should convert UUID to string', function () {
    $uuidString = '550e8400-e29b-41d4-a716-446655440000';
    $uuid = Uuid::fromString($uuidString);

    expect((string) $uuid)->toBe($uuidString);
});

test('should convert UUID to UuidInterface', function () {
    $uuidString = '550e8400-e29b-41d4-a716-446655440000';
    $uuid = Uuid::fromString($uuidString);

    $uuidInterface = $uuid->toUuidInterface();

    expect($uuidInterface->toString())->toBe($uuidString);
});

