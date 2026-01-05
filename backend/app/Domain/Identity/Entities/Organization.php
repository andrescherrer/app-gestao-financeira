<?php

declare(strict_types=1);

namespace App\Domain\Identity\Entities;

use App\Domain\Identity\ValueObjects\Document;
use App\Domain\Shared\ValueObjects\Uuid;
use DateTimeImmutable;

final class Organization
{
    private function __construct(
        private readonly Uuid $id,
        private string $name,
        private Document $document,
        private string $type, // PF ou PJ
        private string $plan,
        private readonly DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt,
        private ?DateTimeImmutable $deletedAt,
    ) {}

    public static function create(
        string $name,
        Document $document,
        string $type = 'PF',
        string $plan = 'free',
    ): self {
        return new self(
            id: Uuid::generate(),
            name: $name,
            document: $document,
            type: $type,
            plan: $plan,
            createdAt: new DateTimeImmutable,
            updatedAt: null,
            deletedAt: null,
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

    public function document(): Document
    {
        return $this->document;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function plan(): string
    {
        return $this->plan;
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

    public function updateName(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function updatePlan(string $plan): void
    {
        $this->plan = $plan;
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
