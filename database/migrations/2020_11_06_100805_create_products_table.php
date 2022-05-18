<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('name');
            $table->string('url')->nullable();
            $table->string('category')->nullable();
            $table->string('brand')->nullable();
            $table->string('image')->nullable();
            $table->json('features')->nullable();
            $table->boolean('published')->default(false);
            $table->json('displayCategory')->nullable();
            $table->boolean('available')->default(true);
            $table->json('label')->nullable();
            $table->json('images')->nullable();
            $table->json('subcategory')->nullable();
            $table->text('description')->nullable();
            $table->string('meta')->nullable();
            $table->json('quantities')->nullable();
            $table->json('discount')->nullable();
            $table->boolean('featured')->default(false);
            $table->json('tags')->nullable();
            $table->unsignedInteger('percentage')->nullable();
            $table->string('country')->nullable();
            $table->string('videoLink')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
