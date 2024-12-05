<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TransactionTypeEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets')->onDelete('cascade');
            $table->enum('type', [
                TransactionTypeEnum::DEPOSIT->value,
                TransactionTypeEnum::WITHDRAWAL->value,
                TransactionTypeEnum::OUTGOING_TRANSFER->value,
                TransactionTypeEnum::INCOMING_TRANSFER->value
            ]);
            $table->decimal('amount', 15);
            $table->decimal('fee', 15)->default(0.00);
            $table->decimal('final_balance', 15);
            $table->foreignId('related_wallet_id')->nullable()
                ->constrained('wallets')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
