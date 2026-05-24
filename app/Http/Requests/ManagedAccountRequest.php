<?php

namespace App\Http\Requests;

use App\Models\TaiKhoan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManagedAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $accountId = $this->route('recordId');

        return [
            'Email' => [
                'required',
                'email',
                Rule::unique('TaiKhoan', 'Email')->ignore($accountId, 'MaTK'),
            ],
            'MatKhau' => [
                $accountId ? 'nullable' : 'required',
                'string',
                'min:6',
            ],
            'LoaiTaiKhoan' => ['required', $accountId ? 'in:0,1,2,3,4' : 'in:0,1,2'],
            'TrangThai' => ['required', 'in:0,1'],
            'customer_id' => [
                'nullable',
                'required_if:LoaiTaiKhoan,0',
                'integer',
                'exists:KhachHang,MaKH',
                function (string $attribute, mixed $value, \Closure $fail) use ($accountId) {
                    if ($value === null || $value === '') {
                        return;
                    }

                    $linkedAccount = TaiKhoan::where('MaKH', $value)
                        ->when($accountId, fn ($query) => $query->where('MaTK', '!=', $accountId))
                        ->exists();

                    if ($linkedAccount) {
                        $fail('Khách hàng này đã được gắn với tài khoản khác.');
                    }
                },
            ],
            'employee_id' => [
                'nullable',
                'required_unless:LoaiTaiKhoan,0',
                'integer',
                'exists:NhanVien,MaNV',
                function (string $attribute, mixed $value, \Closure $fail) use ($accountId) {
                    if ($value === null || $value === '') {
                        return;
                    }

                    $linkedAccount = TaiKhoan::where('MaNV', $value)
                        ->when($accountId, fn ($query) => $query->where('MaTK', '!=', $accountId))
                        ->exists();

                    if ($linkedAccount) {
                        $fail('Nhân viên này đã được gắn với tài khoản khác.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'Email.required' => 'Vui lòng nhập email.',
            'Email.email' => 'Email không hợp lệ.',
            'Email.unique' => 'Email đã được sử dụng.',
            'MatKhau.required' => 'Vui lòng nhập mật khẩu.',
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'LoaiTaiKhoan.required' => 'Vui lòng chọn loại tài khoản.',
            'LoaiTaiKhoan.in' => 'Loại tài khoản không hợp lệ.',
            'TrangThai.required' => 'Vui lòng chọn trạng thái.',
            'TrangThai.in' => 'Trạng thái không hợp lệ.',
            'customer_id.required_if' => 'Vui lòng chọn khách hàng.',
            'customer_id.exists' => 'Khách hàng không tồn tại.',
            'employee_id.required_unless' => 'Vui lòng chọn nhân viên.',
            'employee_id.exists' => 'Nhân viên không tồn tại.',
        ];
    }
}
