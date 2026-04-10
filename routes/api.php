<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KhachHangController;
use App\Http\Controllers\Api\LoaiPhongController;
use App\Http\Controllers\Api\PhongController;
use App\Http\Controllers\Api\TienNghiController;
use App\Http\Controllers\Api\BangGiaController;
use App\Http\Controllers\Api\DatPhongController;
use App\Http\Controllers\Api\DichVuController;
use App\Http\Controllers\Api\SuDungDichVuController;
use App\Http\Controllers\Api\TaiKhoanController;
use App\Http\Controllers\Api\NhanVienController;
use App\Http\Controllers\Api\DenBuHuHongController;
use App\Http\Controllers\Api\KhuyenMaiController;
use App\Http\Controllers\Api\KhoKhuyenMaiController;

Route::get('/bang-gia', [BangGiaController::class, 'index']);
Route::post('/bang-gia', [BangGiaController::class, 'store']);
Route::get('/bang-gia/{maLoaiPhong}/{mua}', [BangGiaController::class, 'show']);
Route::put('/bang-gia/{maLoaiPhong}/{mua}', [BangGiaController::class, 'update']);
Route::delete('/bang-gia/{maLoaiPhong}/{mua}', [BangGiaController::class, 'destroy']);

Route::apiResource('tien-nghi', TienNghiController::class);

Route::apiResource('khachhangs', KhachHangController::class);

Route::apiResource('loai-phong', LoaiPhongController::class);

Route::post('/loai-phong/{id}/tien-nghi', [LoaiPhongController::class, 'updateTienNghi']);
Route::post('/loai-phong/{id}/tien-nghi/{tienNghiId}', [LoaiPhongController::class, 'addTienNghi']);
Route::delete('/loai-phong/{id}/tien-nghi/{tienNghiId}', [LoaiPhongController::class, 'removeTienNghi']);


Route::get('/phong/trong', [PhongController::class, 'phongTrong']);
Route::apiResource('phong', PhongController::class);


Route::post('dat-phong/{id}/phong', [DatPhongController::class, 'addPhong']);
Route::delete('dat-phong/{id}/phong/{maPhong}', [DatPhongController::class, 'removePhong']);
Route::apiResource('dat-phong', DatPhongController::class);

Route::apiResource('dich-vu', \App\Http\Controllers\Api\DichVuController::class);

Route::get(
    'su-dung-dich-vu/dat-phong/{id}',
    [\App\Http\Controllers\Api\SuDungDichVuController::class, 'byDatPhong']
);
Route::apiResource('su-dung-dich-vu', \App\Http\Controllers\Api\SuDungDichVuController::class);


Route::apiResource('tai-khoan', TaiKhoanController::class);

Route::apiResource('nhan-vien', NhanVienController::class);

Route::apiResource('khachhang', KhachHangController::class);

Route::apiResource('den-bu', DenBuHuHongController::class);

Route::apiResource('khuyen-mai', KhuyenMaiController::class);

Route::apiResource('kho-khuyen-mai', KhoKhuyenMaiController::class);
