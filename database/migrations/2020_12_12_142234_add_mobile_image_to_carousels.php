<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMobileImageToCarousels extends Migration
{
    public function up()
    {
        Schema::table('carousels', function (Blueprint $table) {
            $table->string('mobile_image')->nullable();
        });
    }

    public function down()
    {
        Schema::table('carousels', function (Blueprint $table) {
            //
        });
    }
}
