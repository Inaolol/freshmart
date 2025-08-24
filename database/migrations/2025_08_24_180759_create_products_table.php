<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable();
            $table->string('category');
            $table->string('brand')->nullable();
            $table->string('sku')->unique();
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_level')->default(5);
            $table->string('unit')->default('pcs'); // pcs, kg, lbs, etc.
            $table->string('image')->nullable();
            $table->json('gallery')->nullable(); // for multiple images
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->decimal('weight', 8, 3)->nullable();
            $table->string('barcode')->nullable();
            $table->date('expiry_date')->nullable();
            $table->json('nutritional_info')->nullable();
            $table->text('tags')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['category', 'is_active']);
            $table->index(['is_featured', 'is_active']);
            $table->index('price');
            $table->index('stock_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
