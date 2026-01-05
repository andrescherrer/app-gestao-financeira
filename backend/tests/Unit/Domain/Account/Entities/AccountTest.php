<?php

declare(strict_types=1);

use App\Domain\Account\Entities\Account;
use App\Domain\Account\ValueObjects\Money;
use App\Domain\Shared\ValueObjects\Uuid;
use DateTimeImmutable;

test('should create account', function () {
    $organizationId = Uuid::generate();
    $accountTypeId = Uuid::generate();
    $initialBalance = Money::fromDecimal(1000.50);

    $account = Account::create(
        organizationId: $organizationId,
        accountTypeId: $accountTypeId,
        name: 'Conta Corrente',
        initialBalance: $initialBalance,
    );

    expect($account->organizationId())->toEqual($organizationId)
        ->and($account->accountTypeId())->toEqual($accountTypeId)
        ->and($account->name())->toBe('Conta Corrente')
        ->and($account->initialBalance())->toEqual($initialBalance)
        ->and($account->isActive())->toBeTrue()
        ->and($account->isLoan())->toBeFalse();
});

test('should create account with credit limit', function () {
    $organizationId = Uuid::generate();
    $accountTypeId = Uuid::generate();
    $initialBalance = Money::fromDecimal(0);
    $creditLimit = Money::fromDecimal(5000);

    $account = Account::create(
        organizationId: $organizationId,
        accountTypeId: $accountTypeId,
        name: 'Cartão de Crédito',
        initialBalance: $initialBalance,
        creditLimit: $creditLimit,
        closingDay: 10,
        dueDay: 15,
    );

    expect($account->creditLimit())->toEqual($creditLimit)
        ->and($account->closingDay())->toBe(10)
        ->and($account->dueDay())->toBe(15);
});

test('should create loan account', function () {
    $organizationId = Uuid::generate();
    $accountTypeId = Uuid::generate();
    $borrowerId = Uuid::generate();
    $initialBalance = Money::fromDecimal(10000);
    $loanDueDate = new DateTimeImmutable('+1 year');

    $account = Account::createLoan(
        organizationId: $organizationId,
        accountTypeId: $accountTypeId,
        name: 'Empréstimo João',
        initialBalance: $initialBalance,
        borrowerId: $borrowerId,
        interestRate: 2.5,
        loanDueDate: $loanDueDate,
    );

    expect($account->isLoan())->toBeTrue()
        ->and($account->borrowerId())->toEqual($borrowerId)
        ->and($account->interestRate())->toBe(2.5)
        ->and($account->loanDueDate())->toEqual($loanDueDate);
});

test('should update account name', function () {
    $organizationId = Uuid::generate();
    $accountTypeId = Uuid::generate();
    $account = Account::create($organizationId, $accountTypeId, 'Old Name', Money::zero());

    $account->updateName('New Name');

    expect($account->name())->toBe('New Name')
        ->and($account->updatedAt())->not->toBeNull();
});

test('should update initial balance', function () {
    $organizationId = Uuid::generate();
    $accountTypeId = Uuid::generate();
    $account = Account::create($organizationId, $accountTypeId, 'Account', Money::zero());

    $newBalance = Money::fromDecimal(500);
    $account->updateInitialBalance($newBalance);

    expect($account->initialBalance())->toEqual($newBalance)
        ->and($account->updatedAt())->not->toBeNull();
});

test('should activate and deactivate account', function () {
    $organizationId = Uuid::generate();
    $accountTypeId = Uuid::generate();
    $account = Account::create($organizationId, $accountTypeId, 'Account', Money::zero());

    $account->deactivate();
    expect($account->isActive())->toBeFalse();

    $account->activate();
    expect($account->isActive())->toBeTrue();
});

test('should update credit limit', function () {
    $organizationId = Uuid::generate();
    $accountTypeId = Uuid::generate();
    $account = Account::create($organizationId, $accountTypeId, 'Account', Money::zero());

    $creditLimit = Money::fromDecimal(3000);
    $account->updateCreditLimit($creditLimit);

    expect($account->creditLimit())->toEqual($creditLimit);
});

test('should delete account', function () {
    $organizationId = Uuid::generate();
    $accountTypeId = Uuid::generate();
    $account = Account::create($organizationId, $accountTypeId, 'Account', Money::zero());

    $account->delete();

    expect($account->isDeleted())->toBeTrue()
        ->and($account->deletedAt())->not->toBeNull();
});

test('should restore deleted account', function () {
    $organizationId = Uuid::generate();
    $accountTypeId = Uuid::generate();
    $account = Account::create($organizationId, $accountTypeId, 'Account', Money::zero());
    $account->delete();

    $account->restore();

    expect($account->isDeleted())->toBeFalse()
        ->and($account->deletedAt())->toBeNull();
});

