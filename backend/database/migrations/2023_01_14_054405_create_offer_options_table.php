<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_options', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('factor');
            $table->foreignId('offer_id')->constrained('offers')->cascadeOnDelete();
            $table->enum('status',['active','inactive'])->default('inactive');
            $table->float('percentge_value')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer_options');
    }
};
