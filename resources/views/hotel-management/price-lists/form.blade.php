<x-app-layout :assets="['animation']">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 pb-4">
                    <div class="header-title">
                        <h4 class="card-title mb-1">{{ request()->routeIs('hotel.price-lists.create') ? 'Thêm bảng giá' : 'Chỉnh sửa bảng giá' }}</h4>
                        <p class="mb-0 text-muted">Biểu mẫu giao diện cho thông tin bảng giá.</p>
                    </div>
                    <a href="{{ route('hotel.price-lists.index') }}" class="btn btn-sm btn-primary" style="padding: 10px;">Quay lại</a>
                </div>
                <div class="card-body">
                    <form class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Mã loại phòng</label>
                            <input type="text" class="form-control" value="1">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mùa</label>
                            <div class="hm-select-wrap">
                                <select class="form-select">
                                    <option>Mùa 1</option>
                                    <option>Mùa 2</option>
                                    <option>Mùa 3</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tên loại phòng</label>
                             <div class="hm-select-wrap">
                                <select class="form-select">
                                    <option>Family</option>
                                    <option>Junior</option>
                                    <option>Deluxe Family</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Giá phòng</label>
                            <input type="text" class="form-control" value="300000">
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="button" class="btn btn-primary" style="padding: 10px 18px;">Lưu</button>
                            <a href="{{ route('hotel.price-lists.index') }}" class="btn btn-light" style="padding: 10px 18px;">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
