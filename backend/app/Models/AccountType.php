<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'has_credit_limit',
        'supports_borrower',
    ];

    protected $casts = [
        'has_credit_limit' => 'boolean',
        'supports_borrower' => 'boolean',
    ];
}
