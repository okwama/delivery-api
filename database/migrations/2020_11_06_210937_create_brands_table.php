<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->string('brand');
            $table->string('title')->nullable();
            $table->string('headerOne')->nullable();
            $table->string('url')->nullable();
            $table->string('category')->nullable();
            $table->text('pagedesc')->nullable();
            $table->text('footerContent')->nullable();
            $table->string('website')->nullable();
            $table->text('description')->nullable();
            $table->string('country')->nullable();
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
        Schema::dropIfExists('brands');
    }
}
