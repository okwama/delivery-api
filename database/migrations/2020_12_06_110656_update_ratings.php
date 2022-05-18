<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRatings extends Migration
{
    public function up()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->string('productId');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->json('product')->nullable();
        });
    }

    public function down()
    {
        Schema::table('ratings', function (Blueprint $table) {
            //
        });
    }
}
