<?php

declare(strict_types=1);

use App\Domain\Identity\Entities\Organization;
use App\Domain\Identity\ValueObjects\Document;

test('should create organization', function () {
    $document = Document::cpf('00000000191');
    $organization = Organization::create(
        name: 'Minha Empresa',
        document: $document,
    );

    expect($organization->name())->toBe('Minha Empresa')
        ->and($organization->document())->toEqual($document)
        ->and($organization->type())->toBe('PF')
        ->and($organization->plan())->toBe('free')
        ->and($organization->isDeleted())->toBeFalse();
});

test('should create organization with custom type and plan', function () {
    $document = Document::cnpj('11222333000181');
    $organization = Organization::create(
        name: 'Empresa LTDA',
        document: $document,
        type: 'PJ',
        plan: 'premium',
    );

    expect($organization->type())->toBe('PJ')
        ->and($organization->plan())->toBe('premium');
});

test('should update organization name', function () {
    $document = Document::cpf('00000000191');
    $organization = Organization::create('Old Name', $document);

    $organization->updateName('New Name');

    expect($organization->name())->toBe('New Name')
        ->and($organization->updatedAt())->not->toBeNull();
});

test('should update organization plan', function () {
    $document = Document::cpf('00000000191');
    $organization = Organization::create('Company', $document, 'PF', 'free');

    $organization->updatePlan('premium');

    expect($organization->plan())->toBe('premium')
        ->and($organization->updatedAt())->not->toBeNull();
});

test('should delete organization', function () {
    $document = Document::cpf('00000000191');
    $organization = Organization::create('Company', $document);

    $organization->delete();

    expect($organization->isDeleted())->toBeTrue()
        ->and($organization->deletedAt())->not->toBeNull();
});

test('should restore deleted organization', function () {
    $document = Document::cpf('00000000191');
    $organization = Organization::create('Company', $document);
    $organization->delete();

    $organization->restore();

    expect($organization->isDeleted())->toBeFalse()
        ->and($organization->deletedAt())->toBeNull();
});

