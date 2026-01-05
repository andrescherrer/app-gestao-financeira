<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\AccountType;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

uses(RefreshDatabase::class);

test('should isolate accounts by organization', function () {
    // Criar duas organizações
    $org1 = Organization::create([
        'id' => Uuid::uuid4()->toString(),
        'name' => 'Organization 1',
        'document' => '00000000191',
        'type' => 'PF',
        'plan' => 'free',
    ]);

    $org2 = Organization::create([
        'id' => Uuid::uuid4()->toString(),
        'name' => 'Organization 2',
        'document' => '00000000272',
        'type' => 'PF',
        'plan' => 'free',
    ]);

    // Criar usuários para cada organização
    $user1 = User::create([
        'id' => Uuid::uuid4()->toString(),
        'name' => 'User 1',
        'email' => 'user1@example.com',
        'password' => Hash::make('password'),
        'organization_id' => $org1->id,
    ]);

    $user2 = User::create([
        'id' => Uuid::uuid4()->toString(),
        'name' => 'User 2',
        'email' => 'user2@example.com',
        'password' => Hash::make('password'),
        'organization_id' => $org2->id,
    ]);

    // Criar contas para cada organização
    $accountType = AccountType::where('slug', 'checking')->first();
    $account1 = Account::create([
        'id' => Uuid::uuid4()->toString(),
        'organization_id' => $org1->id,
        'account_type_id' => $accountType->id,
        'name' => 'Account Org 1',
        'initial_balance' => 0,
        'is_active' => true,
    ]);

    $account2 = Account::create([
        'id' => Uuid::uuid4()->toString(),
        'organization_id' => $org2->id,
        'account_type_id' => $accountType->id,
        'name' => 'Account Org 2',
        'initial_balance' => 0,
        'is_active' => true,
    ]);

    $token1 = $user1->createToken('test-token')->plainTextToken;
    $token2 = $user2->createToken('test-token')->plainTextToken;

    // User 1 só deve ver sua própria conta
    $response1 = $this->withHeader('Authorization', "Bearer {$token1}")
        ->getJson('/api/accounts');

    $response1->assertStatus(200);
    $accounts1 = $response1->json('data');
    expect($accounts1)->toHaveCount(1)
        ->and($accounts1[0]['id'])->toBe($account1->id);

    // User 2 só deve ver sua própria conta
    $response2 = $this->withHeader('Authorization', "Bearer {$token2}")
        ->getJson('/api/accounts');

    $response2->assertStatus(200);
    $accounts2 = $response2->json('data');
    expect($accounts2)->toHaveCount(1)
        ->and($accounts2[0]['id'])->toBe($account2->id);

    // User 1 não deve conseguir acessar conta de User 2
    $response3 = $this->withHeader('Authorization', "Bearer {$token1}")
        ->getJson("/api/accounts/{$account2->id}");

    $response3->assertStatus(404);
});

test('should prevent access without organization', function () {
    $user = User::create([
        'id' => Uuid::uuid4()->toString(),
        'name' => 'User Without Org',
        'email' => 'noorg@example.com',
        'password' => Hash::make('password'),
        'organization_id' => null,
    ]);

    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/accounts');

    $response->assertStatus(403);
});

