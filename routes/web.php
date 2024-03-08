<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ChatController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
//create.blade.php表示のルート設定------------------------------------------
Route::get('post/create', [PostController::class, 'create'])->name('post.create');
//-------------------------------------------------------------------------

//honbucreate.blade.php表示のルート設定-------------------------------------
Route::get('post/honbucreate', [PostController::class, 'honbucreate'])->name('post.honbucreate');
//-------------------------------------------------------------------------

//create.blade.phpデータベース登録のルート設定-------------------------------
Route::post('user/post', [PostController::class, 'userstore'])->name('user.post.userstore');
//-------------------------------------------------------------------------

//honbucreate.blade.phpのデータベース登録のルート設定------------------------
Route::post('post', [PostController::class, 'honbustore'])->name('post.honbustore');
//-------------------------------------------------------------------------


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';



//chatgpt test-----------------------------------------------//
Route::get('/chat',[ChatController::class,'chat'])->name('chat.create');
Route::post('/chat',[ChatController::class,'chat'])->name('chat.post');
//------------------------------------------------------------//

//chatgpt 本番ルート設定---------------------------------------//
Route::get('usercreate/chat',[PostController::class,'userchat'])->name('usercreate.chat');
Route::post('usercreate/chat',[PostController::class,'userchat'])->name('usercreate.post.chat');
Route::post('userpost/chat',[PostController::class,'userchat'])->name('userpost.chat');
Route::get('userpost/chat',[PostController::class,'userchat'])->name('userget.chat');
//------------------------------------------------------------//

//create.blade.phpの登録ボタン押下時のルート設定----------------//
Route::post('user/store',[PostController::class, 'userStoreAction'])->name('user.store.action');
Route::get('user/store',[PostController::class, 'userStoreAction'])->name('user.create.action');

//create_kousin.blade.phpの更新ボタン押下時のルート設定---------//
Route::get('post/create_kousin',[PostController::class,'edit'])->name('create.edit');
// Route::post('post/create_kousin',[PostController::class,'postupdate'])->name('create.postupdate');
Route::patch('post/create_kousin',[PostController::class,'update'])->name('create.update');