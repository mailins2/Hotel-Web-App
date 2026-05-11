<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManagedCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $recordId = $this->route('recordId');

        return [
            'account_id' => [
                'nullable',
                'exists:TaiKhoan,MaTK',
                Rule::unique('KhachHang', 'MaTK')->ignore($recordId, 'MaKH'),
            ],
            'full_name' => ['required', 'string', 'min:2', 'max:60', 'regex:/^[\pL\s]+$/u'],
            'gender' => ['required', 'in:0,1,2'],
            'phone' => [
                'required',
                'regex:/^0[0-9]{9}$/',
                Rule::unique('KhachHang', 'SoDienThoai')->ignore($recordId, 'MaKH'),
            ],
            'cccd' => [
                'required',
                'regex:/^[0-9]{12}$/',
                Rule::unique('KhachHang', 'CCCD')->ignore($recordId, 'MaKH'),
            ],
            'birthday' => ['required', 'date', 'before_or_equal:today'],
            'province' => ['nullable', 'string'],
            'district' => ['nullable', 'string'],
            'address_line' => ['nullable', 'string', 'min:4', 'max:120', 'regex:/^[0-9\pL\s.\/,\-]+$/u'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.exists' => 'Tài khoản không tồn tại.',
            'account_id.unique' => 'Tài khoản này đã được gắn cho khách hàng khác.',
            'full_name.required' => 'Vui lòng nhập họ và tên.',
            'full_name.min' => 'Họ và tên phải có ít nhất 2 ký tự.',
            'full_name.max' => 'Họ và tên không được vượt quá 60 ký tự.',
            'full_name.regex' => 'Họ và tên chỉ được gồm chữ cái và khoảng trắng.',
            'gender.required' => 'Vui lòng chọn giới tính.',
            'gender.in' => 'Giới tính không hợp lệ.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.',
            'phone.unique' => 'Số điện thoại đã được sử dụng.',
            'cccd.required' => 'Vui lòng nhập CCCD.',
            'cccd.regex' => 'CCCD phải gồm đúng 12 chữ số.',
            'cccd.unique' => 'CCCD đã được sử dụng.',
            'birthday.required' => 'Vui lòng chọn ngày sinh.',
            'birthday.date' => 'Ngày sinh không hợp lệ.',
            'birthday.before_or_equal' => 'Ngày sinh không được lớn hơn ngày hiện tại.',
            'address_line.min' => 'Số nhà và tên đường phải có ít nhất 4 ký tự.',
            'address_line.max' => 'Số nhà và tên đường không được vượt quá 120 ký tự.',
            'address_line.regex' => 'Số nhà và tên đường chỉ được gồm chữ, số và ký tự . / , -',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
        ];
    }
}
