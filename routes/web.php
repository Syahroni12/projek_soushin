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

Route::get('/', [App\Http\Controllers\AuthController::class, 'index'])->name('login')->middleware('guest');
Route::get('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register')->middleware('guest');
Route::post('/registeract', [App\Http\Controllers\AuthController::class, 'registeract'])->name('registeract')->middleware('guest');
Route::post('/loginact', [App\Http\Controllers\AuthController::class, 'loginact'])->name('loginact')->middleware('guest');


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

Route::get('/jenis_acara', [App\Http\Controllers\JenisAcaraController::class, 'index'])->name('jenis_acara');
Route::post('/tambah_jenis_acara', [App\Http\Controllers\JenisAcaraController::class, 'tambah_jenis_acara'])->name('tambah_jenis_acara');
Route::get('/hapus_jenis_acara/{id}', [App\Http\Controllers\JenisAcaraController::class, 'hapus_jenis_acara'])->name('hapus_jenis_acara');
Route::post('/update_jenis_acara', [App\Http\Controllers\JenisAcaraController::class, 'update_jenis_acara'])->name('update_jenis_acara');

Route::get('/ipen', [App\Http\Controllers\EventController::class, 'index'])->name('ipen');
Route::get('/tambah_event', [App\Http\Controllers\EventController::class, 'tambah_event'])->name('tambah_event');
Route::post('/tambah_event_proses', [App\Http\Controllers\EventController::class, 'tambah_event_proses'])->name('tambah_event_proses');
Route::get('/hapus_event/{id}', [App\Http\Controllers\EventController::class, 'hapus_event'])->name('hapus_event');
Route::get('/edit_event/{id}', [App\Http\Controllers\EventController::class, 'edit_event'])->name('edit_event');
Route::put('/update_event/{id}', [App\Http\Controllers\EventController::class, 'update_event'])->name('update_event');
Route::get('/event_detail/{id}', [App\Http\Controllers\EventController::class, 'event_detail'])->name('event_detail');




