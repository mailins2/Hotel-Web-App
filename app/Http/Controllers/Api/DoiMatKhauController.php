<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoiMatKhauController extends Controller
{
    public function doiMatKhau(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->MatKhau)) {
            return response()->json(['message' => 'Mật khẩu cũ không đúng'], 400);
        }

        $user->update(['MatKhau' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Đổi mật khẩu thành công!']);
    }
}