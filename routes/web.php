<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// トップページ（未ログインでもアクセス可能）
Route::get('/', function () {
    return view('welcome');
});

// ダッシュボード（ログイン後の画面）※ 不要なら削除OK
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 認証必須ルートグループ
Route::middleware(['auth'])->group(function () {

    // プロフィール編集
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 商品管理ルート
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');            // 一覧
        Route::get('/create', [ProductController::class, 'create'])->name('create');     // 新規登録フォーム
        Route::post('/', [ProductController::class, 'store'])->name('store');            // 登録処理
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');      // 詳細表示
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit'); // 編集フォーム
        Route::patch('/{product}', [ProductController::class, 'update'])->name('update');// 更新処理
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy'); // 削除
    });
});

// Laravel Breeze の認証ルート
require __DIR__.'/auth.php';
