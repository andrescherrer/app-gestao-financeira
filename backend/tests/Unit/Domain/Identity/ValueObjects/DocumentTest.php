<?php

declare(strict_types=1);

use App\Domain\Identity\ValueObjects\Document;

test('should create CPF from valid string', function () {
    // CPF válido para testes: 00000000191
    $cpf = Document::cpf('00000000191');

    expect($cpf->isCpf())->toBeTrue()
        ->and($cpf->isCnpj())->toBeFalse()
        ->and($cpf->value())->toBe('00000000191');
});

test('should create CPF from formatted string', function () {
    $cpf = Document::fromString('000.000.001-91');

    expect($cpf->isCpf())->toBeTrue()
        ->and($cpf->value())->toBe('00000000191');
});

test('should create CNPJ from valid string', function () {
    // CNPJ válido para testes: 11222333000181
    $cnpj = Document::cnpj('11222333000181');

    expect($cnpj->isCnpj())->toBeTrue()
        ->and($cnpj->isCpf())->toBeFalse()
        ->and($cnpj->value())->toBe('11222333000181');
});

test('should create CNPJ from formatted string', function () {
    $cnpj = Document::fromString('11.222.333/0001-81');

    expect($cnpj->isCnpj())->toBeTrue()
        ->and($cnpj->value())->toBe('11222333000181');
});

test('should format CPF correctly', function () {
    $cpf = Document::cpf('00000000191');

    expect($cpf->formatted())->toBe('000.000.001-91');
});

test('should format CNPJ correctly', function () {
    $cnpj = Document::cnpj('11222333000181');

    expect($cnpj->formatted())->toBe('11.222.333/0001-81');
});

test('should throw exception for invalid CPF', function () {
    expect(fn () => Document::cpf('12345678901'))
        ->toThrow(InvalidArgumentException::class, 'Invalid CPF');
});

test('should throw exception for invalid CNPJ', function () {
    expect(fn () => Document::cnpj('12345678000190'))
        ->toThrow(InvalidArgumentException::class, 'Invalid CNPJ');
});

test('should throw exception for invalid document format', function () {
    expect(fn () => Document::fromString('123456789'))
        ->toThrow(InvalidArgumentException::class, 'Invalid document format');
});

test('should check equality of documents', function () {
    $doc1 = Document::cpf('00000000191');
    $doc2 = Document::cpf('00000000191');
    // Usar outro CPF válido para teste de diferença
    $doc3 = Document::cpf('12345678909');

    expect($doc1->equals($doc2))->toBeTrue()
        ->and($doc1->equals($doc3))->toBeFalse();
});

test('should reject CPF with all same digits', function () {
    expect(fn () => Document::cpf('11111111111'))
        ->toThrow(InvalidArgumentException::class);
});

test('should reject CNPJ with all same digits', function () {
    expect(fn () => Document::cnpj('11111111111111'))
        ->toThrow(InvalidArgumentException::class);
});

