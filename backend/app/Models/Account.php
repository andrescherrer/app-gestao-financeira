<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'account_type_id',
        'name',
        'initial_balance',
        'is_active',
        'credit_limit',
        'closing_day',
        'due_day',
        'borrower_id',
        'interest_rate',
        'loan_due_date',
    ];

    protected $casts = [
        'initial_balance' => 'integer',
        'is_active' => 'boolean',
        'credit_limit' => 'integer',
        'closing_day' => 'integer',
        'due_day' => 'integer',
        'interest_rate' => 'decimal:2',
        'loan_due_date' => 'date',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class);
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }
}
