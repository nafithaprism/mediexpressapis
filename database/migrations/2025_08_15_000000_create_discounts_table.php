<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();              // e.g. LOVETTO10
            $table->enum('type', ['percentage','flat']);   // percentage | flat
            $table->decimal('value', 10, 2);               // % or currency amount
            $table->timestamp('valid_till')->nullable();   // null = no expiry
            $table->boolean('active')->default(true);
            $table->foreignId('influencer_id')->nullable()
                  ->constrained('users')->nullOnDelete();  // who owns the code (optional)
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('discounts');
    }
};
