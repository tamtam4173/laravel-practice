<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'company_id')) {
                $table->unsignedBigInteger('company_id')->after('id');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            
            $table->foreign('company_id', 'products_company_id_foreign')
                  ->references('id')->on('companies')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        
        Schema::table('products', function (Blueprint $table) {
            
            $table->dropForeign('products_company_id_foreign');
            if (Schema::hasColumn('products', 'company_id')) {
                $table->dropColumn('company_id');
            }
        });
    }
};
