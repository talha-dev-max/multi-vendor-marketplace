<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_earnings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vendor_order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendor_profiles')->cascadeOnDelete();
            $table->decimal('gross', 12, 2);
            $table->decimal('commission', 12, 2);
            $table->decimal('net', 12, 2);
            $table->string('status', 20)->default('pending');
            $table->timestamp('released_at')->nullable();
            $table->timestamps();

            $table->index(['vendor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_earnings');
    }
};
