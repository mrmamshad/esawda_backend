<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction', function (Blueprint $table): void {
            $table->char('currency', 3)->default('BDT')->after('amount');
            $table->unsignedBigInteger('amount_minor')->nullable()->after('currency');
            $table->string('gateway_status_code', 20)->nullable()->after('transaction_gatway');
            $table->string('gateway_transaction_id', 100)->nullable()->after('gateway_status_code');
            $table->string('gateway_transaction_number', 100)->nullable()->after('gateway_transaction_id');
            $table->string('gateway_third_party_transaction_number', 100)->nullable()->after('gateway_transaction_number');
            $table->text('gateway_status_message')->nullable()->after('gateway_third_party_transaction_number');
            $table->timestamp('gateway_initiated_at')->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->string('checkout_idempotency_key', 100)->nullable();
            $table->timestamp('policy_accepted_at')->nullable();
            $table->string('policy_version', 40)->nullable();

            $table->index(['transaction_gatway', 'payment_id'], 'tx_gateway_reference_idx');
            $table->index(['transaction_gatway', 'status', 'created_at'], 'tx_gateway_reconcile_idx');
            $table->unique('checkout_idempotency_key', 'tx_checkout_idempotency_unique');
        });

        DB::table('transaction')
            ->whereNull('amount_minor')
            ->whereNotNull('amount')
            ->update(['amount_minor' => DB::raw('ROUND(amount * 100)')]);

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            $table = DB::getTablePrefix().'transaction';
            DB::statement("ALTER TABLE `{$table}` MODIFY `status` VARCHAR(20) NULL");
        }
    }

    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table): void {
            $table->dropIndex('tx_gateway_reference_idx');
            $table->dropIndex('tx_gateway_reconcile_idx');
            $table->dropUnique('tx_checkout_idempotency_unique');
            $table->dropColumn([
                'currency', 'amount_minor', 'gateway_status_code',
                'gateway_transaction_id', 'gateway_transaction_number',
                'gateway_third_party_transaction_number', 'gateway_status_message',
                'gateway_initiated_at', 'last_verified_at', 'checkout_idempotency_key',
                'policy_accepted_at', 'policy_version',
            ]);
        });
    }
};
