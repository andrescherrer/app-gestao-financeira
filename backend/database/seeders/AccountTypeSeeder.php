<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class AccountTypeSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accountTypes = [
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Conta Corrente',
                'slug' => 'checking',
                'has_credit_limit' => false,
                'supports_borrower' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Cartão de Crédito',
                'slug' => 'credit_card',
                'has_credit_limit' => true,
                'supports_borrower' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Investimento',
                'slug' => 'investment',
                'has_credit_limit' => false,
                'supports_borrower' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => 'Empréstimo',
                'slug' => 'loan',
                'has_credit_limit' => false,
                'supports_borrower' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('account_types')->insert($accountTypes);
    }
}
