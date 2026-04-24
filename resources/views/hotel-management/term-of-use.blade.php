@php
    $assets = ['animation'];
@endphp

<x-app-layout :assets="$assets">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header pb-4">
                    <div class="header-title">
                        <h4 class="card-title mb-1">Điều khoản sử dụng</h4>
                        <p class="mb-0 text-muted">Trang nội dung tĩnh phục vụ dựng giao diện dashboard.</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="fw-semibold mb-2">1. Mục đích sử dụng</h6>
                        <p class="text-muted mb-0">
                            Giao diện này đang được dùng để trình bày luồng quản trị và lễ tân ở chế độ UI-only.
                            Các form và bảng biểu chỉ mô phỏng cách hiển thị, chưa ràng buộc với backend nghiệp vụ.
                        </p>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-semibold mb-2">2. Dữ liệu hiển thị</h6>
                        <p class="text-muted mb-0">
                            Toàn bộ số liệu, danh sách và trạng thái trên các trang quản trị hiện là dữ liệu mẫu được
                            dựng trực tiếp trong Blade để phục vụ hoàn thiện bố cục và trải nghiệm giao diện.
                        </p>
                    </div>

                    <div>
                        <h6 class="fw-semibold mb-2">3. Phạm vi hiện tại</h6>
                        <p class="text-muted mb-0">
                            Trang này không triển khai xác thực, ghi dữ liệu hay xử lý nghiệp vụ thật. Khi cần tích hợp
                            hệ thống sau này, bạn có thể thay dữ liệu Blade bằng API hoặc backend tương ứng.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
