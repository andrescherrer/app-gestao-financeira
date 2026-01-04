# Planejamento: Sistema de Gestão Financeira

## Sumário

- [1. Visão Geral da Arquitetura](#1-visão-geral-da-arquitetura)
- [2. Modelagem de Domínios (DDD)](#2-modelagem-de-domínios-ddd)
- [3. Modelagem de Dados](#3-modelagem-de-dados)
- [4. Estrutura do Projeto](#4-estrutura-do-projeto-clean-architecture--ddd)
- [5. Exemplos de Código](#5-exemplos-de-código)
- [6. Configuração Docker](#6-configuração-docker)
- [7. CI/CD Pipeline](#7-cicd-pipeline-github-actions)
- [8. Estratégia de Feature Flags](#8-estratégia-de-feature-flags)
- [9. API Endpoints](#9-api-endpoints)
- [9.1 Importação de Transações via OFX](#91-importação-de-transações-via-ofx)
- [10. Otimizações de Alta Performance](#10-otimizações-de-alta-performance)
- [11. Segurança e Autenticação](#11-segurança-e-autenticação)
- [12. Testes e Qualidade](#12-testes-e-qualidade)
- [13. Tratamento de Erros e Resiliência](#13-tratamento-de-erros-e-resiliência)
- [14. Observabilidade e Monitoramento](#14-observabilidade-e-monitoramento)
- [15. Backup e Disaster Recovery](#15-backup-e-disaster-recovery)
- [16. Documentação da API](#16-documentação-da-api)
- [17. Multi-tenancy e Isolamento](#17-multi-tenancy-e-isolamento)
- [18. Compliance e LGPD](#18-compliance-e-lgpd)
- [19. Deploy e Infraestrutura Avançada](#19-deploy-e-infraestrutura-avançada)
- [20. Sugestões Adicionais](#20-sugestões-adicionais)
- [21. Próximos Passos](#21-próximos-passos)

---

## Requisitos do Sistema

### Funcionais

- Categorização de transações (alimentação, transporte, etc.)
- Transações do tipo receita ou despesa
- Entrada rápida e intuitiva de dados
- Suporte a pessoa física (PF) e jurídica (PJ)
- Saldo global independente das contas
- Múltiplos tipos de conta: Cartão de Crédito, Conta Corrente, Investimento, Empréstimo
- Importação de transações via arquivo OFX
- Controle de objetivos/metas para planejamento de compras ou eventos

### Técnicos

- Laravel (versão mais recente)
- Frontend desacoplado do backend
- Docker
- DDD (Domain-Driven Design)
- Clean Architecture
- CI/CD
- Feature Flags

---

## 1. Visão Geral da Arquitetura

```
┌─────────────────────────────────────────────────────────────────────┐
│                         FRONTEND (SPA)                              │
│                    React/Vue + TypeScript                           │
└─────────────────────────────────────────────────────────────────────┘
                                  │
                                  ▼
┌─────────────────────────────────────────────────────────────────────┐
│                         API GATEWAY                                 │
│                    Laravel + Sanctum/Passport                       │
└─────────────────────────────────────────────────────────────────────┘
                                  │
        ┌─────────────────────────┼─────────────────────────┐
        ▼                         ▼                         ▼
┌───────────────┐       ┌───────────────┐       ┌───────────────┐
│    Domain     │       │    Domain     │       │    Domain     │
│   Identity    │       │   Financial   │       │   Planning    │
│  (User/Org)   │       │ (Transactions)│       │  (Goals)      │
└───────────────┘       └───────────────┘       └───────────────┘
        │                         │                         │
        └─────────────────────────┼─────────────────────────┘
                                  ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    INFRASTRUCTURE LAYER                             │
│         PostgreSQL │ Redis │ Queue │ Feature Flags                  │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 2. Modelagem de Domínios (DDD)

### 2.1 Bounded Contexts Identificados

| Context | Responsabilidade | Entidades Principais |
|---------|------------------|----------------------|
| **Identity** | Gestão de usuários, organizações (PF/PJ), autenticação | User, Organization, Tenant |
| **Account** | Contas financeiras e saldos | Account, AccountType, Balance |
| **Transaction** | Movimentações financeiras | Transaction, Category, Recurrence |
| **Planning** | Objetivos e metas | Goal, GoalContribution, Budget |
| **Reporting** | Relatórios e dashboards | (Read Models) |

### 2.2 Mapa de Contexto

```
┌─────────────┐         ┌─────────────┐
│  Identity   │────────▶│   Account   │
│  (upstream) │         │(downstream) │
└─────────────┘         └──────┬──────┘
                               │
                               ▼
                        ┌─────────────┐
                        │ Transaction │
                        └──────┬──────┘
                               │
              ┌────────────────┼────────────────┐
              ▼                                 ▼
       ┌─────────────┐                  ┌─────────────┐
       │  Planning   │                  │  Reporting  │
       └─────────────┘                  └─────────────┘
```

---

## 3. Modelagem de Dados

### 3.1 Diagrama ER

```
┌──────────────────┐       ┌──────────────────┐
│      users       │       │  organizations   │
├──────────────────┤       ├──────────────────┤
│ id               │       │ id               │
│ name             │       │ name             │
│ email            │       │ document (CPF/   │
│ password         │       │   CNPJ)          │
│ organization_id  │──────▶│ type (PF/PJ)     │
│ created_at       │       │ created_at       │
└──────────────────┘       └──────────────────┘
                                    │
        ┌───────────────────────────┘
        ▼
┌──────────────────┐       ┌──────────────────┐
│    accounts      │       │  account_types   │
├──────────────────┤       ├──────────────────┤
│ id               │       │ id               │
│ organization_id  │       │ name             │
│ account_type_id  │──────▶│ slug (checking,  │
│ name             │       │   credit_card,   │
│ initial_balance  │       │   investment,    │
│ is_active        │       │   loan)          │
│ credit_limit     │       │ has_credit_limit │
│ closing_day      │       │ supports_borrower│
│ due_day          │       └──────────────────┘
│ borrower_id      │
│ interest_rate    │
│ loan_due_date    │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐       ┌──────────────────┐
│  transactions    │       │   categories     │
├──────────────────┤       ├──────────────────┤
│ id               │       │ id               │
│ account_id       │       │ organization_id  │
│ category_id      │──────▶│ parent_id        │
│ type (income/    │       │ name             │
│   expense)       │       │ icon             │
│ amount           │       │ color            │
│ description      │       │ type (income/    │
│ transaction_date │       │   expense)       │
│ competence_date  │       │ is_system        │
│ is_confirmed     │       └──────────────────┘
│ recurrence_id    │
│ tags             │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐       ┌──────────────────┐
│ global_balance   │       │     goals        │
├──────────────────┤       ├──────────────────┤
│ id               │       │ id               │
│ organization_id  │       │ organization_id  │
│ calculated_at    │       │ name             │
│ total_balance    │       │ target_amount    │
│ total_income     │       │ current_amount   │
│ total_expense    │       │ target_date      │
│ period_start     │       │ icon             │
│ period_end       │       │ color            │
└──────────────────┘       │ status           │
                           └──────────────────┘
```

### 3.2 Migrations Principais

#### organizations

```php
Schema::create('organizations', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name');
    $table->string('document', 20)->unique(); // CPF ou CNPJ
    $table->enum('type', ['PF', 'PJ']);
    $table->string('plan')->default('free');
    $table->timestamps();
    $table->softDeletes();
});
```

#### account_types

```php
Schema::create('account_types', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name');
    $table->string('slug')->unique(); // checking, credit_card, investment, loan
    $table->boolean('has_credit_limit')->default(false);
    $table->boolean('supports_borrower')->default(false); // Para empréstimos
    $table->timestamps();
});
```

#### accounts

```php
Schema::create('accounts', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
    $table->foreignUuid('account_type_id')->constrained();
    $table->string('name');
    $table->bigInteger('initial_balance')->default(0); // em centavos
    $table->boolean('is_active')->default(true);
    $table->bigInteger('credit_limit')->nullable(); // para cartão de crédito
    $table->unsignedTinyInteger('closing_day')->nullable();
    $table->unsignedTinyInteger('due_day')->nullable();
    
    // Campos específicos para empréstimos
    $table->foreignUuid('borrower_id')->nullable()->constrained('users')->onDelete('set null');
    $table->decimal('interest_rate', 5, 2)->nullable(); // Taxa de juros (ex: 2.50%)
    $table->date('loan_due_date')->nullable(); // Data de vencimento do empréstimo
    
    $table->timestamps();
    $table->softDeletes();
});
```

#### transactions

```php
Schema::create('transactions', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('account_id')->constrained()->cascadeOnDelete();
    $table->foreignUuid('category_id')->constrained();
    $table->enum('type', ['income', 'expense']);
    $table->bigInteger('amount'); // em centavos
    $table->string('description');
    $table->date('transaction_date');
    $table->date('competence_date');
    $table->boolean('is_confirmed')->default(false);
    $table->foreignUuid('recurrence_id')->nullable()->constrained();
    $table->json('tags')->nullable();
    $table->timestamps();
    $table->softDeletes();
    
    $table->index(['account_id', 'transaction_date']);
    $table->index(['category_id', 'type']);
});
```

#### goals

```php
Schema::create('goals', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('organization_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->text('description')->nullable();
    $table->bigInteger('target_amount'); // em centavos
    $table->bigInteger('current_amount')->default(0);
    $table->date('target_date')->nullable();
    $table->string('icon')->nullable();
    $table->string('color')->nullable();
    $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
    $table->timestamps();
    $table->softDeletes();
});
```

---

## 4. Estrutura do Projeto (Clean Architecture + DDD)

```
finance-api/
├── app/
│   ├── Domain/                          # Camada de Domínio (Core)
│   │   ├── Identity/
│   │   │   ├── Entities/
│   │   │   │   ├── User.php
│   │   │   │   └── Organization.php
│   │   │   ├── ValueObjects/
│   │   │   │   ├── Document.php         # CPF/CNPJ
│   │   │   │   ├── Email.php
│   │   │   │   └── OrganizationType.php # PF/PJ
│   │   │   ├── Repositories/
│   │   │   │   └── UserRepositoryInterface.php
│   │   │   └── Services/
│   │   │       └── OrganizationService.php
│   │   │
│   │   ├── Account/
│   │   │   ├── Entities/
│   │   │   │   ├── Account.php
│   │   │   │   └── AccountType.php
│   │   │   ├── ValueObjects/
│   │   │   │   ├── Money.php
│   │   │   │   ├── CreditCardInfo.php
│   │   │   │   └── AccountTypeEnum.php
│   │   │   ├── Repositories/
│   │   │   │   └── AccountRepositoryInterface.php
│   │   │   ├── Events/
│   │   │   │   └── AccountBalanceChanged.php
│   │   │   └── Services/
│   │   │       └── BalanceCalculator.php
│   │   │
│   │   ├── Transaction/
│   │   │   ├── Entities/
│   │   │   │   ├── Transaction.php
│   │   │   │   ├── Category.php
│   │   │   │   └── Recurrence.php
│   │   │   ├── ValueObjects/
│   │   │   │   ├── TransactionType.php  # income/expense
│   │   │   │   ├── RecurrencePattern.php
│   │   │   │   └── Tags.php
│   │   │   ├── Repositories/
│   │   │   │   ├── TransactionRepositoryInterface.php
│   │   │   │   └── CategoryRepositoryInterface.php
│   │   │   ├── Events/
│   │   │   │   ├── TransactionCreated.php
│   │   │   │   └── TransactionConfirmed.php
│   │   │   └── Services/
│   │   │       └── TransactionService.php
│   │   │
│   │   ├── Planning/
│   │   │   ├── Entities/
│   │   │   │   ├── Goal.php
│   │   │   │   └── Budget.php
│   │   │   ├── ValueObjects/
│   │   │   │   └── GoalStatus.php
│   │   │   └── Services/
│   │   │       └── GoalProgressCalculator.php
│   │   │
│   │   └── Shared/
│   │       ├── ValueObjects/
│   │       │   ├── Uuid.php
│   │       │   └── DateRange.php
│   │       └── Events/
│   │           └── DomainEvent.php
│   │
│   ├── Application/                     # Casos de Uso
│   │   ├── Identity/
│   │   │   ├── Commands/
│   │   │   │   ├── CreateUser/
│   │   │   │   │   ├── CreateUserCommand.php
│   │   │   │   │   └── CreateUserHandler.php
│   │   │   │   └── CreateOrganization/
│   │   │   └── Queries/
│   │   │       └── GetUserProfile/
│   │   │
│   │   ├── Account/
│   │   │   ├── Commands/
│   │   │   │   ├── CreateAccount/
│   │   │   │   └── UpdateBalance/
│   │   │   └── Queries/
│   │   │       ├── GetAccountBalance/
│   │   │       └── GetGlobalBalance/
│   │   │
│   │   ├── Transaction/
│   │   │   ├── Commands/
│   │   │   │   ├── CreateTransaction/
│   │   │   │   │   ├── CreateTransactionCommand.php
│   │   │   │   │   └── CreateTransactionHandler.php
│   │   │   │   ├── ConfirmTransaction/
│   │   │   │   └── CreateQuickTransaction/  # Entrada rápida
│   │   │   └── Queries/
│   │   │       ├── ListTransactions/
│   │   │       └── GetTransactionsByCategory/
│   │   │
│   │   ├── Planning/
│   │   │   ├── Commands/
│   │   │   │   ├── CreateGoal/
│   │   │   │   └── ContributeToGoal/
│   │   │   └── Queries/
│   │   │       └── GetGoalProgress/
│   │   │
│   │   └── Shared/
│   │       ├── Bus/
│   │       │   ├── CommandBusInterface.php
│   │       │   └── QueryBusInterface.php
│   │       └── DTOs/
│   │
│   ├── Infrastructure/                  # Implementações
│   │   ├── Persistence/
│   │   │   ├── Eloquent/
│   │   │   │   ├── Models/
│   │   │   │   │   ├── UserModel.php
│   │   │   │   │   ├── AccountModel.php
│   │   │   │   │   └── TransactionModel.php
│   │   │   │   └── Repositories/
│   │   │   │       ├── EloquentUserRepository.php
│   │   │   │       └── EloquentTransactionRepository.php
│   │   │   └── Mappers/
│   │   │       └── TransactionMapper.php
│   │   │
│   │   ├── Bus/
│   │   │   ├── IlluminateCommandBus.php
│   │   │   └── IlluminateQueryBus.php
│   │   │
│   │   ├── Cache/
│   │   │   └── RedisGlobalBalanceCache.php
│   │   │
│   │   ├── FeatureFlags/
│   │   │   ├── FeatureFlagServiceInterface.php
│   │   │   ├── LaravelPennantAdapter.php
│   │   │   └── Features/
│   │   │       ├── NewDashboard.php
│   │   │       └── QuickTransactionV2.php
│   │   │
│   │   └── ExternalServices/
│   │       └── NotificationService.php
│   │
│   └── Interfaces/                      # Adaptadores de Entrada
│       ├── Http/
│       │   ├── Controllers/
│       │   │   ├── Api/
│       │   │   │   ├── V1/
│       │   │   │   │   ├── AuthController.php
│       │   │   │   │   ├── AccountController.php
│       │   │   │   │   ├── TransactionController.php
│       │   │   │   │   ├── CategoryController.php
│       │   │   │   │   └── GoalController.php
│       │   │   │   └── V2/
│       │   │   └── Webhook/
│       │   ├── Requests/
│       │   │   ├── CreateTransactionRequest.php
│       │   │   └── QuickTransactionRequest.php
│       │   ├── Resources/
│       │   │   ├── TransactionResource.php
│       │   │   └── AccountResource.php
│       │   └── Middleware/
│       │       ├── TenantMiddleware.php
│       │       └── FeatureFlagMiddleware.php
│       │
│       └── Console/
│           └── Commands/
│               ├── CalculateGlobalBalance.php
│               └── ProcessRecurringTransactions.php
│
├── database/
│   ├── migrations/
│   └── seeders/
│       └── DefaultCategoriesSeeder.php
│
├── tests/
│   ├── Unit/
│   │   └── Domain/
│   ├── Integration/
│   └── Feature/
│
├── docker/
│   ├── Dockerfile
│   ├── nginx/
│   └── php/
│
└── docker-compose.yml
```

---

## 5. Exemplos de Código

### 5.1 Value Object: Money

```php
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
            && $this->currency === $other->currency;
    }
}
```

### 5.2 Value Object: Document (CPF/CNPJ)

```php
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
        
        if (!self::isValidCpf($cleaned)) {
            throw new InvalidArgumentException('Invalid CPF');
        }
        
        return new self($cleaned, 'CPF');
    }

    public static function cnpj(string $value): self
    {
        $cleaned = preg_replace('/\D/', '', $value);
        
        if (!self::isValidCnpj($cleaned)) {
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
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
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
            $sum += $cnpj[$i] * $weights1[$i];
        }
        $digit1 = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);
        
        if ($cnpj[12] != $digit1) {
            return false;
        }
        
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $weights2[$i];
        }
        $digit2 = $sum % 11 < 2 ? 0 : 11 - ($sum % 11);
        
        return $cnpj[13] == $digit2;
    }

    public function equals(Document $other): bool
    {
        return $this->value === $other->value && $this->type === $other->type;
    }
}
```

### 5.3 Entity: Transaction

```php
<?php

declare(strict_types=1);

namespace App\Domain\Transaction\Entities;

use App\Domain\Account\ValueObjects\Money;
use App\Domain\Shared\ValueObjects\Uuid;
use App\Domain\Transaction\Events\TransactionConfirmed;
use App\Domain\Transaction\Events\TransactionCreated;
use App\Domain\Transaction\ValueObjects\TransactionType;
use DateTimeImmutable;

class Transaction
{
    private array $domainEvents = [];

    private function __construct(
        private readonly Uuid $id,
        private readonly Uuid $accountId,
        private readonly Uuid $categoryId,
        private readonly TransactionType $type,
        private Money $amount,
        private string $description,
        private DateTimeImmutable $transactionDate,
        private ?DateTimeImmutable $competenceDate,
        private bool $isConfirmed,
        private ?Uuid $recurrenceId,
        private array $tags,
        private readonly DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        Uuid $accountId,
        Uuid $categoryId,
        TransactionType $type,
        Money $amount,
        string $description,
        DateTimeImmutable $transactionDate,
        ?DateTimeImmutable $competenceDate = null,
        bool $isConfirmed = false,
        ?Uuid $recurrenceId = null,
        array $tags = [],
    ): self {
        $transaction = new self(
            id: Uuid::generate(),
            accountId: $accountId,
            categoryId: $categoryId,
            type: $type,
            amount: $amount,
            description: $description,
            transactionDate: $transactionDate,
            competenceDate: $competenceDate ?? $transactionDate,
            isConfirmed: $isConfirmed,
            recurrenceId: $recurrenceId,
            tags: $tags,
            createdAt: new DateTimeImmutable(),
            updatedAt: null,
        );

        $transaction->recordEvent(new TransactionCreated($transaction));

        return $transaction;
    }

    public static function createQuick(
        Uuid $accountId,
        Uuid $categoryId,
        TransactionType $type,
        Money $amount,
        string $description,
    ): self {
        return self::create(
            accountId: $accountId,
            categoryId: $categoryId,
            type: $type,
            amount: $amount,
            description: $description,
            transactionDate: new DateTimeImmutable(),
            isConfirmed: true,
        );
    }

    public function confirm(): void
    {
        if ($this->isConfirmed) {
            return;
        }

        $this->isConfirmed = true;
        $this->updatedAt = new DateTimeImmutable();

        $this->recordEvent(new TransactionConfirmed($this));
    }

    public function updateAmount(Money $amount): void
    {
        $this->amount = $amount;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function effectiveAmount(): Money
    {
        return $this->type->isExpense()
            ? $this->amount->multiply(-1)
            : $this->amount;
    }

    // Getters
    public function id(): Uuid
    {
        return $this->id;
    }

    public function accountId(): Uuid
    {
        return $this->accountId;
    }

    public function categoryId(): Uuid
    {
        return $this->categoryId;
    }

    public function type(): TransactionType
    {
        return $this->type;
    }

    public function amount(): Money
    {
        return $this->amount;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function transactionDate(): DateTimeImmutable
    {
        return $this->transactionDate;
    }

    public function competenceDate(): ?DateTimeImmutable
    {
        return $this->competenceDate;
    }

    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    public function recurrenceId(): ?Uuid
    {
        return $this->recurrenceId;
    }

    public function tags(): array
    {
        return $this->tags;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    // Domain Events
    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
```

### 5.4 Entity: Goal

```php
<?php

declare(strict_types=1);

namespace App\Domain\Planning\Entities;

use App\Domain\Account\ValueObjects\Money;
use App\Domain\Planning\Events\GoalCompleted;
use App\Domain\Planning\Events\GoalContributionAdded;
use App\Domain\Planning\ValueObjects\GoalStatus;
use App\Domain\Shared\ValueObjects\Uuid;
use DateTimeImmutable;
use DomainException;

class Goal
{
    private array $domainEvents = [];

    private function __construct(
        private readonly Uuid $id,
        private readonly Uuid $organizationId,
        private string $name,
        private ?string $description,
        private readonly Money $targetAmount,
        private Money $currentAmount,
        private ?DateTimeImmutable $targetDate,
        private ?string $icon,
        private ?string $color,
        private GoalStatus $status,
        private readonly DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        Uuid $organizationId,
        string $name,
        Money $targetAmount,
        ?string $description = null,
        ?DateTimeImmutable $targetDate = null,
        ?string $icon = null,
        ?string $color = null,
    ): self {
        if ($targetAmount->isNegative() || $targetAmount->isZero()) {
            throw new DomainException('Target amount must be positive');
        }

        return new self(
            id: Uuid::generate(),
            organizationId: $organizationId,
            name: $name,
            description: $description,
            targetAmount: $targetAmount,
            currentAmount: Money::zero(),
            targetDate: $targetDate,
            icon: $icon,
            color: $color,
            status: GoalStatus::Active,
            createdAt: new DateTimeImmutable(),
            updatedAt: null,
        );
    }

    public function contribute(Money $amount): void
    {
        if ($this->status !== GoalStatus::Active) {
            throw new DomainException('Cannot contribute to inactive goal');
        }

        if ($amount->isNegative() || $amount->isZero()) {
            throw new DomainException('Contribution must be positive');
        }

        $this->currentAmount = $this->currentAmount->add($amount);
        $this->updatedAt = new DateTimeImmutable();

        $this->recordEvent(new GoalContributionAdded($this, $amount));

        if ($this->currentAmount->greaterThan($this->targetAmount) || 
            $this->currentAmount->equals($this->targetAmount)) {
            $this->complete();
        }
    }

    public function withdraw(Money $amount): void
    {
        if ($this->status !== GoalStatus::Active) {
            throw new DomainException('Cannot withdraw from inactive goal');
        }

        if ($amount->greaterThan($this->currentAmount)) {
            throw new DomainException('Insufficient funds in goal');
        }

        $this->currentAmount = $this->currentAmount->subtract($amount);
        $this->updatedAt = new DateTimeImmutable();
    }

    public function complete(): void
    {
        $this->status = GoalStatus::Completed;
        $this->updatedAt = new DateTimeImmutable();
        
        $this->recordEvent(new GoalCompleted($this));
    }

    public function cancel(): void
    {
        $this->status = GoalStatus::Cancelled;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function progressPercentage(): float
    {
        if ($this->targetAmount->isZero()) {
            return 0.0;
        }

        $percentage = ($this->currentAmount->cents() / $this->targetAmount->cents()) * 100;
        
        return min(100.0, round($percentage, 2));
    }

    public function remainingAmount(): Money
    {
        $remaining = $this->targetAmount->subtract($this->currentAmount);
        
        return $remaining->isNegative() ? Money::zero() : $remaining;
    }

    public function isOverdue(): bool
    {
        if ($this->targetDate === null) {
            return false;
        }

        return $this->targetDate < new DateTimeImmutable() 
            && $this->status === GoalStatus::Active;
    }

    // Getters
    public function id(): Uuid
    {
        return $this->id;
    }

    public function organizationId(): Uuid
    {
        return $this->organizationId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function targetAmount(): Money
    {
        return $this->targetAmount;
    }

    public function currentAmount(): Money
    {
        return $this->currentAmount;
    }

    public function targetDate(): ?DateTimeImmutable
    {
        return $this->targetDate;
    }

    public function icon(): ?string
    {
        return $this->icon;
    }

    public function color(): ?string
    {
        return $this->color;
    }

    public function status(): GoalStatus
    {
        return $this->status;
    }

    // Domain Events
    private function recordEvent(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }
}
```

### 5.5 Command e Handler: CreateTransaction

```php
<?php

declare(strict_types=1);

namespace App\Application\Transaction\Commands\CreateTransaction;

final readonly class CreateTransactionCommand
{
    public function __construct(
        public string $accountId,
        public string $categoryId,
        public string $type,
        public float $amount,
        public string $description,
        public string $transactionDate,
        public ?string $competenceDate = null,
        public bool $isConfirmed = false,
        public array $tags = [],
    ) {}
}
```

```php
<?php

declare(strict_types=1);

namespace App\Application\Transaction\Commands\CreateTransaction;

use App\Domain\Account\Repositories\AccountRepositoryInterface;
use App\Domain\Account\ValueObjects\Money;
use App\Domain\Shared\ValueObjects\Uuid;
use App\Domain\Transaction\Entities\Transaction;
use App\Domain\Transaction\Repositories\TransactionRepositoryInterface;
use App\Domain\Transaction\ValueObjects\TransactionType;
use DateTimeImmutable;
use Illuminate\Contracts\Events\Dispatcher;

final readonly class CreateTransactionHandler
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository,
        private AccountRepositoryInterface $accountRepository,
        private Dispatcher $eventDispatcher,
    ) {}

    public function handle(CreateTransactionCommand $command): Transaction
    {
        // Validar que a conta existe
        $account = $this->accountRepository->findById(
            Uuid::fromString($command->accountId)
        );

        if ($account === null) {
            throw new \DomainException('Account not found');
        }

        // Criar a transação
        $transaction = Transaction::create(
            accountId: Uuid::fromString($command->accountId),
            categoryId: Uuid::fromString($command->categoryId),
            type: TransactionType::from($command->type),
            amount: Money::fromDecimal($command->amount),
            description: $command->description,
            transactionDate: new DateTimeImmutable($command->transactionDate),
            competenceDate: $command->competenceDate 
                ? new DateTimeImmutable($command->competenceDate) 
                : null,
            isConfirmed: $command->isConfirmed,
            tags: $command->tags,
        );

        // Persistir
        $this->transactionRepository->save($transaction);

        // Dispatch domain events
        foreach ($transaction->pullDomainEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        return $transaction;
    }
}
```

### 5.6 Repository Interface e Implementação

```php
<?php

declare(strict_types=1);

namespace App\Domain\Transaction\Repositories;

use App\Domain\Shared\ValueObjects\Uuid;
use App\Domain\Transaction\Entities\Transaction;
use DateTimeImmutable;

interface TransactionRepositoryInterface
{
    public function save(Transaction $transaction): void;
    
    public function findById(Uuid $id): ?Transaction;
    
    public function findByAccountId(Uuid $accountId): array;
    
    public function findByDateRange(
        Uuid $organizationId,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): array;
    
    public function delete(Uuid $id): void;
}
```

```php
<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Shared\ValueObjects\Uuid;
use App\Domain\Transaction\Entities\Transaction;
use App\Domain\Transaction\Repositories\TransactionRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\TransactionModel;
use App\Infrastructure\Persistence\Mappers\TransactionMapper;
use DateTimeImmutable;

final readonly class EloquentTransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(
        private TransactionMapper $mapper,
    ) {}

    public function save(Transaction $transaction): void
    {
        $model = $this->mapper->toModel($transaction);
        $model->save();
    }

    public function findById(Uuid $id): ?Transaction
    {
        $model = TransactionModel::find($id->toString());
        
        if ($model === null) {
            return null;
        }
        
        return $this->mapper->toEntity($model);
    }

    public function findByAccountId(Uuid $accountId): array
    {
        $models = TransactionModel::where('account_id', $accountId->toString())
            ->orderByDesc('transaction_date')
            ->get();
        
        return $models->map(fn ($model) => $this->mapper->toEntity($model))->all();
    }

    public function findByDateRange(
        Uuid $organizationId,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): array {
        $models = TransactionModel::query()
            ->whereHas('account', fn ($q) => $q->where('organization_id', $organizationId->toString()))
            ->whereBetween('transaction_date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d'),
            ])
            ->orderByDesc('transaction_date')
            ->get();
        
        return $models->map(fn ($model) => $this->mapper->toEntity($model))->all();
    }

    public function delete(Uuid $id): void
    {
        TransactionModel::where('id', $id->toString())->delete();
    }
}
```

### 5.7 Controller com Feature Flag

```php
<?php

declare(strict_types=1);

namespace App\Interfaces\Http\Controllers\Api\V1;

use App\Application\Shared\Bus\CommandBusInterface;
use App\Application\Transaction\Commands\CreateQuickTransaction\CreateQuickTransactionCommand;
use App\Application\Transaction\Commands\CreateTransaction\CreateTransactionCommand;
use App\Infrastructure\FeatureFlags\FeatureFlagServiceInterface;
use App\Interfaces\Http\Requests\CreateTransactionRequest;
use App\Interfaces\Http\Requests\QuickTransactionRequest;
use App\Interfaces\Http\Resources\TransactionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class TransactionController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly FeatureFlagServiceInterface $featureFlags,
    ) {}

    public function store(CreateTransactionRequest $request): JsonResponse
    {
        $command = new CreateTransactionCommand(
            accountId: $request->validated('account_id'),
            categoryId: $request->validated('category_id'),
            type: $request->validated('type'),
            amount: $request->validated('amount'),
            description: $request->validated('description'),
            transactionDate: $request->validated('transaction_date'),
            competenceDate: $request->validated('competence_date'),
            isConfirmed: $request->validated('is_confirmed', false),
            tags: $request->validated('tags', []),
        );

        $transaction = $this->commandBus->dispatch($command);

        return response()->json(
            new TransactionResource($transaction),
            Response::HTTP_CREATED
        );
    }

    /**
     * Entrada rápida de transação (otimizada para mobile)
     */
    public function quick(QuickTransactionRequest $request): JsonResponse
    {
        // Feature Flag para versão melhorada
        $useV2 = $this->featureFlags->isEnabled('quick-transaction-v2', [
            'user_id' => auth()->id(),
        ]);

        if ($useV2) {
            return $this->quickV2($request);
        }

        $command = new CreateQuickTransactionCommand(
            accountId: $request->validated('account_id'),
            categoryId: $request->validated('category_id'),
            type: $request->validated('type'),
            amount: $request->validated('amount'),
            description: $request->validated('description'),
        );

        $transaction = $this->commandBus->dispatch($command);

        return response()->json(
            new TransactionResource($transaction),
            Response::HTTP_CREATED
        );
    }

    private function quickV2(QuickTransactionRequest $request): JsonResponse
    {
        // Implementação V2 com sugestões de categoria via ML
        // ...
    }
}
```

### 5.8 Service: Global Balance Calculator

```php
<?php

declare(strict_types=1);

namespace App\Domain\Account\Services;

use App\Domain\Account\Repositories\AccountRepositoryInterface;
use App\Domain\Account\ValueObjects\Money;
use App\Domain\Shared\ValueObjects\Uuid;
use App\Domain\Transaction\Repositories\TransactionRepositoryInterface;
use DateTimeImmutable;

final readonly class GlobalBalanceCalculator
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private TransactionRepositoryInterface $transactionRepository,
    ) {}

    public function calculate(Uuid $organizationId, ?DateTimeImmutable $untilDate = null): GlobalBalance
    {
        $accounts = $this->accountRepository->findByOrganizationId($organizationId);
        $untilDate ??= new DateTimeImmutable();
        
        $totalBalance = Money::zero();
        $totalIncome = Money::zero();
        $totalExpense = Money::zero();
        $accountBalances = [];

        foreach ($accounts as $account) {
            $transactions = $this->transactionRepository->findByDateRange(
                $organizationId,
                new DateTimeImmutable('1970-01-01'),
                $untilDate
            );

            $accountBalance = $account->initialBalance();

            foreach ($transactions as $transaction) {
                if (!$transaction->accountId()->equals($account->id())) {
                    continue;
                }

                if (!$transaction->isConfirmed()) {
                    continue;
                }

                $accountBalance = $accountBalance->add($transaction->effectiveAmount());

                if ($transaction->type()->isIncome()) {
                    $totalIncome = $totalIncome->add($transaction->amount());
                } else {
                    $totalExpense = $totalExpense->add($transaction->amount());
                }
            }

            $accountBalances[$account->id()->toString()] = $accountBalance;
            $totalBalance = $totalBalance->add($accountBalance);
        }

        return new GlobalBalance(
            organizationId: $organizationId,
            totalBalance: $totalBalance,
            totalIncome: $totalIncome,
            totalExpense: $totalExpense,
            accountBalances: $accountBalances,
            calculatedAt: new DateTimeImmutable(),
        );
    }
}
```

### 5.9 Feature Flag Service

```php
<?php

declare(strict_types=1);

namespace App\Infrastructure\FeatureFlags;

interface FeatureFlagServiceInterface
{
    public function isEnabled(string $feature, array $context = []): bool;
    
    public function getValue(string $feature, mixed $default = null, array $context = []): mixed;
    
    public function define(string $feature, callable $resolver): void;
}
```

```php
<?php

declare(strict_types=1);

namespace App\Infrastructure\FeatureFlags;

use Laravel\Pennant\Feature;

final class LaravelPennantAdapter implements FeatureFlagServiceInterface
{
    public function isEnabled(string $feature, array $context = []): bool
    {
        $scope = $context['user_id'] ?? null;

        return Feature::for($scope)->active($feature);
    }

    public function getValue(string $feature, mixed $default = null, array $context = []): mixed
    {
        $scope = $context['user_id'] ?? null;

        return Feature::for($scope)->value($feature) ?? $default;
    }

    public function define(string $feature, callable $resolver): void
    {
        Feature::define($feature, $resolver);
    }
}
```

---

## 6. Configuração Docker

### 6.1 docker-compose.yml

```yaml
services:
  app:
    build:
      context: .
      dockerfile: docker/Dockerfile
    container_name: finance_app
    restart: unless-stopped
    volumes:
      - .:/var/www/html
      - ./docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini
    networks:
      - finance_network
    depends_on:
      - db
      - redis

  nginx:
    image: nginx:alpine
    container_name: finance_nginx
    restart: unless-stopped
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    networks:
      - finance_network
    depends_on:
      - app

  db:
    image: postgres:16-alpine
    container_name: finance_db
    restart: unless-stopped
    environment:
      POSTGRES_DB: ${DB_DATABASE:-finance}
      POSTGRES_USER: ${DB_USERNAME:-finance}
      POSTGRES_PASSWORD: ${DB_PASSWORD:-secret}
    volumes:
      - db_data:/var/lib/postgresql/data
    ports:
      - "5432:5432"
    networks:
      - finance_network

  redis:
    image: redis:7-alpine
    container_name: finance_redis
    restart: unless-stopped
    command: redis-server --appendonly yes
    volumes:
      - redis_data:/data
    ports:
      - "6379:6379"
    networks:
      - finance_network

  # Queue Worker
  queue:
    build:
      context: .
      dockerfile: docker/Dockerfile
    container_name: finance_queue
    restart: unless-stopped
    command: php artisan queue:work --sleep=3 --tries=3 --max-time=3600
    volumes:
      - .:/var/www/html
    networks:
      - finance_network
    depends_on:
      - app
      - redis

  # Scheduler
  scheduler:
    build:
      context: .
      dockerfile: docker/Dockerfile
    container_name: finance_scheduler
    restart: unless-stopped
    command: >
      sh -c "while true; do php artisan schedule:run --verbose --no-interaction; sleep 60; done"
    volumes:
      - .:/var/www/html
    networks:
      - finance_network
    depends_on:
      - app

networks:
  finance_network:
    driver: bridge

volumes:
  db_data:
  redis_data:
```

### 6.2 Dockerfile

```dockerfile
FROM php:8.3-fpm-alpine

# Argumentos
ARG UID=1000
ARG GID=1000

# Instalar dependências do sistema
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    icu-dev \
    linux-headers \
    $PHPIZE_DEPS

# Instalar extensões PHP
RUN docker-php-ext-configure intl \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        bcmath \
        intl \
        opcache \
        pcntl

# Instalar Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Criar usuário
RUN addgroup -g $GID appgroup && adduser -u $UID -G appgroup -s /bin/sh -D appuser

# Configurar diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos
COPY --chown=appuser:appgroup . .

# Trocar para usuário não-root
USER appuser

# Instalar dependências (em produção, adicionar --no-dev)
RUN composer install --optimize-autoloader

EXPOSE 9000

CMD ["php-fpm"]
```

### 6.3 Nginx Configuration

```nginx
# docker/nginx/default.conf
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;
    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Gzip
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml application/javascript application/json;
}
```

### 6.4 PHP Configuration

```ini
; docker/php/local.ini
upload_max_filesize = 40M
post_max_size = 40M
memory_limit = 256M
max_execution_time = 600

; OPcache
opcache.enable=1
opcache.revalidate_freq=0
opcache.validate_timestamps=1
opcache.max_accelerated_files=10000
opcache.memory_consumption=192
opcache.max_wasted_percentage=10
opcache.interned_strings_buffer=16
```

---

## 7. CI/CD Pipeline (GitHub Actions)

### 7.1 CI Pipeline (.github/workflows/ci.yml)

```yaml
name: CI Pipeline

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main, develop]

jobs:
  tests:
    runs-on: ubuntu-latest
    
    services:
      postgres:
        image: postgres:16-alpine
        env:
          POSTGRES_DB: finance_test
          POSTGRES_USER: finance
          POSTGRES_PASSWORD: secret
        ports:
          - 5432:5432
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
      
      redis:
        image: redis:7-alpine
        ports:
          - 6379:6379
        options: >-
          --health-cmd "redis-cli ping"
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5

    steps:
      - uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: pdo_pgsql, redis, bcmath, intl
          coverage: xdebug

      - name: Cache Composer dependencies
        uses: actions/cache@v4
        with:
          path: vendor
          key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
          restore-keys: ${{ runner.os }}-composer-

      - name: Install dependencies
        run: composer install --prefer-dist --no-progress

      - name: Copy .env
        run: cp .env.testing .env

      - name: Generate key
        run: php artisan key:generate

      - name: Run migrations
        run: php artisan migrate --force
        env:
          DB_CONNECTION: pgsql
          DB_HOST: localhost
          DB_PORT: 5432
          DB_DATABASE: finance_test
          DB_USERNAME: finance
          DB_PASSWORD: secret

      - name: Run PHPStan
        run: vendor/bin/phpstan analyse --memory-limit=2G

      - name: Run tests
        run: php artisan test --coverage --min=80
        env:
          DB_CONNECTION: pgsql
          DB_HOST: localhost
          DB_PORT: 5432
          DB_DATABASE: finance_test
          DB_USERNAME: finance
          DB_PASSWORD: secret
          REDIS_HOST: localhost

  lint:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'

      - name: Install dependencies
        run: composer install --prefer-dist --no-progress

      - name: Run Pint
        run: vendor/bin/pint --test

  security:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      
      - name: Security Audit
        run: composer audit
```

### 7.2 Deploy Pipeline (.github/workflows/deploy.yml)

```yaml
name: Deploy

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'
    needs: [tests, lint]
    
    steps:
      - uses: actions/checkout@v4

      - name: Build Docker image
        run: |
          docker build -t finance-api:${{ github.sha }} \
            --build-arg APP_ENV=production \
            -f docker/Dockerfile .

      - name: Push to Registry
        run: |
          echo ${{ secrets.REGISTRY_PASSWORD }} | docker login -u ${{ secrets.REGISTRY_USER }} --password-stdin
          docker tag finance-api:${{ github.sha }} registry.example.com/finance-api:${{ github.sha }}
          docker tag finance-api:${{ github.sha }} registry.example.com/finance-api:latest
          docker push registry.example.com/finance-api:${{ github.sha }}
          docker push registry.example.com/finance-api:latest

      - name: Deploy to Production
        uses: appleboy/ssh-action@v1
        with:
          host: ${{ secrets.PROD_HOST }}
          username: ${{ secrets.PROD_USER }}
          key: ${{ secrets.PROD_SSH_KEY }}
          script: |
            cd /opt/finance
            docker compose pull
            docker compose up -d --remove-orphans
            docker exec finance_app php artisan migrate --force
            docker exec finance_app php artisan config:cache
            docker exec finance_app php artisan route:cache
            docker exec finance_app php artisan view:cache
```

---

## 8. Estratégia de Feature Flags

### 8.1 Provider de Features

```php
<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Lottery;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;

class FeatureFlagServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Gradual rollout do novo dashboard
        Feature::define('new-dashboard', fn (User $user) => match (true) {
            $user->is_beta_tester => true,
            $user->created_at->isAfter(now()->subDays(7)) => true,
            default => Lottery::odds(1, 10)->choose(), // 10% dos usuários
        });

        // Quick Transaction V2 com ML
        Feature::define('quick-transaction-v2', fn (User $user) => 
            $user->organization->plan === 'premium'
        );

        // Multi-currency support
        Feature::define('multi-currency', fn () => false); // Desabilitado por padrão

        // Importação via OFX/CSV
        Feature::define('import-transactions', fn (User $user) => 
            $user->organization->type === 'PJ'
        );

        // Dark mode no frontend
        Feature::define('dark-mode', fn () => true);
    }
}
```

### 8.2 Features Planejadas

| Feature Flag | Descrição | Critério de Ativação |
|--------------|-----------|----------------------|
| `new-dashboard` | Novo design do dashboard | Beta testers + 10% gradual |
| `quick-transaction-v2` | Entrada rápida com ML | Plano premium |
| `multi-currency` | Suporte multi-moeda | Desabilitado (futuro) |
| `import-transactions` | Importação OFX/CSV | Apenas PJ |
| `dark-mode` | Tema escuro | Todos os usuários |
| `recurring-v2` | Nova engine de recorrência | Beta testers |
| `goals-sharing` | Compartilhamento de metas | Premium + Beta |

---

## 9. API Endpoints

### Autenticação

```
POST   /api/v1/auth/register          # Criar conta
POST   /api/v1/auth/login             # Login
POST   /api/v1/auth/logout            # Logout
POST   /api/v1/auth/refresh           # Renovar token
POST   /api/v1/auth/forgot-password   # Recuperar senha
POST   /api/v1/auth/reset-password    # Redefinir senha
```

### Organização (PF/PJ)

```
GET    /api/v1/organization           # Dados da organização
PUT    /api/v1/organization           # Atualizar organização
```

### Contas

```
GET    /api/v1/accounts               # Listar contas
POST   /api/v1/accounts               # Criar conta
GET    /api/v1/accounts/{id}          # Detalhes da conta
PUT    /api/v1/accounts/{id}          # Atualizar conta
DELETE /api/v1/accounts/{id}          # Excluir conta
GET    /api/v1/accounts/{id}/balance  # Saldo da conta
GET    /api/v1/balance/global         # Saldo consolidado

# Endpoints específicos para empréstimos
POST   /api/v1/accounts/{id}/lend     # Criar empréstimo para outro usuário
GET    /api/v1/accounts/{id}/loans   # Listar empréstimos da conta
POST   /api/v1/accounts/{id}/repay    # Registrar pagamento de empréstimo
```

### Categorias

```
GET    /api/v1/categories             # Listar categorias
POST   /api/v1/categories             # Criar categoria
PUT    /api/v1/categories/{id}        # Atualizar categoria
DELETE /api/v1/categories/{id}        # Excluir categoria
```

### Transações

```
GET    /api/v1/transactions           # Listar transações
POST   /api/v1/transactions           # Criar transação
POST   /api/v1/transactions/quick     # Entrada rápida
GET    /api/v1/transactions/{id}      # Detalhes da transação
PUT    /api/v1/transactions/{id}      # Atualizar transação
DELETE /api/v1/transactions/{id}      # Excluir transação
POST   /api/v1/transactions/{id}/confirm  # Confirmar transação
POST   /api/v1/transactions/import    # Importar (feature flag)
```

### Recorrências

```
GET    /api/v1/recurrences            # Listar recorrências
POST   /api/v1/recurrences            # Criar recorrência
GET    /api/v1/recurrences/{id}       # Detalhes
PUT    /api/v1/recurrences/{id}       # Atualizar
DELETE /api/v1/recurrences/{id}       # Excluir
```

### Objetivos/Metas

```
GET    /api/v1/goals                  # Listar metas
POST   /api/v1/goals                  # Criar meta
GET    /api/v1/goals/{id}             # Detalhes da meta
PUT    /api/v1/goals/{id}             # Atualizar meta
DELETE /api/v1/goals/{id}             # Excluir meta
POST   /api/v1/goals/{id}/contribute  # Contribuir para meta
POST   /api/v1/goals/{id}/withdraw    # Retirar da meta
```

### Relatórios

```
GET    /api/v1/reports/summary        # Resumo geral
GET    /api/v1/reports/by-category    # Por categoria
GET    /api/v1/reports/cash-flow      # Fluxo de caixa
GET    /api/v1/reports/trends         # Tendências
```

### Feature Flags (Frontend)

```
GET    /api/v1/features               # Features ativas para o usuário
```

---

## 9.1 Importação de Transações via OFX

### Visão Geral

A importação de transações via arquivo OFX permite que usuários importem extratos bancários automaticamente, facilitando o registro de múltiplas transações de uma vez.

### Funcionalidades

- **Parser de arquivo OFX:** Leitura e interpretação de arquivos OFX padrão
- **Mapeamento automático:** Identificação de conta, data, valor e descrição
- **Detecção de duplicatas:** Evita importar transações já existentes
- **Processamento em lote:** Importação assíncrona para grandes volumes
- **Relatório de importação:** Feedback sobre sucessos e falhas

### Estrutura Técnica

#### Service de Parsing OFX

```php
<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Domain\Transaction\ValueObjects\TransactionType;
use App\Domain\Shared\ValueObjects\Money;
use DateTimeImmutable;

final class OfxParser
{
    public function parse(string $ofxContent): array
    {
        // Parse do arquivo OFX
        // Retorna array de transações estruturadas
        return [
            [
                'account_id' => 'uuid',
                'type' => TransactionType::EXPENSE,
                'amount' => Money::fromDecimal('100.00'),
                'description' => 'Compra no mercado',
                'transaction_date' => new DateTimeImmutable('2025-01-15'),
                'competence_date' => new DateTimeImmutable('2025-01-15'),
            ],
            // ... mais transações
        ];
    }
}
```

#### Command de Importação

```php
<?php

declare(strict_types=1);

namespace App\Application\Transaction\Commands;

use App\Domain\Shared\ValueObjects\Uuid;

final class ImportTransactionsCommand
{
    public function __construct(
        public readonly string $organizationId,
        public readonly string $accountId,
        public readonly string $filePath,
        public readonly bool $skipDuplicates = true,
    ) {}
}
```

#### Handler de Importação

```php
<?php

declare(strict_types=1);

namespace App\Application\Transaction\Handlers;

use App\Application\Transaction\Commands\ImportTransactionsCommand;
use App\Infrastructure\Services\OfxParser;
use App\Domain\Transaction\Repositories\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class ImportTransactionsHandler
{
    public function __construct(
        private readonly OfxParser $ofxParser,
        private readonly TransactionRepositoryInterface $transactionRepository,
    ) {}

    public function handle(ImportTransactionsCommand $command): ImportResult
    {
        $ofxContent = file_get_contents($command->filePath);
        $transactions = $this->ofxParser->parse($ofxContent);
        
        $imported = 0;
        $skipped = 0;
        $errors = [];
        
        DB::beginTransaction();
        
        try {
            foreach ($transactions as $transactionData) {
                try {
                    // Verificar duplicatas
                    if ($command->skipDuplicates && $this->isDuplicate($transactionData)) {
                        $skipped++;
                        continue;
                    }
                    
                    // Criar transação
                    $transaction = Transaction::create(...$transactionData);
                    $this->transactionRepository->save($transaction);
                    $imported++;
                    
                } catch (\Exception $e) {
                    $errors[] = [
                        'transaction' => $transactionData,
                        'error' => $e->getMessage(),
                    ];
                    Log::error('Erro ao importar transação', [
                        'error' => $e->getMessage(),
                        'data' => $transactionData,
                    ]);
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        
        return new ImportResult(
            imported: $imported,
            skipped: $skipped,
            errors: $errors,
        );
    }
    
    private function isDuplicate(array $transactionData): bool
    {
        // Verificar se já existe transação com mesmo valor, data e descrição
        return $this->transactionRepository->exists(
            accountId: Uuid::fromString($transactionData['account_id']),
            amount: $transactionData['amount'],
            date: $transactionData['transaction_date'],
            description: $transactionData['description'],
        );
    }
}
```

#### Controller de Importação

```php
<?php

namespace App\Interfaces\Http\Controllers\Api\V1;

use App\Application\Transaction\Commands\ImportTransactionsCommand;
use App\Application\Transaction\Handlers\ImportTransactionsHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

final class TransactionImportController extends Controller
{
    public function __construct(
        private readonly ImportTransactionsHandler $handler,
    ) {}
    
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|uuid|exists:accounts,id',
            'file' => 'required|file|mimes:ofx|max:10240', // 10MB
            'skip_duplicates' => 'boolean',
        ]);
        
        $file = $request->file('file');
        $path = $file->store('imports');
        
        try {
            $command = new ImportTransactionsCommand(
                organizationId: $request->user()->organization_id,
                accountId: $request->input('account_id'),
                filePath: storage_path("app/{$path}"),
                skipDuplicates: $request->input('skip_duplicates', true),
            );
            
            $result = $this->handler->handle($command);
            
            // Limpar arquivo temporário
            Storage::delete($path);
            
            return response()->json([
                'message' => 'Importação concluída',
                'data' => [
                    'imported' => $result->imported,
                    'skipped' => $result->skipped,
                    'errors_count' => count($result->errors),
                    'errors' => $result->errors,
                ],
            ], 200);
            
        } catch (\Exception $e) {
            Storage::delete($path);
            
            return response()->json([
                'message' => 'Erro ao importar transações',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
```

### Job para Processamento Assíncrono

```php
<?php

namespace App\Infrastructure\Jobs;

use App\Application\Transaction\Commands\ImportTransactionsCommand;
use App\Application\Transaction\Handlers\ImportTransactionsHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class ImportTransactionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $tries = 3;
    public $timeout = 300; // 5 minutos
    
    public function __construct(
        private readonly ImportTransactionsCommand $command,
    ) {}
    
    public function handle(ImportTransactionsHandler $handler): void
    {
        $handler->handle($this->command);
    }
}
```

### Validações e Regras de Negócio

1. **Formato do arquivo:** Apenas arquivos OFX válidos
2. **Tamanho máximo:** 10MB por arquivo
3. **Conta válida:** A conta deve pertencer à organização do usuário
4. **Detecção de duplicatas:** Baseada em valor, data e descrição
5. **Transações futuras:** Opção de importar ou ignorar transações com data futura

### Feature Flag

A importação OFX está protegida por feature flag `import-transactions`, que pode ser configurada por:
- Tipo de organização (PF/PJ)
- Plano do usuário
- Rollout gradual

### Exemplo de Uso

```bash
POST /api/v1/transactions/import
Content-Type: multipart/form-data

{
  "account_id": "550e8400-e29b-41d4-a716-446655440000",
  "file": <arquivo.ofx>,
  "skip_duplicates": true
}
```

**Resposta:**
```json
{
  "message": "Importação concluída",
  "data": {
    "imported": 45,
    "skipped": 3,
    "errors_count": 0,
    "errors": []
  }
}
```

---

## 10. Otimizações de Alta Performance

### 10.1 Estratégia de Cache Multi-Camada

#### Cache Layers

```
┌─────────────────────────────────────────┐
│  Application Cache (Redis)              │
│  - Saldos globais                       │
│  - Relatórios agregados                 │
│  - Feature flags                        │
│  - TTL: 5-60 minutos                    │
└─────────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│  Database Query Cache (Redis)            │
│  - Resultados de queries frequentes      │
│  - Listagens paginadas                  │
│  - TTL: 1-15 minutos                    │
└─────────────────────────────────────────┘
                  │
                  ▼
┌─────────────────────────────────────────┐
│  HTTP Response Cache (Nginx/Varnish)     │
│  - Endpoints GET públicos                │
│  - Headers ETag/Last-Modified            │
│  - TTL: 1-5 minutos                     │
└─────────────────────────────────────────┘
```

#### Implementação de Cache de Saldo Global

```php
<?php

namespace App\Infrastructure\Cache;

use App\Domain\Account\Services\GlobalBalanceCalculator;
use App\Domain\Shared\ValueObjects\Uuid;
use Illuminate\Contracts\Cache\Repository;
use DateTimeImmutable;

final class CachedGlobalBalanceCalculator
{
    private const CACHE_TTL = 300; // 5 minutos
    private const CACHE_PREFIX = 'global_balance:';

    public function __construct(
        private readonly GlobalBalanceCalculator $calculator,
        private readonly Repository $cache,
    ) {}

    public function calculate(Uuid $organizationId, ?DateTimeImmutable $untilDate = null): GlobalBalance
    {
        $cacheKey = $this->buildCacheKey($organizationId, $untilDate);

        return $this->cache->remember($cacheKey, self::CACHE_TTL, function () use ($organizationId, $untilDate) {
            return $this->calculator->calculate($organizationId, $untilDate);
        });
    }

    public function invalidate(Uuid $organizationId): void
    {
        $pattern = self::CACHE_PREFIX . $organizationId->toString() . ':*';
        $this->cache->forget($pattern);
    }

    private function buildCacheKey(Uuid $organizationId, ?DateTimeImmutable $untilDate): string
    {
        $dateKey = $untilDate?->format('Y-m-d') ?? 'latest';
        return self::CACHE_PREFIX . $organizationId->toString() . ':' . $dateKey;
    }
}
```

#### Cache Warming Strategy

```php
<?php

namespace App\Infrastructure\Console\Commands;

use App\Domain\Account\Services\GlobalBalanceCalculator;
use App\Domain\Identity\Repositories\OrganizationRepositoryInterface;
use Illuminate\Console\Command;

final class WarmGlobalBalanceCache extends Command
{
    protected $signature = 'cache:warm-balances {--organization=}';
    protected $description = 'Warm up global balance cache for all organizations';

    public function handle(
        OrganizationRepositoryInterface $organizationRepository,
        GlobalBalanceCalculator $calculator
    ): int {
        $organizationId = $this->option('organization');

        if ($organizationId) {
            $organizations = [$organizationRepository->findById(Uuid::fromString($organizationId))];
        } else {
            $organizations = $organizationRepository->findAll();
        }

        $bar = $this->output->createProgressBar(count($organizations));
        $bar->start();

        foreach ($organizations as $organization) {
            $calculator->calculate($organization->id());
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Cache warmed successfully!');

        return Command::SUCCESS;
    }
}
```

### 10.2 Otimizações de Banco de Dados

#### Índices Compostos Otimizados

```php
// Migration: add_performance_indexes.php
Schema::table('transactions', function (Blueprint $table) {
    // Índice para queries de saldo por conta
    $table->index(['account_id', 'is_confirmed', 'transaction_date'], 'idx_account_confirmed_date');
    
    // Índice para relatórios por categoria
    $table->index(['category_id', 'type', 'competence_date'], 'idx_category_type_competence');
    
    // Índice para filtros de organização (via account)
    $table->index(['account_id', 'competence_date', 'type'], 'idx_account_competence_type');
    
    // Índice parcial para transações confirmadas (PostgreSQL)
    $table->rawIndex(
        "CREATE INDEX idx_confirmed_transactions ON transactions (account_id, transaction_date) WHERE is_confirmed = true",
        'idx_confirmed_transactions'
    );
});

Schema::table('accounts', function (Blueprint $table) {
    $table->index(['organization_id', 'is_active'], 'idx_org_active');
});

Schema::table('goals', function (Blueprint $table) {
    $table->index(['organization_id', 'status', 'target_date'], 'idx_org_status_date');
});
```

#### Particionamento de Tabelas (PostgreSQL)

```php
// Migration: partition_transactions_by_year.php
DB::statement('
    CREATE TABLE transactions_2024 PARTITION OF transactions
    FOR VALUES FROM (\'2024-01-01\') TO (\'2025-01-01\');
');

DB::statement('
    CREATE TABLE transactions_2025 PARTITION OF transactions
    FOR VALUES FROM (\'2025-01-01\') TO (\'2026-01-01\');
');
```

#### Materialized Views para Relatórios

```php
// Migration: create_report_materialized_views.php
DB::statement('
    CREATE MATERIALIZED VIEW mv_monthly_summary AS
    SELECT 
        organization_id,
        DATE_TRUNC(\'month\', competence_date) as month,
        type,
        category_id,
        SUM(amount) as total_amount,
        COUNT(*) as transaction_count
    FROM transactions t
    INNER JOIN accounts a ON t.account_id = a.id
    WHERE t.is_confirmed = true
    GROUP BY organization_id, DATE_TRUNC(\'month\', competence_date), type, category_id;
');

DB::statement('CREATE INDEX idx_mv_monthly_org ON mv_monthly_summary (organization_id, month);');

// Refresh automático via scheduler
DB::statement('
    CREATE OR REPLACE FUNCTION refresh_monthly_summary()
    RETURNS void AS $$
    BEGIN
        REFRESH MATERIALIZED VIEW CONCURRENTLY mv_monthly_summary;
    END;
    $$ LANGUAGE plpgsql;
');
```

### 10.3 CQRS e Read Models

#### Estrutura de Read Models

```
app/
├── Infrastructure/
│   ├── ReadModels/
│   │   ├── TransactionReadModel.php
│   │   ├── BalanceReadModel.php
│   │   ├── CategorySummaryReadModel.php
│   │   └── GoalProgressReadModel.php
│   └── Projectors/
│       ├── TransactionProjector.php
│       └── BalanceProjector.php
```

#### Implementação de Read Model

```php
<?php

namespace App\Infrastructure\ReadModels;

use Illuminate\Support\Facades\DB;

final class CategorySummaryReadModel
{
    public function getMonthlySummary(string $organizationId, string $year, string $month): array
    {
        return DB::table('mv_monthly_summary')
            ->where('organization_id', $organizationId)
            ->where('month', "{$year}-{$month}-01")
            ->get()
            ->groupBy('type')
            ->map(fn ($items) => $items->sum('total_amount'))
            ->toArray();
    }

    public function getByCategory(string $organizationId, string $startDate, string $endDate): array
    {
        return DB::table('mv_monthly_summary')
            ->select('category_id', 'type', DB::raw('SUM(total_amount) as total'))
            ->where('organization_id', $organizationId)
            ->whereBetween('month', [$startDate, $endDate])
            ->groupBy('category_id', 'type')
            ->get()
            ->toArray();
    }
}
```

### 10.4 Otimização de Queries Eloquent

#### Repository Otimizado com Eager Loading

```php
<?php

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

final class EloquentTransactionRepository implements TransactionRepositoryInterface
{
    public function findByAccountIdWithRelations(Uuid $accountId, int $limit = 50): array
    {
        $models = TransactionModel::query()
            ->where('account_id', $accountId->toString())
            ->with(['category:id,name,icon,color', 'account:id,name'])
            ->orderByDesc('transaction_date')
            ->limit($limit)
            ->cursor(); // Usa cursor para reduzir memória

        return iterator_to_array(
            (function () use ($models) {
                foreach ($models as $model) {
                    yield $this->mapper->toEntity($model);
                }
            })()
        );
    }

    public function findByDateRangeChunked(
        Uuid $organizationId,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        callable $callback
    ): void {
        TransactionModel::query()
            ->whereHas('account', fn ($q) => $q->where('organization_id', $organizationId->toString()))
            ->whereBetween('transaction_date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d'),
            ])
            ->orderBy('transaction_date')
            ->chunk(1000, function ($transactions) use ($callback) {
                $entities = $transactions->map(fn ($model) => $this->mapper->toEntity($model))->all();
                $callback($entities);
            });
    }
}
```

### 10.5 API Performance

#### Paginação Otimizada com Cursor

```php
<?php

namespace App\Interfaces\Http\Controllers\Api\V1;

final class TransactionController
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->integer('per_page', 20), 100);
        $cursor = $request->string('cursor');

        $query = TransactionModel::query()
            ->where('account_id', $request->user()->organization_id)
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');

        if ($cursor) {
            [$date, $id] = explode('_', base64_decode($cursor));
            $query->where(function ($q) use ($date, $id) {
                $q->where('transaction_date', '<', $date)
                  ->orWhere(function ($q2) use ($date, $id) {
                      $q2->where('transaction_date', $date)
                         ->where('id', '<', $id);
                  });
            });
        }

        $transactions = $query->limit($perPage + 1)->get();
        $hasMore = $transactions->count() > $perPage;

        if ($hasMore) {
            $transactions->pop();
        }

        $nextCursor = $hasMore 
            ? base64_encode($transactions->last()->transaction_date . '_' . $transactions->last()->id)
            : null;

        return response()->json([
            'data' => TransactionResource::collection($transactions),
            'meta' => [
                'has_more' => $hasMore,
                'next_cursor' => $nextCursor,
            ],
        ]);
    }
}
```

#### Response Compression e ETags

```php
<?php

namespace App\Interfaces\Http\Middleware;

final class ResponseCacheMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET') && $response->getStatusCode() === 200) {
            $etag = md5($response->getContent());
            $response->setEtag($etag);

            if ($request->header('If-None-Match') === $etag) {
                return response('', 304);
            }
        }

        return $response;
    }
}
```

### 10.6 Background Processing

#### Job para Cálculo Assíncrono de Saldo

```php
<?php

namespace App\Infrastructure\Jobs;

use App\Domain\Account\Services\GlobalBalanceCalculator;
use App\Domain\Shared\ValueObjects\Uuid;
use DateTimeImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

final class CalculateGlobalBalanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $organizationId,
        private readonly ?string $untilDate = null,
    ) {}

    public function handle(GlobalBalanceCalculator $calculator): void
    {
        $calculator->calculate(
            Uuid::fromString($this->organizationId),
            $this->untilDate ? new DateTimeImmutable($this->untilDate) : null
        );
    }

    public function retryUntil(): Carbon
    {
        return now()->addMinutes(10);
    }
}
```

#### Event Listener para Invalidação de Cache

```php
<?php

namespace App\Infrastructure\Listeners;

use App\Domain\Account\Repositories\AccountRepositoryInterface;
use App\Domain\Transaction\Events\TransactionConfirmed;
use App\Infrastructure\Cache\CachedGlobalBalanceCalculator;
use App\Infrastructure\Jobs\CalculateGlobalBalanceJob;
use Illuminate\Contracts\Queue\ShouldQueue;

final class InvalidateBalanceCacheListener implements ShouldQueue
{
    public function __construct(
        private readonly CachedGlobalBalanceCalculator $cachedCalculator,
        private readonly AccountRepositoryInterface $accountRepository,
    ) {}

    public function handle(TransactionConfirmed $event): void
    {
        $account = $this->accountRepository->findById($event->transaction->accountId());
        
        if ($account === null) {
            return;
        }
        
        $organizationId = $account->organizationId();
        $this->cachedCalculator->invalidate($organizationId);
        
        // Recalcular em background
        CalculateGlobalBalanceJob::dispatch($organizationId->toString());
    }
}
```

### 10.7 Database Connection Pooling

#### Configuração PgBouncer no Docker

```yaml
# docker-compose.yml
services:
  pgbouncer:
    image: pgbouncer/pgbouncer:latest
    container_name: finance_pgbouncer
    environment:
      DATABASES_HOST: db
      DATABASES_PORT: 5432
      DATABASES_USER: ${DB_USERNAME}
      DATABASES_PASSWORD: ${DB_PASSWORD}
      DATABASES_DBNAME: ${DB_DATABASE}
      PGBOUNCER_POOL_MODE: transaction
      PGBOUNCER_MAX_CLIENT_CONN: 1000
      PGBOUNCER_DEFAULT_POOL_SIZE: 25
      PGBOUNCER_MIN_POOL_SIZE: 5
      PGBOUNCER_RESERVE_POOL_SIZE: 5
    ports:
      - "6432:6432"
    networks:
      - finance_network
    depends_on:
      - db
```

#### Configuração Laravel para PgBouncer

```php
// config/database.php
'pgsql' => [
    'driver' => 'pgsql',
    'host' => env('DB_HOST', 'pgbouncer'), // Usar PgBouncer
    'port' => env('DB_PORT', '6432'),
    'database' => env('DB_DATABASE'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
    'charset' => 'utf8',
    'prefix' => '',
    'prefix_indexes' => true,
    'search_path' => 'public',
    'sslmode' => 'prefer',
    'options' => [
        PDO::ATTR_PERSISTENT => false, // Importante para pooling
    ],
],
```

### 10.8 CDN e Static Assets

#### Configuração Nginx com Cache de Assets

```nginx
# docker/nginx/default.conf
server {
    # ... configurações existentes ...

    # Cache de assets estáticos
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Cache de API responses (com validação)
    location ~ ^/api/v1/(accounts|categories|goals)$ {
        proxy_cache api_cache;
        proxy_cache_valid 200 5m;
        proxy_cache_key "$scheme$request_method$host$request_uri$http_authorization";
        proxy_cache_use_stale error timeout updating http_500 http_502 http_503 http_504;
        add_header X-Cache-Status $upstream_cache_status;
        
        proxy_pass http://app:9000;
    }
}

# Zona de cache
proxy_cache_path /var/cache/nginx levels=1:2 keys_zone=api_cache:10m max_size=1g inactive=60m use_temp_path=off;
```

### 10.9 Monitoring e Observability

#### APM com Laravel Telescope (Dev) e DataDog/New Relic (Prod)

```php
// config/telescope.php (apenas desenvolvimento)
'watchers' => [
    Watchers\CacheWatcher::class => ['enabled' => true],
    Watchers\QueryWatcher::class => ['enabled' => true, 'slow' => 100],
    Watchers\RequestWatcher::class => ['enabled' => true],
    Watchers\JobWatcher::class => ['enabled' => true],
];
```

#### Logging Estruturado

```php
<?php

namespace App\Infrastructure\Logging;

use Illuminate\Support\Facades\Log;

final class PerformanceLogger
{
    public function logSlowQuery(string $query, float $time, array $bindings = []): void
    {
        if ($time > 1.0) { // Queries > 1 segundo
            Log::warning('Slow query detected', [
                'query' => $query,
                'time' => $time,
                'bindings' => $bindings,
                'context' => 'database',
            ]);
        }
    }

    public function logCacheHit(string $key): void
    {
        Log::debug('Cache hit', [
            'key' => $key,
            'context' => 'cache',
        ]);
    }

    public function logCacheMiss(string $key): void
    {
        Log::debug('Cache miss', [
            'key' => $key,
            'context' => 'cache',
        ]);
    }
}
```

### 10.10 Horizontal Scaling

#### Load Balancer Configuration

```yaml
# docker-compose.prod.yml
services:
  nginx-lb:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./docker/nginx/load-balancer.conf:/etc/nginx/nginx.conf
    depends_on:
      - app1
      - app2
      - app3

  app1:
    # ... mesma configuração do app
  app2:
    # ... mesma configuração do app
  app3:
    # ... mesma configuração do app
```

```nginx
# docker/nginx/load-balancer.conf
upstream app_servers {
    least_conn; # Balanceamento por menor conexão
    server app1:9000;
    server app2:9000;
    server app3:9000;
}

server {
    listen 80;
    location / {
        proxy_pass http://app_servers;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

#### Database Replication (Read Replicas)

```php
// config/database.php
'pgsql' => [
    'read' => [
        'host' => [
            env('DB_READ_HOST_1', 'db-read-1'),
            env('DB_READ_HOST_2', 'db-read-2'),
        ],
    ],
    'write' => [
        'host' => env('DB_WRITE_HOST', 'db-master'),
    ],
],
```

### 10.11 Resumo de Métricas de Performance

| Métrica | Target | Como Medir |
|---------|--------|------------|
| **Response Time (p95)** | < 200ms | APM / Logs |
| **Database Query Time** | < 50ms | Query Watcher |
| **Cache Hit Rate** | > 80% | Redis INFO |
| **API Throughput** | > 1000 req/s | Load Testing |
| **Database Connections** | < 80% pool | PgBouncer stats |
| **Memory Usage** | < 512MB/worker | PHP-FPM status |
| **Queue Processing** | < 5s delay | Queue Monitor |

### 10.12 Checklist de Implementação

- [ ] Implementar cache multi-camada (Redis)
- [ ] Adicionar índices compostos otimizados
- [ ] Criar materialized views para relatórios
- [ ] Implementar CQRS com Read Models
- [ ] Configurar PgBouncer para connection pooling
- [ ] Implementar paginação com cursor
- [ ] Adicionar ETags e compression
- [ ] Mover cálculos pesados para background jobs
- [ ] Configurar CDN para assets estáticos
- [ ] Implementar APM e logging estruturado
- [ ] Configurar load balancer (quando necessário)
- [ ] Setup database replication (quando necessário)

---

## 11. Segurança e Autenticação

### 11.1 Estratégia de Autenticação

#### Laravel Sanctum com Tokens

```php
// config/sanctum.php
'expiration' => 60 * 24, // 24 horas
'token_prefix' => 'finance_',
'middleware' => [
    'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
    'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
],
```

#### Rate Limiting por Endpoint

```php
// routes/api.php
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::put('/transactions/{id}', [TransactionController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'throttle:120,1'])->group(function () {
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/accounts', [AccountController::class, 'index']);
});
```

#### Políticas de Senha

```php
// app/Infrastructure/Validation/Rules/StrongPassword.php
final class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strlen($value) < 8) {
            $fail('A senha deve ter no mínimo 8 caracteres.');
        }

        if (!preg_match('/[A-Z]/', $value)) {
            $fail('A senha deve conter pelo menos uma letra maiúscula.');
        }

        if (!preg_match('/[a-z]/', $value)) {
            $fail('A senha deve conter pelo menos uma letra minúscula.');
        }

        if (!preg_match('/[0-9]/', $value)) {
            $fail('A senha deve conter pelo menos um número.');
        }

        if (!preg_match('/[^A-Za-z0-9]/', $value)) {
            $fail('A senha deve conter pelo menos um caractere especial.');
        }
    }
}
```

### 11.2 Autorização e Permissões

#### Policy para Transações

```php
// app/Infrastructure/Policies/TransactionPolicy.php
final class TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->organization_id !== null;
    }

    public function view(User $user, Transaction $transaction): bool
    {
        return $user->organization_id === $transaction->account->organization_id;
    }

    public function create(User $user): bool
    {
        return $user->organization_id !== null;
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $user->organization_id === $transaction->account->organization_id
            && !$transaction->is_confirmed;
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->organization_id === $transaction->account->organization_id
            && !$transaction->is_confirmed;
    }
}
```

### 11.3 Proteção contra Ataques

#### CSRF Protection

```php
// app/Http/Middleware/VerifyCsrfToken.php
protected $except = [
    'api/*', // APIs geralmente não precisam de CSRF
];
```

#### XSS Protection

```php
// app/Http/Middleware/XssProtection.php
final class XssProtection
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        return $response;
    }
}
```

### 11.4 Autenticação de Dois Fatores (2FA)

#### Implementação com Laravel Fortify

```php
// config/fortify.php
'features' => [
    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]),
],

// Middleware para rotas sensíveis
Route::middleware(['auth:sanctum', '2fa'])->group(function () {
    Route::post('/accounts', [AccountController::class, 'store']);
    Route::delete('/accounts/{id}', [AccountController::class, 'destroy']);
});
```

### 11.5 Logs de Auditoria de Segurança

```php
// app/Infrastructure/Logging/SecurityLogger.php
final class SecurityLogger
{
    public function logLoginAttempt(string $email, bool $success, ?string $ip = null): void
    {
        Log::channel('security')->info('Login attempt', [
            'email' => $email,
            'success' => $success,
            'ip' => $ip ?? request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function logSensitiveAction(string $action, User $user, array $context = []): void
    {
        Log::channel('security')->warning('Sensitive action', [
            'action' => $action,
            'user_id' => $user->id,
            'organization_id' => $user->organization_id,
            'ip' => request()->ip(),
            'context' => $context,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
```

### 11.6 OAuth2 para Integrações

```php
// config/passport.php
'clients' => [
    'personal_access_client' => [
        'client_id' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_ID'),
        'client_secret' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET'),
    ],
],

// Rotas para OAuth
Route::prefix('oauth')->group(function () {
    Route::post('/token', [OAuthController::class, 'issueToken']);
    Route::get('/authorize', [OAuthController::class, 'authorize']);
});
```

---

## 12. Testes e Qualidade

### 12.1 Estratégia de Testes

#### Estrutura de Testes

```
tests/
├── Unit/
│   ├── Domain/
│   │   ├── ValueObjects/
│   │   │   ├── MoneyTest.php
│   │   │   └── DocumentTest.php
│   │   └── Entities/
│   │       ├── TransactionTest.php
│   │       └── GoalTest.php
│   └── Application/
│       └── Commands/
├── Integration/
│   ├── Repositories/
│   │   └── TransactionRepositoryTest.php
│   ├── Services/
│   │   └── GlobalBalanceCalculatorTest.php
│   └── Events/
├── Feature/
│   ├── Api/
│   │   ├── TransactionApiTest.php
│   │   ├── AccountApiTest.php
│   │   └── AuthApiTest.php
│   └── E2E/
│       └── TransactionFlowTest.php
└── Performance/
    └── LoadTest.php
```

### 12.2 Testes Unitários

#### Exemplo: Money Value Object

```php
// tests/Unit/Domain/ValueObjects/MoneyTest.php
use App\Domain\Account\ValueObjects\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function test_can_create_money_from_cents(): void
    {
        $money = Money::fromCents(10000);
        
        $this->assertEquals(10000, $money->cents());
        $this->assertEquals(100.00, $money->toDecimal());
    }

    public function test_can_add_money(): void
    {
        $money1 = Money::fromCents(10000);
        $money2 = Money::fromCents(5000);
        
        $result = $money1->add($money2);
        
        $this->assertEquals(15000, $result->cents());
    }

    public function test_cannot_add_different_currencies(): void
    {
        $this->expectException(InvalidArgumentException::class);
        
        $money1 = Money::fromCents(10000, 'BRL');
        $money2 = Money::fromCents(5000, 'USD');
        
        $money1->add($money2);
    }
}
```

### 12.3 Testes de Integração

#### Exemplo: Transaction Repository

```php
// tests/Integration/Repositories/TransactionRepositoryTest.php
use Tests\TestCase;
use App\Domain\Transaction\Entities\Transaction;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentTransactionRepository;

final class TransactionRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_save_and_retrieve_transaction(): void
    {
        $repository = app(TransactionRepositoryInterface::class);
        $transaction = $this->createTransaction();
        
        $repository->save($transaction);
        
        $retrieved = $repository->findById($transaction->id());
        
        $this->assertNotNull($retrieved);
        $this->assertEquals($transaction->id()->toString(), $retrieved->id()->toString());
    }
}
```

### 12.4 Testes de API

#### Exemplo: Transaction API

```php
// tests/Feature/Api/TransactionApiTest.php
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

final class TransactionApiTest extends TestCase
{
    public function test_can_create_transaction(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        $response = $this->postJson('/api/v1/transactions', [
            'account_id' => $user->organization->accounts->first()->id,
            'category_id' => $user->organization->categories->first()->id,
            'type' => 'expense',
            'amount' => 50.00,
            'description' => 'Test transaction',
            'transaction_date' => now()->format('Y-m-d'),
        ]);
        
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'amount',
                    'description',
                ],
            ]);
    }

    public function test_cannot_create_transaction_without_authentication(): void
    {
        $response = $this->postJson('/api/v1/transactions', []);
        
        $response->assertStatus(401);
    }
}
```

### 12.5 Testes de Performance

```php
// tests/Performance/LoadTest.php
use Tests\TestCase;

final class LoadTest extends TestCase
{
    public function test_api_can_handle_concurrent_requests(): void
    {
        $user = User::factory()->create();
        
        $responses = [];
        for ($i = 0; $i < 100; $i++) {
            $responses[] = $this->actingAs($user)
                ->getJson('/api/v1/transactions');
        }
        
        foreach ($responses as $response) {
            $response->assertStatus(200);
        }
    }
}
```

### 12.6 Cobertura de Testes

```yaml
# .github/workflows/ci.yml - Adicionar ao job de testes
- name: Generate coverage report
  run: php artisan test --coverage --min=80

- name: Upload coverage to Codecov
  uses: codecov/codecov-action@v3
  with:
    files: ./coverage.xml
```

---

## 13. Tratamento de Erros e Resiliência

### 13.1 Estratégia de Tratamento de Erros

#### Exception Handler Customizado

```php
// app/Exceptions/Handler.php
public function register(): void
{
    $this->renderable(function (DomainException $e, Request $request) {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => 'DOMAIN_ERROR',
                ],
            ], 422);
        }
    });

    $this->renderable(function (ValidationException $e, Request $request) {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => [
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ],
            ], 422);
        }
    });
}
```

### 13.2 Retry Policies para Jobs

```php
// app/Infrastructure/Jobs/CalculateGlobalBalanceJob.php
final class CalculateGlobalBalanceJob implements ShouldQueue
{
    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min
    public $timeout = 300; // 5 minutos
    
    public function handle(GlobalBalanceCalculator $calculator): void
    {
        try {
            $calculator->calculate(
                Uuid::fromString($this->organizationId),
                $this->untilDate ? new DateTimeImmutable($this->untilDate) : null
            );
        } catch (Exception $e) {
            Log::error('Balance calculation failed', [
                'organization_id' => $this->organizationId,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);
            
            throw $e; // Re-throw para retry
        }
    }
    
    public function failed(Throwable $exception): void
    {
        // Dead letter queue
        Log::critical('Balance calculation permanently failed', [
            'organization_id' => $this->organizationId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
        
        // Notificar administradores
        // Notification::route('slack', config('logging.slack'))
        //     ->notify(new BalanceCalculationFailed($this->organizationId));
    }
}
```

### 13.3 Circuit Breaker para Serviços Externos

```php
// app/Infrastructure/Resilience/CircuitBreaker.php
final class CircuitBreaker
{
    private const FAILURE_THRESHOLD = 5;
    private const TIMEOUT = 60; // segundos
    
    public function __construct(
        private readonly Cache $cache,
    ) {}
    
    public function call(string $service, callable $operation): mixed
    {
        if ($this->isOpen($service)) {
            throw new CircuitBreakerOpenException("Circuit breaker is open for {$service}");
        }
        
        try {
            $result = $operation();
            $this->recordSuccess($service);
            return $result;
        } catch (Exception $e) {
            $this->recordFailure($service);
            throw $e;
        }
    }
    
    private function isOpen(string $service): bool
    {
        $failures = $this->cache->get("circuit_breaker:{$service}:failures", 0);
        $lastFailure = $this->cache->get("circuit_breaker:{$service}:last_failure");
        
        if ($failures >= self::FAILURE_THRESHOLD) {
            if ($lastFailure && now()->diffInSeconds($lastFailure) < self::TIMEOUT) {
                return true;
            }
            // Reset após timeout
            $this->cache->forget("circuit_breaker:{$service}:failures");
        }
        
        return false;
    }
}
```

### 13.4 Health Checks

```php
// app/Http/Controllers/HealthController.php
final class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
            'queue' => $this->checkQueue(),
        ];
        
        $healthy = !in_array(false, $checks);
        
        return response()->json([
            'status' => $healthy ? 'healthy' : 'unhealthy',
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
        ], $healthy ? 200 : 503);
    }
    
    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    private function checkRedis(): bool
    {
        try {
            Redis::ping();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    private function checkQueue(): bool
    {
        try {
            Queue::size('default');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
```

---

## 14. Observabilidade e Monitoramento

### 14.1 Logging Estruturado

#### Configuração de Canais

```php
// config/logging.php
'channels' => [
    'application' => [
        'driver' => 'stack',
        'channels' => ['daily', 'slack'],
        'ignore_exceptions' => false,
    ],
    
    'security' => [
        'driver' => 'daily',
        'path' => storage_path('logs/security.log'),
        'level' => 'info',
        'days' => 90, // Manter por 90 dias
    ],
    
    'performance' => [
        'driver' => 'daily',
        'path' => storage_path('logs/performance.log'),
        'level' => 'debug',
    ],
],
```

#### Logger Estruturado

```php
// app/Infrastructure/Logging/StructuredLogger.php
final class StructuredLogger
{
    public function logTransactionCreated(Transaction $transaction, User $user): void
    {
        Log::channel('application')->info('Transaction created', [
            'event' => 'transaction.created',
            'transaction_id' => $transaction->id()->toString(),
            'account_id' => $transaction->accountId()->toString(),
            'amount' => $transaction->amount()->cents(),
            'type' => $transaction->type()->value,
            'user_id' => $user->id,
            'organization_id' => $user->organization_id,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
    
    public function logSlowQuery(string $query, float $time, array $bindings = []): void
    {
        if ($time > 1.0) {
            Log::channel('performance')->warning('Slow query detected', [
                'query' => $query,
                'time' => $time,
                'bindings' => $bindings,
                'context' => 'database',
                'timestamp' => now()->toIso8601String(),
            ]);
        }
    }
}
```

### 14.2 Distributed Tracing (OpenTelemetry)

```php
// config/opentelemetry.php
return [
    'service_name' => 'finance-api',
    'service_version' => env('APP_VERSION', '1.0.0'),
    'exporter' => [
        'type' => 'otlp',
        'endpoint' => env('OTEL_EXPORTER_OTLP_ENDPOINT'),
    ],
];

// Middleware para tracing
final class TracingMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $span = Tracer::spanBuilder('http.request')
            ->setAttribute('http.method', $request->method())
            ->setAttribute('http.url', $request->url())
            ->startSpan();
        
        try {
            $response = $next($request);
            $span->setAttribute('http.status_code', $response->status());
            return $response;
        } finally {
            $span->end();
        }
    }
}
```

### 14.3 Métricas e Dashboards

#### Métricas Customizadas

```php
// app/Infrastructure/Metrics/MetricsCollector.php
final class MetricsCollector
{
    public function incrementTransactionCreated(): void
    {
        // Usar Prometheus ou StatsD
        // Prometheus::counter('transactions_created_total')->inc();
    }
    
    public function recordResponseTime(string $endpoint, float $time): void
    {
        // Prometheus::histogram('http_request_duration_seconds')
        //     ->observe($time, ['endpoint' => $endpoint]);
    }
}
```

### 14.4 Error Tracking (Sentry)

```php
// config/sentry.php
'dsn' => env('SENTRY_LARAVEL_DSN'),
'environment' => env('APP_ENV'),
'release' => env('APP_VERSION'),
'traces_sample_rate' => env('SENTRY_TRACES_SAMPLE_RATE', 0.1),

// Context adicional
Sentry\configureScope(function (Sentry\State\Scope $scope): void {
    $scope->setUser([
        'id' => auth()->id(),
        'email' => auth()->user()?->email,
    ]);
});
```

---

## 15. Backup e Disaster Recovery

### 15.1 Estratégia de Backup

#### Backup Automatizado do PostgreSQL

```yaml
# docker-compose.yml
services:
  backup:
    image: postgres:16-alpine
    container_name: finance_backup
    restart: unless-stopped
    environment:
      PGPASSWORD: ${DB_PASSWORD}
    volumes:
      - ./backups:/backups
      - ./scripts/backup.sh:/backup.sh
    command: >
      sh -c "
        chmod +x /backup.sh &&
        while true; do
          /backup.sh
          sleep 86400
        done
      "
    networks:
      - finance_network
    depends_on:
      - db
```

#### Script de Backup

```bash
#!/bin/bash
# scripts/backup.sh

BACKUP_DIR="/backups"
TIMESTAMP=$(date +%Y%m%d-%H%M%S)
BACKUP_FILE="${BACKUP_DIR}/backup-${TIMESTAMP}.sql"

# Backup do banco
pg_dump -h db -U ${DB_USERNAME} ${DB_DATABASE} > ${BACKUP_FILE}

# Comprimir
gzip ${BACKUP_FILE}

# Remover backups antigos (manter últimos 30 dias)
find ${BACKUP_DIR} -name "*.sql.gz" -mtime +30 -delete

# Upload para S3 (opcional)
# aws s3 cp ${BACKUP_FILE}.gz s3://finance-backups/
```

### 15.2 RTO e RPO

| Componente | RTO (Recovery Time Objective) | RPO (Recovery Point Objective) |
|------------|-------------------------------|--------------------------------|
| **Database** | 4 horas | 1 hora |
| **Application** | 1 hora | 15 minutos |
| **Files/Assets** | 2 horas | 24 horas |

### 15.3 Disaster Recovery Plan

```php
// app/Console/Commands/RestoreBackup.php
final class RestoreBackup extends Command
{
    protected $signature = 'backup:restore {file}';
    
    public function handle(): int
    {
        $file = $this->argument('file');
        
        if (!file_exists($file)) {
            $this->error("Backup file not found: {$file}");
            return Command::FAILURE;
        }
        
        $this->info("Restoring backup from {$file}...");
        
        // Descomprimir se necessário
        if (str_ends_with($file, '.gz')) {
            $this->info("Decompressing backup...");
            exec("gunzip -c {$file} > /tmp/restore.sql");
            $file = '/tmp/restore.sql';
        }
        
        // Restaurar
        $this->info("Restoring database...");
        exec("psql -h {$this->getDbHost()} -U {$this->getDbUser()} {$this->getDbName()} < {$file}");
        
        $this->info("Backup restored successfully!");
        
        return Command::SUCCESS;
    }
}
```

---

## 16. Documentação da API

### 16.1 OpenAPI/Swagger Specification

#### Configuração com L5-Swagger

```php
// config/l5-swagger.php
'defaults' => [
    'routes' => [
        'api' => 'api/documentation',
    ],
    'paths' => [
        'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', false),
        'docs' => storage_path('api-docs'),
        'views' => resource_path('views/vendor/l5-swagger'),
    ],
],
```

#### Exemplo de Documentação

```php
/**
 * @OA\Post(
 *     path="/api/v1/transactions",
 *     summary="Criar nova transação",
 *     tags={"Transactions"},
 *     security={{"sanctum": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"account_id", "category_id", "type", "amount", "description"},
 *             @OA\Property(property="account_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
 *             @OA\Property(property="category_id", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440001"),
 *             @OA\Property(property="type", type="string", enum={"income", "expense"}, example="expense"),
 *             @OA\Property(property="amount", type="number", format="float", example=50.00),
 *             @OA\Property(property="description", type="string", example="Almoço"),
 *             @OA\Property(property="transaction_date", type="string", format="date", example="2025-01-15"),
 *             @OA\Property(property="is_confirmed", type="boolean", example=true),
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Transação criada com sucesso",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="object"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Erro de validação",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="object"),
 *         )
 *     ),
 *     @OA\Response(response=401, description="Não autenticado"),
 * )
 */
public function store(CreateTransactionRequest $request): JsonResponse
{
    // ...
}
```

### 16.2 Changelog da API

```markdown
# API Changelog

## [1.1.0] - 2025-02-01

### Added
- Endpoint `POST /api/v1/transactions/import` para importação em lote
- Filtro `competence_date` no endpoint de listagem de transações

### Changed
- Endpoint `GET /api/v1/transactions` agora retorna paginação com cursor por padrão

### Deprecated
- Endpoint `GET /api/v1/transactions?page=X` será removido na versão 2.0.0

## [1.0.0] - 2025-01-01

### Added
- Versão inicial da API
```

---

## 17. Multi-tenancy e Isolamento

### 17.1 Row-Level Security (PostgreSQL)

```php
// Migration: enable_row_level_security.php
DB::statement('
    ALTER TABLE transactions ENABLE ROW LEVEL SECURITY;
    
    CREATE POLICY tenant_isolation_transactions ON transactions
        FOR ALL
        USING (
            account_id IN (
                SELECT id FROM accounts 
                WHERE organization_id = current_setting(\'app.current_organization_id\')::uuid
            )
        );
');

DB::statement('
    ALTER TABLE accounts ENABLE ROW LEVEL SECURITY;
    
    CREATE POLICY tenant_isolation_accounts ON accounts
        FOR ALL
        USING (organization_id = current_setting(\'app.current_organization_id\')::uuid);
');
```

### 17.2 Tenant Middleware Aprimorado

```php
// app/Http/Middleware/TenantMiddleware.php
final class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user || !$user->organization_id) {
            abort(403, 'Organization not found');
        }
        
        // Definir organização atual para RLS
        DB::statement("SET app.current_organization_id = '{$user->organization_id}'");
        
        // Adicionar ao contexto do cache
        Cache::tags(["org:{$user->organization_id}"]);
        
        $response = $next($request);
        
        // Limpar após request
        DB::statement('RESET app.current_organization_id');
        
        return $response;
    }
}
```

### 17.3 Isolamento de Cache por Tenant

```php
// app/Infrastructure/Cache/TenantAwareCache.php
final class TenantAwareCache
{
    public function remember(string $key, int $ttl, callable $callback, ?string $organizationId = null): mixed
    {
        $organizationId ??= auth()->user()?->organization_id;
        $tenantKey = "org:{$organizationId}:{$key}";
        
        return Cache::tags(["org:{$organizationId}"])->remember($tenantKey, $ttl, $callback);
    }
    
    public function forget(string $key, ?string $organizationId = null): void
    {
        $organizationId ??= auth()->user()?->organization_id;
        Cache::tags(["org:{$organizationId}"])->flush();
    }
}
```

---

## 18. Compliance e LGPD

### 18.1 Estratégia de Privacidade

#### Consentimento e Termos

```php
// Migration: add_consent_fields.php
Schema::table('users', function (Blueprint $table) {
    $table->boolean('terms_accepted')->default(false);
    $table->timestamp('terms_accepted_at')->nullable();
    $table->boolean('privacy_policy_accepted')->default(false);
    $table->timestamp('privacy_policy_accepted_at')->nullable();
    $table->json('consent_preferences')->nullable();
});
```

### 18.2 Direito ao Esquecimento

```php
// app/Application/Identity/Commands/DeleteUserData/DeleteUserDataHandler.php
final class DeleteUserDataHandler
{
    public function handle(DeleteUserDataCommand $command): void
    {
        $user = $this->userRepository->findById(Uuid::fromString($command->userId));
        
        if (!$user) {
            throw new DomainException('User not found');
        }
        
        DB::transaction(function () use ($user) {
            // Anonimizar dados pessoais
            $user->update([
                'email' => "deleted_{$user->id}@deleted.local",
                'name' => 'Usuário Removido',
                'document' => null,
            ]);
            
            // Manter transações para histórico financeiro, mas sem vínculo pessoal
            // Soft delete da organização
            $user->organization->delete();
            
            // Log da ação
            Log::channel('security')->info('User data deleted', [
                'user_id' => $user->id,
                'requested_by' => auth()->id(),
                'timestamp' => now()->toIso8601String(),
            ]);
        });
    }
}
```

### 18.3 Exportação de Dados (LGPD)

```php
// app/Console/Commands/ExportUserData.php
final class ExportUserData extends Command
{
    protected $signature = 'user:export-data {user_id}';
    
    public function handle(): int
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User not found");
            return Command::FAILURE;
        }
        
        $data = [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
            ],
            'organization' => [
                'name' => $user->organization->name,
                'type' => $user->organization->type,
            ],
            'accounts' => $user->organization->accounts->map(fn ($a) => [
                'name' => $a->name,
                'type' => $a->accountType->name,
                'created_at' => $a->created_at,
            ]),
            'transactions' => $user->organization->transactions->map(fn ($t) => [
                'amount' => $t->amount,
                'description' => $t->description,
                'date' => $t->transaction_date,
                'type' => $t->type,
            ]),
            'goals' => $user->organization->goals->map(fn ($g) => [
                'name' => $g->name,
                'target_amount' => $g->target_amount,
                'current_amount' => $g->current_amount,
                'status' => $g->status,
            ]),
        ];
        
        $filename = "exports/user-{$userId}-" . now()->format('Y-m-d-His') . '.json';
        Storage::put($filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->info("Data exported to: {$filename}");
        
        return Command::SUCCESS;
    }
}
```

### 18.4 Logs de Acesso a Dados Pessoais

```php
// app/Infrastructure/Logging/PrivacyLogger.php
final class PrivacyLogger
{
    public function logDataAccess(string $action, User $user, array $dataAccessed): void
    {
        Log::channel('privacy')->info('Personal data accessed', [
            'action' => $action,
            'user_id' => $user->id,
            'accessed_by' => auth()->id(),
            'data_types' => $dataAccessed,
            'ip' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
```

---

## 19. Deploy e Infraestrutura Avançada

### 19.1 Estratégia de Rollback

```yaml
# .github/workflows/deploy.yml - Adicionar rollback
- name: Deploy to Production
  uses: appleboy/ssh-action@v1
  with:
    host: ${{ secrets.PROD_HOST }}
    username: ${{ secrets.PROD_USER }}
    key: ${{ secrets.PROD_SSH_KEY }}
    script: |
      cd /opt/finance
      
      # Backup antes do deploy
      docker exec finance_db pg_dump -U finance finance > /backups/pre-deploy-$(date +%Y%m%d-%H%M%S).sql
      
      # Tag atual para rollback
      CURRENT_TAG=$(docker images finance-api --format "{{.Tag}}" | head -1)
      echo "CURRENT_TAG=$CURRENT_TAG" > /opt/finance/.current_tag
      
      # Deploy
      docker compose pull
      docker compose up -d --remove-orphans
      
      # Health check
      sleep 10
      if ! curl -f http://localhost/health; then
        echo "Deploy failed, rolling back..."
        docker compose down
        docker tag finance-api:$CURRENT_TAG finance-api:latest
        docker compose up -d
        exit 1
      fi
```

### 19.2 Blue-Green Deployment

```yaml
# docker-compose.blue-green.yml
services:
  app-blue:
    image: finance-api:blue
    # ...
  
  app-green:
    image: finance-api:green
    # ...
  
  nginx:
    # Alternar entre blue e green via variável de ambiente
    environment:
      APP_COLOR: ${DEPLOY_COLOR:-blue}
```

### 19.3 Health Checks Detalhados

```php
// app/Http/Controllers/HealthController.php - Expandido
public function detailed(): JsonResponse
{
    $checks = [
        'application' => [
            'status' => 'ok',
            'version' => config('app.version'),
            'environment' => config('app.env'),
        ],
        'database' => $this->checkDatabaseDetailed(),
        'redis' => $this->checkRedisDetailed(),
        'queue' => $this->checkQueueDetailed(),
        'storage' => $this->checkStorage(),
        'cache' => $this->checkCache(),
    ];
    
    $allHealthy = collect($checks)->every(fn ($check) => 
        is_array($check) ? ($check['status'] ?? 'unknown') === 'ok' : $check === true
    );
    
    return response()->json([
        'status' => $allHealthy ? 'healthy' : 'degraded',
        'checks' => $checks,
        'timestamp' => now()->toIso8601String(),
    ], $allHealthy ? 200 : 503);
}
```

---

## 20. Sugestões Adicionais

| Recurso | Descrição | Prioridade | Complexidade |
|---------|-----------|------------|--------------|
| **PWA** | App instalável para entrada rápida no mobile | Alta | Média |
| **Importação OFX/CSV** | Integração com extratos bancários | **Alta** | Média |
| **Notificações Push** | Alertas de vencimento, metas atingidas | Média | Baixa |
| **Multi-moeda** | Suporte a USD, EUR com conversão automática | Baixa | Alta |
| **API de Bancos** | Open Banking para sync automático | Futura | Alta |
| **Relatórios Avançados** | Gráficos interativos, previsões | Média | Média |
| **Compartilhamento** | Dividir contas em família/empresa | Média | Média |
| **Auditoria** | Log de todas alterações (Event Sourcing light) | Alta para PJ | Alta |
| **Exportação PDF** | Relatórios em PDF | Baixa | Baixa |
| **Anexos** | Upload de comprovantes | Média | Baixa |
| **Tags Customizáveis** | Etiquetas personalizadas | Baixa | Baixa |
| **Orçamento Mensal** | Limites por categoria | Alta | Média |

---

## 21. Próximos Passos

### Fase 1: Setup Inicial (Semanas 1-2)

- [ ] Criar projeto Laravel
- [ ] Configurar Docker (app, nginx, postgres, redis)
- [ ] Estruturar pastas DDD/Clean Architecture
- [ ] Configurar PHPStan, Pint, Pest
- [ ] Setup CI/CD básico
- [ ] Configurar Laravel Pennant (feature flags)
- [ ] Configurar logging estruturado
- [ ] Setup de health checks básicos
- [ ] Configurar backup automatizado

### Fase 2: Domain Identity e Account (Semanas 3-4)

- [ ] Implementar Value Objects (Document, Money, Email)
- [ ] Implementar entidades User, Organization, Account
- [ ] Criar migrations e seeders
- [ ] Implementar autenticação (Sanctum)
- [ ] Configurar rate limiting
- [ ] Implementar políticas de senha
- [ ] Criar endpoints de conta
- [ ] Testes unitários do domínio
- [ ] Configurar Row-Level Security (RLS)

### Fase 3: Domain Transaction (Semanas 5-6)

- [ ] Implementar entidade Transaction
- [ ] Implementar Category com hierarquia
- [ ] Criar comando de entrada rápida
- [ ] Implementar confirmação de transação
- [ ] Criar categorias padrão (seeder)
- [ ] Testes de integração
- [ ] Testes de API (Feature tests)
- [ ] Implementar tratamento de erros

### Fase 4: Domain Planning (Semanas 7-8)

- [ ] Implementar entidade Goal
- [ ] Criar sistema de contribuições
- [ ] Implementar cálculo de progresso
- [ ] Notificações de metas
- [ ] Testes E2E
- [ ] Implementar retry policies para jobs

### Fase 5: Frontend + Integração (Semanas 9-10)

- [ ] Setup projeto frontend (React/Vue)
- [ ] Implementar autenticação
- [ ] Dashboard principal
- [ ] Formulário de entrada rápida
- [ ] Listagem de transações
- [ ] Gestão de metas
- [ ] Integração com OpenAPI docs

### Fase 6: Segurança e Compliance (Semanas 11-12)

- [ ] Implementar 2FA/MFA
- [ ] Configurar OAuth2 para integrações
- [ ] Implementar compliance LGPD
- [ ] Criar endpoints de exportação de dados
- [ ] Implementar direito ao esquecimento
- [ ] Configurar logs de auditoria
- [ ] Testes de segurança (OWASP)

### Fase 7: Observabilidade e Performance (Semanas 13-14)

- [ ] Configurar OpenTelemetry/Distributed Tracing
- [ ] Implementar métricas e dashboards
- [ ] Configurar Sentry para error tracking
- [ ] Otimização de performance
- [ ] Cache de saldo global
- [ ] Implementar CQRS com Read Models
- [ ] Configurar PgBouncer

### Fase 8: Deploy e Infraestrutura (Semanas 15-16)

- [ ] Testes E2E completos
- [ ] Documentação API (OpenAPI/Swagger)
- [ ] Configurar estratégia de rollback
- [ ] Setup blue-green deployment
- [ ] Configurar health checks detalhados
- [ ] Deploy em produção
- [ ] Monitoramento completo (logs, métricas, alertas)
- [ ] Testes de disaster recovery

---

## Referências

- [Laravel Documentation](https://laravel.com/docs)
- [Domain-Driven Design Reference](https://www.domainlanguage.com/ddd/reference/)
- [Clean Architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
- [Laravel Pennant](https://laravel.com/docs/pennant)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)

---

*Documento gerado em: Janeiro/2026*
*Versão: 1.0*
