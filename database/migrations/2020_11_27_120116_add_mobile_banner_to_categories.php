<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMobileBannerToCategories extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('mobile_banner');
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            //
        });
    }
}
