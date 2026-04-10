<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    // 1. TaiKhoan
    Schema::create('TaiKhoan', function (Blueprint $table) {
        $table->id('MaTK'); // unsignedBigInteger Primary Key
        $table->string('MatKhau', 255);
        $table->integer('LoaiTaiKhoan'); // 0: Khách hàng, 1: Nhân viên, 2: Quản lý
        $table->string('Email', 100)->unique();
        $table->integer('TrangThai')->default(1); // 1: Hoạt động
        $table->timestamps();
    });

    // 2. NhanVien
    Schema::create('NhanVien', function (Blueprint $table) {
        $table->id('MaNV');
        $table->string('TenNV', 100);
        $table->unsignedBigInteger('MaTK')->unique(); // Phải khớp kiểu với MaTK bên TaiKhoan[cite: 3]
        $table->foreign('MaTK')->references('MaTK')->on('TaiKhoan')->onDelete('cascade');
    });

    // 3. KhachHang
    Schema::create('KhachHang', function (Blueprint $table) {
        $table->id('MaKH');
        $table->unsignedBigInteger('MaTK')->unique()->nullable();
        $table->string('TenKH', 100);
        $table->string('DiaChi', 200);
        $table->string('SoDienThoai', 15)->unique();
        $table->string('CCCD', 20)->unique();
        $table->date('NgaySinh');
        $table->integer('GioiTinh'); // 0: Nữ, 1: Nam, 2: Khác[cite: 3]
        $table->integer('DIEM')->default(0);
        $table->foreign('MaTK')->references('MaTK')->on('TaiKhoan')->onDelete('set null');
    });

    // 4. LoaiPhong
    Schema::create('LoaiPhong', function (Blueprint $table) {
        $table->id('MaLoaiPhong');
        $table->string('TenLoaiPhong', 50);
        $table->string('Mota', 200)->nullable();
        $table->integer('SoNguoiToiDa')->default(1);
    });

    // 5. TienNghi
    Schema::create('TienNghi', function (Blueprint $table) {
        $table->id('MaTienNghi');
        $table->string('TenTienNghi', 100);
    });

    // 6. TienNghiPhong (Sửa lỗi khai báo Primary Key)
    Schema::create('TienNghiPhong', function (Blueprint $table) {
        $table->unsignedBigInteger('MaLoaiPhong');
        $table->unsignedBigInteger('MaTienNghi');
        $table->primary(['MaLoaiPhong', 'MaTienNghi']); //[cite: 3]
        $table->foreign('MaLoaiPhong')->references('MaLoaiPhong')->on('LoaiPhong')->onDelete('cascade');
        $table->foreign('MaTienNghi')->references('MaTienNghi')->on('TienNghi')->onDelete('cascade');
    });

    // 7. BangGia
    Schema::create('BangGia', function (Blueprint $table) {
        $table->unsignedBigInteger('MaLoaiPhong');
        $table->integer('Mua'); // 0: Thấp điểm, 1: Cao điểm[cite: 3]
        $table->decimal('GiaPhong', 18, 2);
        $table->primary(['MaLoaiPhong', 'Mua']);
        $table->foreign('MaLoaiPhong')->references('MaLoaiPhong')->on('LoaiPhong')->onDelete('cascade');
    });

    // 8. Phong
    Schema::create('Phong', function (Blueprint $table) {
        $table->id('MaPhong');
        $table->string('SoPhong', 10)->unique();
        $table->unsignedBigInteger('MaLoaiPhong');
        $table->integer('TinhTrang')->default(0); // 0: Trống, 1: Đã đặt...[cite: 3]
        $table->foreign('MaLoaiPhong')->references('MaLoaiPhong')->on('LoaiPhong')->onDelete('restrict');
    });

    // 9. DatPhong
    Schema::create('DatPhong', function (Blueprint $table) {
        $table->id('MaDatPhong');
        $table->unsignedBigInteger('MaKH');
        $table->date('NgayDat');
        $table->date('NgayNhanPhong');
        $table->date('NgayTraPhong');
        $table->integer('SoLuong');
        $table->integer('TinhTrang'); // 0: Đã đặt, 1: Đang sử dụng...[cite: 3]
        $table->foreign('MaKH')->references('MaKH')->on('KhachHang');
    });

    // 10. ChiTietDatPhong
    Schema::create('ChiTietDatPhong', function (Blueprint $table) {
        $table->id('MaCTDP');
        $table->unsignedBigInteger('MaDatPhong');
        $table->unsignedBigInteger('MaPhong');
        $table->foreign('MaDatPhong')->references('MaDatPhong')->on('DatPhong')->onDelete('cascade');
        $table->foreign('MaPhong')->references('MaPhong')->on('Phong');
    });

    // 11. DichVu
    Schema::create('DichVu', function (Blueprint $table) {
        $table->id('MaDV');
        $table->string('TenDV', 100);
        $table->decimal('GiaDV', 18, 2);
        $table->integer('LoaiDV'); //[cite: 3]
    });

    // 12. SuDungDichVu
    Schema::create('SuDungDichVu', function (Blueprint $table) {
        $table->id('MaSuDung');
        $table->unsignedBigInteger('MaDatPhong');
        $table->unsignedBigInteger('MaDV');
        $table->integer('SoLuong');
        $table->timestamp('ThoiGian')->useCurrent();
        $table->foreign('MaDatPhong')->references('MaDatPhong')->on('DatPhong')->onDelete('cascade');
        $table->foreign('MaDV')->references('MaDV')->on('DichVu');
    });

    // 13. DenBuHuHong
    Schema::create('DenBuHuHong', function (Blueprint $table) {
        $table->id('MaDenBu');
        $table->unsignedBigInteger('MaDatPhong')->unique();
        $table->string('MoTa', 200)->nullable();
        $table->decimal('TienDenBu', 18, 2);
        $table->foreign('MaDatPhong')->references('MaDatPhong')->on('DatPhong')->onDelete('cascade');
    });

    // 14. KhuyenMai
    Schema::create('KhuyenMai', function (Blueprint $table) {
        $table->id('MaKM');
        $table->string('TenKM', 100);
        $table->string('MoTa', 200)->nullable();
        $table->integer('Diem')->nullable();
        $table->date('NgayBatDau');
        $table->date('NgayKetThuc');
        $table->decimal('PhanTramGiamGia', 5, 2);
    });

    // 15. KhoKhuyenMai
    Schema::create('KhoKhuyenMai', function (Blueprint $table) {
        $table->unsignedBigInteger('MaKM');
        $table->unsignedBigInteger('MaKH');
        $table->integer('TrangThai')->default(0); // 0: Chưa sử dụng[cite: 3]
        $table->primary(['MaKM', 'MaKH']);
        $table->foreign('MaKM')->references('MaKM')->on('KhuyenMai')->onDelete('cascade');
        $table->foreign('MaKH')->references('MaKH')->on('KhachHang')->onDelete('cascade');
    });

    // 16. HoaDon
    Schema::create('HoaDon', function (Blueprint $table) {
        $table->id('MaHD');
        $table->unsignedBigInteger('MaDatPhong')->unique();
        $table->date('NgayLapHD');
        $table->unsignedBigInteger('MaKM')->nullable();
        $table->decimal('TongTien', 18, 2);
        $table->unsignedBigInteger('MaNV')->nullable();
        $table->integer('TrangThai'); // 0: Chưa thanh toán, 1: Đã thanh toán[cite: 3]
        $table->decimal('DaThanhToan', 18, 2)->default(0);
        $table->foreign('MaDatPhong')->references('MaDatPhong')->on('DatPhong');
        $table->foreign('MaNV')->references('MaNV')->on('NhanVien')->onDelete('set null');
        $table->foreign('MaKM')->references('MaKM')->on('KhuyenMai')->onDelete('set null');
    });

    // 17. ChiTietHoaDon
    Schema::create('ChiTietHoaDon', function (Blueprint $table) {
        $table->id('MaCTHD');
        $table->unsignedBigInteger('MaHD');
        $table->unsignedBigInteger('MaLoaiPhong')->nullable();
        $table->unsignedBigInteger('MaSuDung')->nullable();
        $table->unsignedBigInteger('MaDenBu')->nullable();
        $table->string('MoTa', 200)->nullable();
        $table->integer('SoLuong');
        $table->decimal('DonGia', 18, 2);
        $table->foreign('MaHD')->references('MaHD')->on('HoaDon')->onDelete('cascade');
        $table->foreign('MaLoaiPhong')->references('MaLoaiPhong')->on('LoaiPhong');
        $table->foreign('MaSuDung')->references('MaSuDung')->on('SuDungDichVu');
        $table->foreign('MaDenBu')->references('MaDenBu')->on('DenBuHuHong');
    });

    // 18. ThanhToan
    Schema::create('ThanhToan', function (Blueprint $table) {
        $table->id('MaTT');
        $table->unsignedBigInteger('MaHD');
        $table->decimal('SoTien', 18, 2);
        $table->integer('PhuongThuc'); // 1:The, 2: QRCode[cite: 3]
        $table->integer('LoaiThanhToan'); // 0: đặt cọc, 1: thanh toán checkout[cite: 3]
        $table->timestamp('NgayThanhToan')->useCurrent();
        $table->foreign('MaHD')->references('MaHD')->on('HoaDon')->onDelete('cascade');
    });

    // 19. DanhGia
    Schema::create('DanhGia', function (Blueprint $table) {
        $table->id('MaDG');
        $table->unsignedBigInteger('MaDatPhong')->unique();
        $table->integer('Sao');
        $table->string('MoTa', 200)->nullable();
        $table->date('NgayDanhGia');
        $table->foreign('MaDatPhong')->references('MaDatPhong')->on('DatPhong')->onDelete('cascade');
    });

    // 20. Hinh
    Schema::create('Hinh', function (Blueprint $table) {
        $table->id();
        $table->string('Url', 255); // Tăng giới hạn URL để lưu trữ linh hoạt hơn[cite: 3]
        $table->unsignedBigInteger('MaLoaiPhong')->nullable();
        $table->unsignedBigInteger('MaDV')->nullable();
        $table->foreign('MaLoaiPhong')->references('MaLoaiPhong')->on('LoaiPhong')->onDelete('cascade');
        $table->foreign('MaDV')->references('MaDV')->on('DichVu')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_management_system');
    }
};
