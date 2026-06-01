<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('total', 12, 2);
            $table->string('currency', 3)->default('usd');
            $table->string('status', 30)->default('pending');
            $table->string('payment_method', 20);
            $table->string('payment_status', 20)->default('pending');
            $table->json('shipping_address');
            $table->timestamp('placed_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
