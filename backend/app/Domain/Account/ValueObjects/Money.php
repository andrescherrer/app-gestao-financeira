<?php

declare(strict_types=1);

namespace App\Domain\Account\ValueObjects;

use InvalidArgumentException;

final class Money
{
    private function __construct(
        private readonly int $cents,
        private readonly string $currency = 'BRL'
    ) {}

    public static function fromCents(int $cents, string $currency = 'BRL'): self
    {
        return new self($cents, $currency);
    }

    public static function fromDecimal(float|string $amount, string $currency = 'BRL'): self
    {
        $cents = (int) round((float) $amount * 100);

        return new self($cents, $currency);
    }

    public static function zero(string $currency = 'BRL'): self
    {
        return new self(0, $currency);
    }

    public function add(Money $other): self
    {
        $this->ensureSameCurrency($other);

        return new self($this->cents + $other->cents, $this->currency);
    }

    public function subtract(Money $other): self
    {
        $this->ensureSameCurrency($other);

        return new self($this->cents - $other->cents, $this->currency);
    }

    public function multiply(float $factor): self
    {
        return new self((int) round($this->cents * $factor), $this->currency);
    }

    public function isNegative(): bool
    {
        return $this->cents < 0;
    }

    public function isPositive(): bool
    {
        return $this->cents > 0;
    }

    public function isZero(): bool
    {
        return $this->cents === 0;
    }

    public function greaterThan(Money $other): bool
    {
        $this->ensureSameCurrency($other);

        return $this->cents > $other->cents;
    }

    public function lessThan(Money $other): bool
    {
        $this->ensureSameCurrency($other);

        return $this->cents < $other->cents;
    }

    public function cents(): int
    {
        return $this->cents;
    }

    public function toDecimal(): float
    {
        return $this->cents / 100;
    }

    public function format(): string
    {
        return number_format($this->toDecimal(), 2, ',', '.');
    }

    public function currency(): string
    {
        return $this->currency;
    }

    private function ensureSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->currency()) {
            throw new InvalidArgumentException(
                "Cannot operate on different currencies: {$this->currency} vs {$other->currency()}"
            );
        }
    }

    public function equals(Money $other): bool
    {
        return $this->cents === $other->cents
            && $this->currency === $other->currency();
    }
}
