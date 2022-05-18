<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->string('title');
            $table->string('meta');
            $table->string('url');
            $table->text('body')->nullable();
            $table->string('image')->nullable();
            $table->json('images')->nullable();
            $table->string('website')->nullable();
            $table->json('ads')->nullable();
            $table->json('keywords')->nullable();
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
        Schema::dropIfExists('articles');
    }
}
