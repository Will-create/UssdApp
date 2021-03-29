<?php

use App\Mail\TestMail;
use PhpParser\Node\Expr\New_;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UssdController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/serviceussd',[UssdController::class,'index'])->name('serviceussd');

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
Route::get('/test-permanence',function(){
    Mail::to('louisbertson@gmail.com')->send(New TestMail());

    return 'Working';
});
