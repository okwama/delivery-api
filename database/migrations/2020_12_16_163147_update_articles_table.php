<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateArticlesTable extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('images');
            $table->dropColumn('website');
            $table->dropColumn('ads');
            $table->dropColumn('keywords');
           $table->json('tags')->nullable();


        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            //
        });
    }
}
