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
            // restrictOnDelete: kategori yang masih punya produk tidak bisa dihapus di level database
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('sku', 50)->unique();          // SKU unik dilindungi constraint database
            $table->string('name', 150)->index();
            $table->decimal('purchase_price', 12, 2);
            $table->decimal('selling_price', 12, 2);
            $table->unsignedInteger('stock')->default(0);  // unsigned: stok tidak bisa negatif
            $table->unsignedInteger('minimum_stock')->default(0);
            $table->softDeletes();                         // produk tidak di-hard delete
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
