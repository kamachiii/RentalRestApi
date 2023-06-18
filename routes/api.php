<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RentalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/', function () {
    return view('welcome');
});
Route::get('/rentals', [RentalController::class, 'index']);
//Tambah data
Route::post('/rentals/store', [RentalController::class, 'store']);
//Menambahkan token
Route::get('/', [RentalController::class, 'createToken']);
//Menampilkan data spesifik
Route::get('/rentals/{id}', [RentalController::class, 'show']);
//Mengubah data
Route::patch('/rentals/update/{id}',[RentalController::class,'update']);
//Hapus data (softdeletes)
Route::delete('/rentals/delete/{id}', [RentalController::class, 'destroy']);
//Menampilkan data terhapus sementara
Route::get('/rentals/show/trash', [RentalController::class, 'trash']);
//Mengembalikan data yang telah dihapus sementara
Route::get('/rentals/trash/restore/{id}', [RentalController::class, 'restore']);
//Menangani proses penghapusan data permanen
Route::post('/rentals/trash/delete/permanent/{id}', [RentalController::class, 'permanentDelete']);

