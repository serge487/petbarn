<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->index();
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->enum('category', ['dog', 'cat']);
            $table->string('subcategory');
            $table->string('measurement_unit');
            $table->decimal('measurement_value', 10, 3);
            $table->decimal('unit_price_usd', 12, 2);
            $table->decimal('toters_price', 12, 2);
            $table->enum('source', ['excel_import', 'manual'])->default('manual');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
