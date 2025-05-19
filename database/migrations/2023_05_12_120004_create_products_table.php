<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('product_type')->nullable();
            $table->string('route')->nullable();
            $table->string('featured_img')->nullable();
            $table->bigInteger('brand_id')->nullable();
            $table->string('sku')->nullable();
            $table->string('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->longText('additional_information')->nullable();
            $table->longText('slider_img')->nullable();
            $table->longText('tags')->nullable();
            $table->string('status')->default(['1' => 'Active', '0' => 'Disabled']);
            $table->string('stock')->default(['1' => 'inStock', '0' => 'Out of Stock']);
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
        Schema::dropIfExists('products');
    }
}
