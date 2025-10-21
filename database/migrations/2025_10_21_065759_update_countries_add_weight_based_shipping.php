<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCountriesAddWeightBasedShipping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       

Schema::table('countries', function (Blueprint $table) {
            if (!Schema::hasColumn('countries', 'weight_based_shipping')) {
                $table->json('weight_based_shipping')->nullable()->after('currency');
            }

            // Optional: drop old flat-rate columns if you won’t use them anymore
            if (Schema::hasColumn('countries', 'standard_shipping_charges')) {
                $table->dropColumn('standard_shipping_charges');
            }
            if (Schema::hasColumn('countries', 'express_shipping_charges')) {
                $table->dropColumn('express_shipping_charges');
            }
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('countries', function (Blueprint $table) {
            if (Schema::hasColumn('countries', 'weight_based_shipping')) {
                $table->dropColumn('weight_based_shipping');
            }
            // Add back old columns if you dropped them
            if (!Schema::hasColumn('countries', 'standard_shipping_charges')) {
                $table->decimal('standard_shipping_charges', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('countries', 'express_shipping_charges')) {
                $table->decimal('express_shipping_charges', 10, 2)->nullable();
            }
        });
    }
}
