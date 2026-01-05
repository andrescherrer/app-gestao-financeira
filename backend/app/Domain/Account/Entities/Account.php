<?php

declare(strict_types=1);

namespace App\Domain\Account\Entities;

use App\Domain\Account\ValueObjects\Money;
use App\Domain\Shared\ValueObjects\Uuid;
use DateTimeImmutable;

final class Account
{
    private function __construct(
        private readonly Uuid $id,
        private readonly Uuid $organizationId,
        private readonly Uuid $accountTypeId,
        private string $name,
        private Money $initialBalance,
        private bool $isActive,
        private ?Money $creditLimit,
        private ?int $closingDay,
        private ?int $dueDay,
        // Campos específicos para empréstimos
        private ?Uuid $borrowerId,
        private ?float $interestRate,
        private ?DateTimeImmutable $loanDueDate,
        private readonly DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt,
        private ?DateTimeImmutable $deletedAt,
    ) {}

    public static function create(
        Uuid $organizationId,
        Uuid $accountTypeId,
        string $name,
        Money $initialBalance,
        ?Money $creditLimit = null,
        ?int $closingDay = null,
        ?int $dueDay = null,
    ): self {
        return new self(
            id: Uuid::generate(),
            organizationId: $organizationId,
            accountTypeId: $accountTypeId,
            name: $name,
            initialBalance: $initialBalance,
            isActive: true,
            creditLimit: $creditLimit,
            closingDay: $closingDay,
            dueDay: $dueDay,
            borrowerId: null,
            interestRate: null,
            loanDueDate: null,
            createdAt: new DateTimeImmutable,
            updatedAt: null,
            deletedAt: null,
        );
    }

    public static function createLoan(
        Uuid $organizationId,
        Uuid $accountTypeId,
        string $name,
        Money $initialBalance,
        Uuid $borrowerId,
        float $interestRate,
        DateTimeImmutable $loanDueDate,
    ): self {
        return new self(
            id: Uuid::generate(),
            organizationId: $organizationId,
            accountTypeId: $accountTypeId,
            name: $name,
            initialBalance: $initialBalance,
            isActive: true,
            creditLimit: null,
            closingDay: null,
            dueDay: null,
            borrowerId: $borrowerId,
            interestRate: $interestRate,
            loanDueDate: $loanDueDate,
            createdAt: new DateTimeImmutable,
            updatedAt: null,
            deletedAt: null,
        );
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function organizationId(): Uuid
    {
        return $this->organizationId;
    }

    public function accountTypeId(): Uuid
    {
        return $this->accountTypeId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function initialBalance(): Money
    {
        return $this->initialBalance;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function creditLimit(): ?Money
    {
        return $this->creditLimit;
    }

    public function closingDay(): ?int
    {
        return $this->closingDay;
    }

    public function dueDay(): ?int
    {
        return $this->dueDay;
    }

    public function borrowerId(): ?Uuid
    {
        return $this->borrowerId;
    }

    public function interestRate(): ?float
    {
        return $this->interestRate;
    }

    public function loanDueDate(): ?DateTimeImmutable
    {
        return $this->loanDueDate;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function deletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function isLoan(): bool
    {
        return $this->borrowerId !== null;
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function updateInitialBalance(Money $initialBalance): void
    {
        $this->initialBalance = $initialBalance;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function activate(): void
    {
        $this->isActive = true;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function deactivate(): void
    {
        $this->isActive = false;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function updateCreditLimit(?Money $creditLimit): void
    {
        $this->creditLimit = $creditLimit;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function updateClosingDay(?int $closingDay): void
    {
        $this->closingDay = $closingDay;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function updateDueDay(?int $dueDay): void
    {
        $this->dueDay = $dueDay;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function delete(): void
    {
        $this->deletedAt = new DateTimeImmutable;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function restore(): void
    {
        $this->deletedAt = null;
        $this->updatedAt = new DateTimeImmutable;
    }
}
