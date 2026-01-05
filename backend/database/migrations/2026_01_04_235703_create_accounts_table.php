<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
