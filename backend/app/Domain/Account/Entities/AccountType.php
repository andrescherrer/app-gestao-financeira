<?php

declare(strict_types=1);

namespace App\Domain\Account\Entities;

use App\Domain\Shared\ValueObjects\Uuid;
use DateTimeImmutable;

final class AccountType
{
    private function __construct(
        private readonly Uuid $id,
        private string $name,
        private string $slug,
        private bool $hasCreditLimit,
        private bool $supportsBorrower,
        private readonly DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        string $name,
        string $slug,
        bool $hasCreditLimit = false,
        bool $supportsBorrower = false,
    ): self {
        return new self(
            id: Uuid::generate(),
            name: $name,
            slug: $slug,
            hasCreditLimit: $hasCreditLimit,
            supportsBorrower: $supportsBorrower,
            createdAt: new DateTimeImmutable,
            updatedAt: null,
        );
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function hasCreditLimit(): bool
    {
        return $this->hasCreditLimit;
    }

    public function supportsBorrower(): bool
    {
        return $this->supportsBorrower;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new DateTimeImmutable;
    }
}
