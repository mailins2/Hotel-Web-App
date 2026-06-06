<?php

return [
    'khach_san' => [
        'ten' => 'Peach Valley',
        'dia_chi' => '26K đường Yersin, Đà Lạt, Lâm Đồng',
        'dien_thoai' => '0987098120',
        'email' => 'info@peachvalley.vn',
        'gio_nhan_phong' => '14:00',
        'gio_tra_phong' => '12:00',
    ],

    'luong_dat_phong' => [
        'tim_phong_trong' => [
            'Trang chủ có form tìm kiếm theo ngày nhận phòng, ngày trả phòng, số khách và số phòng.',
            'Trang /customer/rooms-booking hiển thị kết quả phòng trống theo dữ liệu tìm kiếm và gọi API /api/phong/tim-kiem.',
            'Trang /customer/rooms chỉ dùng để xem/lọc danh sách loại phòng, không kiểm tra phòng trống theo ngày.',
        ],
        'dat_phong' => [
            'Sau khi chọn phòng ở trang kết quả, khách bấm Đặt ngay để sang trang nhập thông tin đặt phòng.',
            'Khách cần nhập họ tên và số điện thoại hợp lệ.',
            'Không thể chỉnh sửa thông tin sau khi đặt phòng thành công.',
            'Hệ thống giữ phòng trong 15 phút để khách thanh toán.',
        ],
        'xem_lai_dat_phong_khi_chua_co_tai_khoan' => [
            'Nếu khách đã đặt phòng trước khi đăng ký tài khoản, hãy hướng dẫn khách đăng ký tài khoản bằng đúng số điện thoại đã dùng khi đặt phòng.',
            'Sau khi đăng ký và đăng nhập bằng tài khoản đó, khách có thể vào mục đặt phòng của tôi để xem lịch sử đặt phòng liên quan đến số điện thoại này.',
            'Hệ thống hiện không gửi email xác nhận đặt phòng, vì vậy không hướng dẫn khách kiểm tra email xác nhận.',
            'Nếu khách đăng ký bằng số điện thoại khác với số đã dùng đặt phòng, lịch sử đặt phòng cũ có thể không hiển thị.',
            'Nếu vẫn không thấy lịch sử sau khi đăng nhập đúng số điện thoại, khách nên liên hệ khách sạn để được hỗ trợ tra cứu.',
        ],
    ],

    'chinh_sach_dat_phong' => [
        'Khách hàng cần thanh toán trước 100% tiền phòng để được xác nhận đặt phòng.',
        'Ngày nhận phòng không được ở quá khứ.',
        'Ngày trả phòng phải sau ngày nhận phòng.',
        'Hệ thống chỉ cho phép đặt tối đa trong khoảng 1 năm tới.',
        'Nếu quá 15 phút chưa thanh toán, lượt giữ phòng có thể hết hiệu lực.',
    ],

    'chinh_sach_huy_phong' => [
        'Khách có thể hủy đặt phòng trong tài khoản ở mục đặt phòng của tôi nếu đơn còn ở trạng thái chờ xác nhận hoặc đã xác nhận.',
        'Không thể hủy đặt phòng đã quá ngày nhận phòng hoặc đã nhận phòng.',
        'Hủy trước 10-15 ngày: có thể được miễn phí hủy phòng.',
        'Hủy trước 5-10 ngày: có thể chịu 30%-70% phí đặt phòng.',
        'Hủy trước 1-5 ngày: có thể chịu 100% phí đặt phòng.',
        'Mức phí thực tế cần xác nhận với khách sạn theo từng trường hợp.',
    ],

    'thanh_toan' => [
        'Khách có thể thanh toán bằng VNPAY hoặc ZaloPay.',
        'Trang thanh toán sẽ tạo giữ phòng trước, sau đó chuyển khách sang cổng thanh toán.',
        'Sau khi thanh toán thành công, hệ thống xác nhận đặt phòng và hóa đơn.',
        'Nếu thanh toán thất bại hoặc quá thời gian giữ phòng, khách nên tìm và đặt lại phòng.',
    ],

    'khuyen_mai_va_tich_diem' => [
        'Khách hàng có điểm tích lũy trong trường DIEM của hồ sơ khách hàng.',
        'Một số mã khuyến mãi yêu cầu số điểm nhất định để đổi.',
        'Khi đổi mã bằng điểm, hệ thống kiểm tra đủ điểm, thời hạn mã và mã chưa có trong kho ở trạng thái chưa dùng.',
        'Đổi mã thành công sẽ trừ điểm và thêm mã vào kho khuyến mãi của khách.',
        'Trạng thái mã trong kho: 0 là chưa dùng, 1 là đã dùng, 2 là hết hạn.',
        'Khi đặt phòng, khách chỉ chọn được mã còn hạn, chưa dùng và thuộc kho khuyến mãi của mình.',
    ],

    'dich_vu' => [
        'Khách có thể xem danh sách dịch vụ trên website.',
        'Dịch vụ phòng thường dành cho khách đã có đặt phòng đang lưu trú hoặc đủ điều kiện sử dụng dịch vụ.',
        'Giá dịch vụ được lấy từ dữ liệu dịch vụ hiện có trong hệ thống.',
    ],

    'bao_mat_rieng_tu' => [
        'Chatbot không tự tạo, sửa, hủy đặt phòng hoặc xác nhận thanh toán thay khách.',
        'Với thông tin cá nhân như lịch sử đặt phòng, hóa đơn, điểm hiện có hoặc mã trong ví, chatbot nên hướng dẫn khách đăng nhập tài khoản hoặc liên hệ khách sạn.',
    ],
];
