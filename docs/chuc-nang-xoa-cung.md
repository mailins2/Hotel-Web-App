# Tóm tắt logic chức năng xóa cứng

Tài liệu này mô tả logic xóa hiện tại cho các bảng chính trong module quản lý khách sạn. Hệ thống không dùng xóa mềm. Khi người dùng bấm biểu tượng xóa ở cột thao tác, giao diện sẽ mở hộp thoại xác nhận. Nếu backend từ chối xóa, giao diện hiển thị thông báo lỗi kèm lý do cụ thể.

## Nguyên tắc chung

- Không dùng `SoftDeletes`, không có thùng rác, không có khôi phục.
- Nếu bản ghi chưa có dữ liệu lịch sử quan trọng thì xóa cứng bằng `delete()`.
- Nếu bản ghi đã được dùng trong nghiệp vụ cần giữ lịch sử thì không xóa, hoặc chuyển sang khóa tài khoản tùy bảng.
- Các kiểm tra điều kiện xóa nằm trong các lớp guard tại `app/Services/Guards`.
- Controller nhận kết quả từ guard, sau đó thực hiện xóa, set null, detach bảng trung gian hoặc khóa tài khoản.

## Tài khoản `TaiKhoan`

Guard: `TaiKhoanDeletionGuard`.

### Trường hợp được xóa cứng

Tài khoản được xóa cứng khi:

- Tài khoản không liên kết với khách hàng có đơn đặt phòng.
- Tài khoản không liên kết với nhân viên đã xử lý hóa đơn.

Khi đó controller gọi:

```php
$taiKhoan->delete();
```

### Trường hợp không xóa cứng mà khóa tài khoản

Nếu tài khoản liên kết với:

- Khách hàng đã có đơn đặt phòng, hoặc
- Nhân viên đã xử lý hóa đơn,

thì hệ thống không xóa tài khoản. Thay vào đó, tài khoản được khóa:

```php
$taiKhoan->update(['TrangThai' => 0]);
```

Lý do: các dữ liệu lịch sử như đặt phòng và hóa đơn cần được giữ nguyên để tra cứu.

## Nhân viên `NhanVien`

Guard: `NhanVienDeletionGuard`.

### Trường hợp được xóa cứng

Nhân viên được xóa cứng khi nhân viên đó chưa xử lý hóa đơn nào.

Trước khi xóa nhân viên, hệ thống gỡ liên kết tài khoản:

```php
TaiKhoan::where('MaNV', $nhanVien->MaNV)->update(['MaNV' => null]);
$nhanVien->delete();
```

Kết quả:

- Bản ghi nhân viên bị xóa cứng.
- Tài khoản liên quan vẫn còn, nhưng không còn trỏ tới nhân viên đó.

### Trường hợp không xóa cứng mà khóa tài khoản

Nếu nhân viên đã xử lý hóa đơn, hệ thống không xóa nhân viên vì hóa đơn cần giữ thông tin người xử lý.

Thay vào đó, tài khoản liên quan bị khóa:

```php
TaiKhoan::where('MaNV', $nhanVien->MaNV)->update(['TrangThai' => 0]);
```

## Khách hàng `KhachHang`

Guard: `KhachHangDeletionGuard`.

### Trường hợp không được xóa

Nếu khách hàng đã có đơn đặt phòng, hệ thống chặn xóa và trả về lý do:

- Khách hàng đã có số lượng đơn đặt phòng tương ứng.

Lý do: đơn đặt phòng là dữ liệu lịch sử, cần giữ khách hàng để đối chiếu.

### Trường hợp được xóa cứng

Nếu khách hàng chưa có đơn đặt phòng, hệ thống xóa theo transaction:

```php
TaiKhoan::where('MaKH', $khachHang->MaKH)->delete();
DB::table('KhoKhuyenMai')->where('MaKH', $khachHang->MaKH)->delete();
$khachHang->delete();
```

Kết quả:

- Tài khoản liên quan bị xóa cứng.
- Các khuyến mãi trong kho của khách hàng bị xóa khỏi `KhoKhuyenMai`.
- Khách hàng bị xóa cứng.

## Loại phòng `LoaiPhong`

Guard: `LoaiPhongDeletionGuard`.

Đây là bảng có nhiều liên kết nên logic xóa có nhiều bước.

### Trường hợp không được xóa

Hệ thống lấy danh sách phòng thuộc loại phòng đó, sau đó kiểm tra:

- Các phòng thuộc loại phòng có xuất hiện trong `ChiTietDatPhong` không.
- Các phòng thuộc loại phòng có xuất hiện trong `LuuTru` không.

Nếu có chi tiết đặt phòng hoặc lưu trú, hệ thống chặn xóa loại phòng và hiển thị lý do.

Ví dụ:

- Loại phòng A có phòng 101.
- Phòng 101 đã nằm trong chi tiết đặt phòng.
- Khi xóa loại phòng A, hệ thống không cho xóa vì phòng thuộc loại này đã có lịch sử đặt phòng.

### Trường hợp loại phòng có tiện nghi nhưng chưa có lịch sử đặt phòng/lưu trú

Nếu loại phòng chỉ có liên kết tiện nghi trong bảng trung gian `TienNghiPhong`, hệ thống vẫn cho xóa.

Khi xóa, hệ thống thực hiện:

```php
$loaiPhong->tienNghis()->detach();
```

Kết quả:

- Các dòng liên kết giữa loại phòng và tiện nghi trong `TienNghiPhong` bị xóa.
- Bản ghi tiện nghi trong bảng `TienNghi` không bị xóa.
- Sau đó loại phòng vẫn tiếp tục được xóa cứng.

### Trường hợp được xóa cứng

Nếu các phòng thuộc loại phòng chưa có lịch sử đặt phòng/lưu trú, hệ thống xóa theo transaction:

```php
DB::table('ChiTietHoaDon')
    ->where('MaLoaiPhong', $loaiPhong->MaLoaiPhong)
    ->update(['MaLoaiPhong' => null]);

DB::table('Hinh')
    ->where('MaLoaiPhong', $loaiPhong->MaLoaiPhong)
    ->delete();

$loaiPhong->tienNghis()->detach();
$loaiPhong->phongs()->delete();
$loaiPhong->delete();
```

Kết quả:

- `ChiTietHoaDon.MaLoaiPhong` được set null nếu có tham chiếu.
- Hình ảnh gắn với loại phòng trong bảng `Hinh` bị xóa.
- Liên kết tiện nghi trong `TienNghiPhong` bị xóa.
- Các phòng con chưa có lịch sử bị xóa cứng.
- Loại phòng bị xóa cứng.

## Phòng `Phong`

Guard: `PhongDeletionGuard`.

### Trường hợp không được xóa

Phòng không được xóa nếu:

- Có dòng trong `ChiTietDatPhong`.
- Có dòng trong `LuuTru`.

Khi đó backend trả về lỗi kèm số lượng liên kết đang chặn.

### Trường hợp được xóa cứng

Nếu phòng chưa từng có chi tiết đặt phòng và chưa có lưu trú, hệ thống xóa cứng:

```php
$phong->delete();
```

## Tiện nghi `TienNghi`

Guard: `TienNghiDeletionGuard`.

### Trường hợp có liên kết với loại phòng

Tiện nghi có thể đang được gắn với nhiều loại phòng qua bảng trung gian `TienNghiPhong`.

Khi xóa tiện nghi, hệ thống không chặn vì đây là quan hệ nhiều-nhiều có thể gỡ liên kết an toàn.

Controller thực hiện:

```php
$tienNghi->loaiPhongs()->detach();
$tienNghi->delete();
```

Kết quả:

- Các dòng liên kết trong `TienNghiPhong` bị xóa.
- Các loại phòng liên quan không bị xóa.
- Tiện nghi bị xóa cứng.

## Dịch vụ `DichVu`

Guard: `DichVuDeletionGuard`.

### Trường hợp không được xóa

Nếu dịch vụ đã có lượt sử dụng trong `SuDungDichVu`, hệ thống chặn xóa.

Lý do: lượt sử dụng dịch vụ là dữ liệu nghiệp vụ liên quan tới đặt phòng/hóa đơn.

### Trường hợp được xóa cứng

Nếu dịch vụ chưa có lượt sử dụng, hệ thống xóa cứng:

```php
$dv->delete();
```

Nếu database có foreign key cascade với bảng hình ảnh `Hinh`, các hình gắn với dịch vụ sẽ được xử lý theo ràng buộc database.

## Khuyến mãi `KhuyenMai`

Guard: `KhuyenMaiDeletionGuard`.

### Trường hợp không được xóa

Khuyến mãi không được xóa nếu còn liên kết với:

- `KhoKhuyenMai`: khuyến mãi đã được phát vào kho của khách hàng.
- `HoaDon`: khuyến mãi đã được dùng hoặc tham chiếu trong hóa đơn.

Khi đó backend trả về lỗi kèm số lượng liên kết.

### Trường hợp được xóa cứng

Nếu khuyến mãi chưa có trong kho khuyến mãi và chưa được hóa đơn tham chiếu, hệ thống xóa cứng:

```php
$khuyenMai->delete();
```

Với các bảng khác có khóa ngoại set null tới khuyến mãi, ví dụ `LoaiPhong.MaKM`, database sẽ xử lý theo ràng buộc `nullOnDelete` nếu khóa ngoại đang tồn tại.

## Tóm tắt nhanh theo bảng

| Bảng | Có liên kết lịch sử | Cách xử lý |
| --- | --- | --- |
| `TaiKhoan` | Khách hàng có đặt phòng hoặc nhân viên có hóa đơn | Khóa tài khoản bằng `TrangThai = 0` |
| `TaiKhoan` | Không có lịch sử cần giữ | Xóa cứng tài khoản |
| `NhanVien` | Đã xử lý hóa đơn | Khóa tài khoản liên quan |
| `NhanVien` | Chưa xử lý hóa đơn | Set `TaiKhoan.MaNV = null`, xóa cứng nhân viên |
| `KhachHang` | Đã có đơn đặt phòng | Chặn xóa |
| `KhachHang` | Chưa có đơn đặt phòng | Xóa tài khoản liên quan, xóa `KhoKhuyenMai`, xóa cứng khách hàng |
| `LoaiPhong` | Phòng con đã có đặt phòng/lưu trú | Chặn xóa |
| `LoaiPhong` | Chỉ có tiện nghi/hình ảnh, phòng con chưa có lịch sử | Xóa hình ảnh, detach tiện nghi, set null chi tiết hóa đơn, xóa phòng con, xóa cứng loại phòng |
| `Phong` | Có chi tiết đặt phòng/lưu trú | Chặn xóa |
| `Phong` | Không có lịch sử | Xóa cứng phòng |
| `TienNghi` | Có gắn với loại phòng | Detach khỏi `TienNghiPhong`, xóa cứng tiện nghi |
| `DichVu` | Đã có lượt sử dụng | Chặn xóa |
| `DichVu` | Chưa có lượt sử dụng | Xóa cứng dịch vụ |
| `KhuyenMai` | Có trong kho khuyến mãi hoặc hóa đơn | Chặn xóa |
| `KhuyenMai` | Không có liên kết chặn | Xóa cứng khuyến mãi |
