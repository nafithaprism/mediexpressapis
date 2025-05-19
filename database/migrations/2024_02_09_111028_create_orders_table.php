<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
            $table->id();
            $table->string('order_number');
            $table->string('tracking_number')->nullable();
            $table->string('logistics_partner_name')->nullable();
            $table->string('logistics_partner_link')->nullable();
            $table->bigInteger('user_id');
            $table->bigInteger('billing_address_id');
            $table->string('payment_type');
            $table->string('sub_total');
            $table->string('discounted_amount')->nullable();
            $table->string('total_amount');
            $table->string('currency');
            $table->bigInteger('country_id');
            $table->string('shipping_charges')->nullable();
            $table->string('payment_status')->default('PENDING');
            $table->string('status')->default('PENDING');
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