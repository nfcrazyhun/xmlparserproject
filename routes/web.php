<?php

use App\Http\Controllers\XmlParserV2Controller;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('home');
})->name('home');


Route::resource('xml-parser-v2', XmlParserV2Controller::class)->only('index','store');
Route::resource('products', ProductController::class)->only('index','show');
