<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\KhuyenMai;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = KhuyenMai::with('hinhs')
            ->where('LoaiKM', 1)
            ->orderBy('NgayBatDau', 'desc')
            ->get();

        return view('customer.promotion', [
            'promotions' => $promotions,
        ]);
    }
}
