<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\AccountType;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('should create loan for another user', function () {
    $organization = Organization::create([
        'id' => Uuid::uuid4()->toString(),
        'name' => 'Test Org',
        'document' => '00000000191',
        'type' => 'PF',
        'plan' => 'free',
    ]);

    $user = User::create([
        'id' => Uuid::uuid4()->toString(),
        'name' => 'Lender',
        'email' => 'lender@example.com',
        'password' => Hash::make('password'),
        'organization_id' => $organization->id,
    ]);

    $borrower = User::create([
        'id' => Uuid::uuid4()->toString(),
        'name' => 'Borrower',
        'email' => 'borrower@example.com',
        'password' => Hash::make('password'),
        'organization_id' => $organization->id,
    ]);

    $loanAccountType = AccountType::where('slug', 'loan')->first();
    $account = Account::create([
        'id' => Uuid::uuid4()->toString(),
        'organization_id' => $organization->id,
        'account_type_id' => $loanAccountType->id,
        'name' => 'Conta Principal',
        'initial_balance' => 0,
        'is_active' => true,
    ]);

    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson("/api/accounts/{$account->id}/lend", [
            'borrower_id' => $borrower->id,
            'amount' => 1000.00,
            'interest_rate' => 2.5,
            'loan_due_date' => now()->addYear()->format('Y-m-d'),
        ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_loan',
                'borrower',
                'interest_rate',
                'loan_due_date',
            ],
        ]);

    expect($response->json('data.is_loan'))->toBeTrue()
        ->and($response->json('data.borrower.id'))->toBe($borrower->id);
});

test('should list loans for an account', function () {
    $organization = Organization::create([
        'id' => Uuid::uuid4()->toString(),
        'name' => 'Test Org',
        'document' => '00000000191',
        'type' => 'PF',
        'plan' => 'free',
    ]);

    $user = User::create([
        'id' => Uuid::uuid4()->toString(),
        'name' => 'User',
        'email' => 'user@example.com',
        'password' => Hash::make('password'),
        'organization_id' => $organization->id,
    ]);

    $loanAccountType = AccountType::where('slug', 'loan')->first();
    $account = Account::create([
        'id' => Uuid::uuid4()->toString(),
        'organization_id' => $organization->id,
        'account_type_id' => $loanAccountType->id,
        'name' => 'Conta Principal',
        'initial_balance' => 0,
        'is_active' => true,
    ]);

    // Criar empréstimo
    Account::create([
        'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
        'organization_id' => $organization->id,
        'account_type_id' => $loanAccountType->id,
        'name' => 'Empréstimo 1',
        'initial_balance' => 100000, // 1000.00 em centavos
        'borrower_id' => $account->id,
        'interest_rate' => 2.5,
        'loan_due_date' => now()->addYear(),
        'is_active' => true,
    ]);

    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson("/api/accounts/{$account->id}/loans");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'is_loan',
                ],
            ],
        ]);

    expect($response->json('data'))->toHaveCount(1);
});

test('should register loan repayment', function () {
    $organization = Organization::create([
        'id' => Uuid::uuid4()->toString(),
        'name' => 'Test Org',
        'document' => '00000000191',
        'type' => 'PF',
        'plan' => 'free',
    ]);

    $user = User::create([
        'id' => Uuid::uuid4()->toString(),
        'name' => 'User',
        'email' => 'user@example.com',
        'password' => Hash::make('password'),
        'organization_id' => $organization->id,
    ]);

    $borrower = User::create([
        'id' => Uuid::uuid4()->toString(),
        'name' => 'Borrower',
        'email' => 'borrower@example.com',
        'password' => Hash::make('password'),
        'organization_id' => $organization->id,
    ]);

    $loanAccountType = AccountType::where('slug', 'loan')->first();
    $loanAccount = Account::create([
        'id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
        'organization_id' => $organization->id,
        'account_type_id' => $loanAccountType->id,
        'name' => 'Empréstimo',
        'initial_balance' => 100000, // 1000.00 em centavos
        'borrower_id' => $borrower->id,
        'interest_rate' => 2.5,
        'loan_due_date' => now()->addYear(),
        'is_active' => true,
    ]);

    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson("/api/accounts/{$loanAccount->id}/repay", [
            'amount' => 250.00,
        ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'remaining_balance',
        ]);

    expect($response->json('remaining_balance'))->toBe(750.00);

    $loanAccount->refresh();
    expect($loanAccount->initial_balance)->toBe(75000); // 750.00 em centavos
});

test('should reject loan creation for non-loan account type', function () {
    $organization = Organization::create([
        'id' => Uuid::uuid4()->toString(),
        'name' => 'Test Org',
        'document' => '00000000191',
        'type' => 'PF',
        'plan' => 'free',
    ]);

    $user = User::create([
        'id' => Uuid::uuid4()->toString(),
        'name' => 'User',
        'email' => 'user@example.com',
        'password' => Hash::make('password'),
        'organization_id' => $organization->id,
    ]);

    $borrower = User::create([
        'id' => Uuid::uuid4()->toString(),
        'name' => 'Borrower',
        'email' => 'borrower@example.com',
        'password' => Hash::make('password'),
        'organization_id' => $organization->id,
    ]);

    $checkingAccountType = AccountType::where('slug', 'checking')->first();
    $account = Account::create([
        'id' => Uuid::uuid4()->toString(),
        'organization_id' => $organization->id,
        'account_type_id' => $checkingAccountType->id,
        'name' => 'Conta Corrente',
        'initial_balance' => 0,
        'is_active' => true,
    ]);

    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson("/api/accounts/{$account->id}/lend", [
            'borrower_id' => $borrower->id,
            'amount' => 1000.00,
            'interest_rate' => 2.5,
            'loan_due_date' => now()->addYear()->format('Y-m-d'),
        ]);

    $response->assertStatus(422)
        ->assertJson([
            'message' => 'This account type does not support loans',
        ]);
});
