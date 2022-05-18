<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metas', function (Blueprint $table) {
            $table->string('headerOne');
            $table->string('category');
            $table->string('title');
            $table->string('pagetitle');
            $table->text('pagedesc')->nullable();
            $table->string('quotetitle')->nullable();
            $table->text('metadescription')->nullable();
            $table->text('footercontent')->nullable();
            $table->json('scripts')->nullable();
            $table->json('quotes')->nullable();
            $table->boolean('isCategory')->default(false);
            $table->string('website')->nullable();
            $table->text('highlight')->nullable();
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
        Schema::dropIfExists('metas');
    }
}
