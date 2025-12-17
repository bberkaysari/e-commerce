<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('sku')->unique();
            $table->decimal('price', 10, 2);

            $table->unsignedInteger('stock_quantity')->default(0);

            // {"size":"M","color":"Black"} gibi
            $table->json('attributes')->nullable();

            $table->boolean('status')->default(true)->index();

            $table->timestamps();

            $table->index(['product_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};