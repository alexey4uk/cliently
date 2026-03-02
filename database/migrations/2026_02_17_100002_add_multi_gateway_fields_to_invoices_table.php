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
        Schema::table('invoices', function (Blueprint $table) {
            // Тип оплаты (subscription, purchase, donation, balance)
            $table->string('payment_type', 50)->default('subscription')->after('status');

            // Платёжный шлюз (bepaid, freekassa)
            $table->string('gateway', 50)->nullable()->after('payment_type');

            // Универсальные поля для любого шлюза
            $table->string('gateway_transaction_id')->nullable()->after('bepaid_payment_token');
            $table->string('gateway_payment_url')->nullable()->after('gateway_transaction_id');
            $table->json('gateway_response')->nullable()->after('gateway_payment_url');

            // Полиморфная связь для разных типов оплат
            $table->nullableMorphs('payable');

            // Индексы
            $table->index('payment_type');
            $table->index('gateway');
            $table->index('gateway_transaction_id');
        });

        // Мигрируем существующие данные: копируем bepaid_transaction_id в gateway_transaction_id
        // и устанавливаем gateway = 'bepaid' для существующих записей
        \Illuminate\Support\Facades\DB::table('invoices')
            ->whereNotNull('bepaid_transaction_id')
            ->update([
                'gateway' => 'bepaid',
                'gateway_transaction_id' => \Illuminate\Support\Facades\DB::raw('bepaid_transaction_id'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['payment_type']);
            $table->dropIndex(['gateway']);
            $table->dropIndex(['gateway_transaction_id']);
            $table->dropMorphs('payable');

            $table->dropColumn([
                'payment_type',
                'gateway',
                'gateway_transaction_id',
                'gateway_payment_url',
                'gateway_response',
            ]);
        });
    }
};
