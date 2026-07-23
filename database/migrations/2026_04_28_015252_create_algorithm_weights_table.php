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
        Schema::create('algorithm_weights', function (Blueprint $table) {
            $table->id();
            $table->integer('s_weight')->default(30);
            $table->integer('p_weight')->default(25);
            $table->integer('t_weight')->default(20);
            $table->integer('w_weight')->default(15);
            $table->integer('c_weight')->default(10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('algorithm_weights');
    }
};
