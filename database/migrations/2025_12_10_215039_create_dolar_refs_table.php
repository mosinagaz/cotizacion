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
        Schema::create('dolar_refs', function (Blueprint $table) {
            $table->id();
            $table->date('fecha')->unique();
            $table->decimal('precio_compra', 10, 5);
            $table->decimal('precio_venta', 10, 5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dolar_refs');
    }
};
