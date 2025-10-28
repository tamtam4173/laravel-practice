<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

           
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

            
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            
            $table->unsignedInteger('quantity');      
            $table->unsignedInteger('unit_price');     
            $table->unsignedInteger('subtotal');       

            
            $table->timestamp('sold_at')->useCurrent();

            
            $table->string('note')->nullable();

            $table->timestamps();

            
            $table->index(['sold_at']);
            $table->index(['product_id', 'sold_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
