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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->integer('business_id');
            $table->integer('product_category_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->integer('min_indicator')->default(0);
            $table->integer('max_indicator')->default(0);
            $table->integer('stock_value')->default(0);
            $table->integer('selling_price')->default(0);
            $table->string('bar_code')->nullable();
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
        Schema::dropIfExists('items');
    }
};
