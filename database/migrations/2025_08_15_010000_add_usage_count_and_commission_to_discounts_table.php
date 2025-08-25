<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('discounts', function (Blueprint $table) {
            $table->unsignedInteger('usage_count')->default(0)->after('influencer_id');
            $table->boolean('is_eligible_for_commission')->default(false)->after('usage_count');
        });
    }

    public function down(): void {
        Schema::table('discounts', function (Blueprint $table) {
            $table->dropColumn(['usage_count', 'is_eligible_for_commission']);
        });
    }
};
