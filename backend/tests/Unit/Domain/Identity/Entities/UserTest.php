<?php

declare(strict_types=1);

use App\Domain\Identity\Entities\User;
use App\Domain\Identity\ValueObjects\Email;
use App\Domain\Shared\ValueObjects\Uuid;

test('should create user', function () {
    $email = Email::fromString('user@example.com');
    $user = User::create(
        name: 'John Doe',
        email: $email,
    );

    expect($user->name())->toBe('John Doe')
        ->and($user->email())->toEqual($email)
        ->and($user->passwordHash())->toBeNull()
        ->and($user->organizationId())->toBeNull();
});

test('should create user with password and organization', function () {
    $email = Email::fromString('user@example.com');
    $organizationId = Uuid::generate();
    $passwordHash = 'hashed_password';

    $user = User::create(
        name: 'John Doe',
        email: $email,
        passwordHash: $passwordHash,
        organizationId: $organizationId,
    );

    expect($user->passwordHash())->toBe($passwordHash)
        ->and($user->organizationId())->toEqual($organizationId);
});

test('should update user name', function () {
    $email = Email::fromString('user@example.com');
    $user = User::create('John Doe', $email);

    $user->updateName('Jane Doe');

    expect($user->name())->toBe('Jane Doe')
        ->and($user->updatedAt())->not->toBeNull();
});

test('should update user email', function () {
    $email = Email::fromString('user@example.com');
    $user = User::create('John Doe', $email);

    $newEmail = Email::fromString('newemail@example.com');
    $user->updateEmail($newEmail);

    expect($user->email())->toEqual($newEmail)
        ->and($user->updatedAt())->not->toBeNull();
});

test('should update user password', function () {
    $email = Email::fromString('user@example.com');
    $user = User::create('John Doe', $email, 'old_hash');

    $user->updatePassword('new_hash');

    expect($user->passwordHash())->toBe('new_hash')
        ->and($user->updatedAt())->not->toBeNull();
});

test('should assign user to organization', function () {
    $email = Email::fromString('user@example.com');
    $user = User::create('John Doe', $email);

    $organizationId = Uuid::generate();
    $user->assignToOrganization($organizationId);

    expect($user->organizationId())->toEqual($organizationId)
        ->and($user->updatedAt())->not->toBeNull();
});

