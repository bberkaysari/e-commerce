<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            // shipping / billing
            $table->string('type')->index();

            $table->string('full_name');
            $table->string('phone')->nullable();

            $table->string('city');
            $table->string('district')->nullable();
            $table->string('address_line');
            $table->string('postal_code')->nullable();

            $table->timestamps();

            $table->unique(['order_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_addresses');
    }
};