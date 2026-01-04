<?php

declare(strict_types=1);

namespace App\Domain\Identity\ValueObjects;

use InvalidArgumentException;

final class Document
{
    private function __construct(
        private readonly string $value,
        private readonly string $type
    ) {}

    public static function fromString(string $document): self
    {
        $cleaned = preg_replace('/\D/', '', $document);

        return match (strlen($cleaned)) {
            11 => self::cpf($cleaned),
            14 => self::cnpj($cleaned),
            default => throw new InvalidArgumentException('Invalid document format'),
        };
    }

    public static function cpf(string $value): self
    {
        $cleaned = preg_replace('/\D/', '', $value);

        if (! self::isValidCpf($cleaned)) {
            throw new InvalidArgumentException('Invalid CPF');
        }

        return new self($cleaned, 'CPF');
    }

    public static function cnpj(string $value): self
    {
        $cleaned = preg_replace('/\D/', '', $value);

        if (! self::isValidCnpj($cleaned)) {
            throw new InvalidArgumentException('Invalid CNPJ');
        }

        return new self($cleaned, 'CNPJ');
    }

    public function value(): string
    {
        return $this->value;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function isCpf(): bool
    {
        return $this->type === 'CPF';
    }

    public function isCnpj(): bool
    {
        return $this->type === 'CNPJ';
    }

    public function formatted(): string
    {
        if ($this->isCpf()) {
            return preg_replace(
                '/(\d{3})(\d{3})(\d{3})(\d{2})/',
                '$1.$2.$3-$4',
                $this->value
            );
        }

        return preg_replace(
            '/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/',
            '$1.$2.$3/$4-$5',
            $this->value
        );
    }

    private static function isValidCpf(string $cpf): bool
    {
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Validação dos dígitos verificadores
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += (int) $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ((int) $cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }

    private static function isValidCnpj(string $cnpj): bool
    {
        if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        // Validação dos dígitos verificadores
        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $cnpj[$i] * $weights1[$i];
        }
        $digit1 = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);

        if ((int) $cnpj[12] != $digit1) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += (int) $cnpj[$i] * $weights2[$i];
        }
        $digit2 = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);

        return (int) $cnpj[13] == $digit2;
    }

    public function equals(Document $other): bool
    {
        return $this->value === $other->value && $this->type === $other->type;
    }
}
