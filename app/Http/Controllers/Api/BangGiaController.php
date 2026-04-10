<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BangGia;
use Illuminate\Http\Request;

class BangGiaController extends Controller
{
    // GET /api/bang-gia
    public function index()
    {
        return response()->json(
            BangGia::with('loaiPhong')->get()
        );
    }

    // POST /api/bang-gia
    public function store(Request $request)
    {
        $data = $request->validate([
            'MaLoaiPhong' => 'required|exists:LoaiPhong,MaLoaiPhong',
            'Mua' => 'required|integer',
            'GiaPhong' => 'required|numeric'
        ]);

        // check trùng (quan trọng)
        $exists = BangGia::where('MaLoaiPhong', $data['MaLoaiPhong'])
            ->where('Mua', $data['Mua'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Đã tồn tại giá cho mùa này'
            ], 400);
        }

        $bangGia = BangGia::create($data);

        return response()->json(
            BangGia::with('loaiPhong')
                ->where('MaLoaiPhong', $data['MaLoaiPhong'])
                ->where('Mua', $data['Mua'])
                ->first(),
            201
        );
    }

    // GET /api/bang-gia/{maLoaiPhong}/{mua}
    public function show($maLoaiPhong, $mua)
    {
        return response()->json(
            BangGia::with('loaiPhong')
                ->where('MaLoaiPhong', $maLoaiPhong)
                ->where('Mua', $mua)
                ->firstOrFail()
        );
    }

    // PUT /api/bang-gia/{maLoaiPhong}/{mua}
    public function update(Request $request, $maLoaiPhong, $mua)
    {
        $data = $request->validate([
            'GiaPhong' => 'required|numeric'
        ]);

        BangGia::where('MaLoaiPhong', $maLoaiPhong)
            ->where('Mua', $mua)
            ->update($data);

        return response()->json([
            'message' => 'Updated'
        ]);
    }

    // DELETE /api/bang-gia/{maLoaiPhong}/{mua}
    public function destroy($maLoaiPhong, $mua)
    {
        BangGia::where('MaLoaiPhong', $maLoaiPhong)
            ->where('Mua', $mua)
            ->delete();

        return response()->json([
            'message' => 'Deleted'
        ]);
    }
}