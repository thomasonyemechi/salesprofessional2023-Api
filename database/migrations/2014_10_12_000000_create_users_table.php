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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('business_id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('date_of_birth')->nullable();
            $table->string('phone');
            $table->string('email')->unique();
            $table->integer('role')->default(3);
            $table->string('address')->nullable();
            $table->string('appointment_date')->nullable();
            $table->string('appointment_type')->nullable();
            $table->string('note')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
