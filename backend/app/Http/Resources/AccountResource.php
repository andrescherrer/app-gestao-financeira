<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $account = $this->resource;

        return [
            'id' => $account->id,
            'name' => $account->name,
            'account_type' => [
                'id' => $account->accountType->id ?? null,
                'name' => $account->accountType->name ?? null,
                'slug' => $account->accountType->slug ?? null,
            ],
            'initial_balance' => $account->initial_balance / 100, // Converter centavos para decimal
            'current_balance' => $account->initial_balance / 100, // TODO: Calcular saldo real quando tivermos transações
            'is_active' => $account->is_active,
            'credit_limit' => $account->credit_limit ? $account->credit_limit / 100 : null,
            'closing_day' => $account->closing_day,
            'due_day' => $account->due_day,
            'is_loan' => $account->borrower_id !== null,
            'borrower' => $this->when($account->borrower_id, [
                'id' => $account->borrower->id ?? null,
                'name' => $account->borrower->name ?? null,
            ]),
            'interest_rate' => $account->interest_rate,
            'loan_due_date' => $account->loan_due_date?->format('Y-m-d'),
            'created_at' => $account->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $account->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
