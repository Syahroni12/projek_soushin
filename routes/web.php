<?php

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
    return view('welcome');
});
Route::get('/jadwal', [App\Http\Controllers\JadwalController::class, 'index'])->name('jadwal');
Route::post('/tambah_jadwal', [App\Http\Controllers\JadwalController::class, 'tambah_jadwal'])->name('tambah_jadwal');
Route::post('/update_jadwal', [App\Http\Controllers\JadwalController::class, 'update_jadwal'])->name('update_jadwal');
Route::get('/hapus_jadwal/{id}', [App\Http\Controllers\JadwalController::class, 'hapus_jadwal'])->name('hapus_jadwal');

Route::get('/barang', [App\Http\Controllers\BarangController::class, 'index'])->name('barang');
Route::get('/tambah_barang', [App\Http\Controllers\BarangController::class, 'tambah_barang'])->name('tambah_barang');
Route::post('/tambah_barang_proses', [App\Http\Controllers\BarangController::class, 'tambah_barang_proses'])->name('tambah_barang_proses');
Route::get('/hapus_barang/{id}', [App\Http\Controllers\BarangController::class, 'hapus_barang'])->name('hapus_barang');
Route::get('/edit_barang/{id}', [App\Http\Controllers\BarangController::class, 'edit_barang'])->name('edit_barang');
Route::put('/update_barang/{id}', [App\Http\Controllers\BarangController::class, 'update_barang'])->name('update_barang');
