<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $accounts = Account::query()
            ->where('organization_id', $user->organization_id)
            ->with(['accountType', 'borrower'])
            ->orderBy('name')
            ->get();

        return response()->json(AccountResource::collection($accounts));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAccountRequest $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $account = Account::create([
            'organization_id' => $user->organization_id,
            'account_type_id' => $request->account_type_id,
            'name' => $request->name,
            'initial_balance' => (int) ($request->initial_balance * 100), // Converter para centavos
            'credit_limit' => $request->credit_limit ? (int) ($request->credit_limit * 100) : null,
            'closing_day' => $request->closing_day,
            'due_day' => $request->due_day,
            'is_active' => true,
        ]);

        $account->load(['accountType', 'borrower']);

        return response()->json(new AccountResource($account), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $account = Account::query()
            ->where('organization_id', $user->organization_id)
            ->where('id', $id)
            ->with(['accountType', 'borrower'])
            ->firstOrFail();

        return response()->json(new AccountResource($account));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccountRequest $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $account = Account::query()
            ->where('organization_id', $user->organization_id)
            ->where('id', $id)
            ->firstOrFail();

        $data = $request->validated();

        if (isset($data['initial_balance'])) {
            $data['initial_balance'] = (int) ($data['initial_balance'] * 100);
        }

        if (isset($data['credit_limit'])) {
            $data['credit_limit'] = (int) ($data['credit_limit'] * 100);
        }

        $account->update($data);
        $account->load(['accountType', 'borrower']);

        return response()->json(new AccountResource($account));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $account = Account::query()
            ->where('organization_id', $user->organization_id)
            ->where('id', $id)
            ->firstOrFail();

        $account->delete();

        return response()->json(['message' => 'Account deleted successfully'], 200);
    }

    /**
     * Get account balance.
     */
    public function balance(Request $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $account = Account::query()
            ->where('organization_id', $user->organization_id)
            ->where('id', $id)
            ->firstOrFail();

        // Por enquanto, retorna o saldo inicial
        // TODO: Calcular saldo real quando tivermos transações
        $balance = $account->initial_balance / 100;

        return response()->json([
            'account_id' => $account->id,
            'account_name' => $account->name,
            'balance' => $balance,
            'currency' => 'BRL',
        ]);
    }

    /**
     * Create a loan for another user.
     */
    public function lend(Request $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $request->validate([
            'borrower_id' => ['required', 'uuid', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'interest_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'loan_due_date' => ['required', 'date', 'after:today'],
        ]);

        $account = Account::query()
            ->where('organization_id', $user->organization_id)
            ->where('id', $id)
            ->with('accountType')
            ->firstOrFail();

        // Verificar se o tipo de conta suporta empréstimos
        /** @var \App\Models\AccountType $accountType */
        $accountType = $account->accountType;
        if (! $accountType->supports_borrower) {
            return response()->json([
                'message' => 'This account type does not support loans',
            ], 422);
        }

        // Criar nova conta de empréstimo
        $loanAccount = Account::create([
            'organization_id' => $user->organization_id,
            'account_type_id' => $account->account_type_id,
            'name' => "Empréstimo para {$request->borrower_id}",
            'initial_balance' => (int) ($request->amount * 100),
            'borrower_id' => $request->borrower_id,
            'interest_rate' => $request->interest_rate,
            'loan_due_date' => $request->loan_due_date,
            'is_active' => true,
        ]);

        $loanAccount->load(['accountType', 'borrower']);

        return response()->json(new AccountResource($loanAccount), 201);
    }

    /**
     * List loans for an account.
     */
    public function loans(Request $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $account = Account::query()
            ->where('organization_id', $user->organization_id)
            ->where('id', $id)
            ->firstOrFail();

        $loans = Account::query()
            ->where('organization_id', $user->organization_id)
            ->where('borrower_id', $account->id)
            ->with(['accountType', 'borrower'])
            ->get();

        return response()->json(AccountResource::collection($loans));
    }

    /**
     * Register loan repayment.
     */
    public function repay(Request $request, string $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $account = Account::query()
            ->where('organization_id', $user->organization_id)
            ->where('id', $id)
            ->whereNotNull('borrower_id')
            ->firstOrFail();

        // Por enquanto, apenas registra o pagamento
        // TODO: Criar transação quando tivermos o módulo de transações
        $repaymentAmount = (int) ($request->amount * 100);

        // Atualizar saldo inicial (reduzir o valor do empréstimo)
        $account->initial_balance = max(0, $account->initial_balance - $repaymentAmount);
        $account->save();

        return response()->json([
            'message' => 'Repayment registered successfully',
            'remaining_balance' => $account->initial_balance / 100,
        ]);
    }
}
