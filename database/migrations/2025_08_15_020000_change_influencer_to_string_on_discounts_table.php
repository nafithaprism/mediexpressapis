<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('discounts', function (Blueprint $table) {
            // Safely drop FK (works if FK was created with ->constrained())
            if (Schema::hasColumn('discounts', 'influencer_id')) {
                // For Laravel 8+: dropConstrainedForeignId; otherwise use dropForeign + dropColumn
                try {
                    $table->dropConstrainedForeignId('influencer_id');
                } catch (\Throwable $e) {
                    $table->dropForeign(['influencer_id']);
                    $table->dropColumn('influencer_id');
                }
            }

            // Add the string column
            if (!Schema::hasColumn('discounts', 'influencer')) {
                $table->string('influencer')->nullable()->index()->after('active');
            }

            // Ensure the extra fields exist (from earlier step)
            if (!Schema::hasColumn('discounts', 'usage_count')) {
                $table->unsignedInteger('usage_count')->default(0)->after('influencer');
            }
            if (!Schema::hasColumn('discounts', 'is_eligible_for_commission')) {
                $table->boolean('is_eligible_for_commission')->default(false)->after('usage_count');
            }
        });
    }

    public function down(): void {
        Schema::table('discounts', function (Blueprint $table) {
            // Remove the string field & extras
            if (Schema::hasColumn('discounts', 'is_eligible_for_commission')) {
                $table->dropColumn('is_eligible_for_commission');
            }
            if (Schema::hasColumn('discounts', 'usage_count')) {
                $table->dropColumn('usage_count');
            }
            if (Schema::hasColumn('discounts', 'influencer')) {
                $table->dropColumn('influencer');
            }

            // Optionally restore influencer_id FK (only if you really want to roll back)
            // $table->foreignId('influencer_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }
};
