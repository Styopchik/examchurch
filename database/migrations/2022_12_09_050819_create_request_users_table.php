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
        Schema::create('request_users', function (Blueprint $table) {
            $table->id();
            $table->string('name',191);
            $table->string('email',191);
            $table->string('password',191);
            $table->string('type',191);
            $table->boolean('is_approved')->default(0);
            $table->boolean('payment_status')->default(0);
            $table->text('disapprove_reason')->nullable();
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
        Schema::dropIfExists('request_users');
    }
};
