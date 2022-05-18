<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->longText('instructions');
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->string('location');
            $table->string('road');
            $table->string('house');
            $table->string('street');
            $table->string('building');
            $table->unsignedInteger('amountPaid')->default(0);
            $table->unsignedInteger('discountApplied')->default(0);
            $table->string('paymentOption')->nullable();
            $table->unsignedInteger('total')->default(0);
            $table->json('products');
            $table->dateTime('placedOn')->default(Carbon::now());
            $table->dateTime('deliveryDate')->default(Carbon::now());
            $table->boolean('pending')->default(true);
            $table->boolean('rejected')->default(false);
            $table->boolean('handled')->default(false);
            $table->boolean('approved')->default(false);
            $table->boolean('confirmed')->default(false);
            $table->boolean('paid')->default(false);
            $table->boolean('scheduled')->default(false);
            $table->dateTime('scheduleDate')->default(Carbon::now());
            $table->boolean('shipped')->default(false);
            $table->dateTime('dateShipped')->default(Carbon::now());
            $table->string('orderCategory')->default('Home delivery');
            $table->string('medium')->default('site');
            $table->text('reason')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
