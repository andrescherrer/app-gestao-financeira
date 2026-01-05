<?php

declare(strict_types=1);

namespace App\Domain\Identity\Entities;

use App\Domain\Identity\ValueObjects\Email;
use App\Domain\Shared\ValueObjects\Uuid;
use DateTimeImmutable;

final class User
{
    private function __construct(
        private readonly Uuid $id,
        private string $name,
        private Email $email,
        private ?string $passwordHash,
        private ?Uuid $organizationId,
        private readonly DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        string $name,
        Email $email,
        ?string $passwordHash = null,
        ?Uuid $organizationId = null,
    ): self {
        return new self(
            id: Uuid::generate(),
            name: $name,
            email: $email,
            passwordHash: $passwordHash,
            organizationId: $organizationId,
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

    public function email(): Email
    {
        return $this->email;
    }

    public function passwordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function organizationId(): ?Uuid
    {
        return $this->organizationId;
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

    public function updateEmail(Email $email): void
    {
        $this->email = $email;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function updatePassword(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function assignToOrganization(Uuid $organizationId): void
    {
        $this->organizationId = $organizationId;
        $this->updatedAt = new DateTimeImmutable;
    }
}
