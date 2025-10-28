<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) 仕様に必要なカラムを追加（存在チェック付き）
        Schema::table('products', function (Blueprint $table) {
            // product_name 追加（旧 name がある想定）
            if (! Schema::hasColumn('products', 'product_name')) {
                $table->string('product_name')->after('company_id');
            }

            if (! Schema::hasColumn('products', 'price')) {
                $table->integer('price')->after('product_name');
            }

            if (! Schema::hasColumn('products', 'stock')) {
                $table->integer('stock')->after('price');
            }

            if (! Schema::hasColumn('products', 'comment')) {
                $table->text('comment')->nullable()->after('stock');
            }

            if (! Schema::hasColumn('products', 'img_path')) {
                $table->string('img_path')->nullable()->after('comment');
            }
        });

        // 2) 旧 name → 新 product_name にデータ移行（旧 name が残っている環境のため）
        if (Schema::hasColumn('products', 'name') && Schema::hasColumn('products', 'product_name')) {
            DB::table('products')->whereNull('product_name')->update([
                'product_name' => DB::raw('name'),
            ]);
        }

        // 3) 旧 name カラムを削除（存在する場合のみ）
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'name')) {
                $table->dropColumn('name');
            }
        });
    }

    public function down(): void
    {
        // 逆変換： product_name → name に戻す（必要な場合）
        if (! Schema::hasColumn('products', 'name') && Schema::hasColumn('products', 'product_name')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('name')->nullable()->after('company_id');
            });

            DB::table('products')->whereNull('name')->update([
                'name' => DB::raw('product_name'),
            ]);

            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('product_name');
            });
        }

        // 追加したカラムを安全に削除（存在チェック付き）
        Schema::table('products', function (Blueprint $table) {
            foreach (['img_path', 'comment', 'stock', 'price'] as $col) {
                if (Schema::hasColumn('products', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
