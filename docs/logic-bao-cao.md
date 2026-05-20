# Logic card Khách hàng trong báo cáo admin

## Mục tiêu

Card **Khách hàng** trên trang báo cáo admin hiển thị số lượng khách hàng đã từng có trong dữ liệu đặt phòng.

Số này không còn là số cố định trong giao diện. Khi có một mã khách hàng mới tạo đặt phòng, card sẽ tự tăng trong lần tải trang tiếp theo.

## Nguồn dữ liệu

Logic lấy dữ liệu từ bảng `DatPhong`.

Trường được dùng:

- `DatPhong.MaDatPhong`: mã đặt phòng.
- `DatPhong.MaKH`: mã khách hàng gắn với đặt phòng.

Model liên quan:

- `App\Models\DatPhong`
- `App\Models\KhachHang`

Quan hệ đang dùng:

```php
DatPhong::whereHas('khachHang')
```

Quan hệ này đảm bảo chỉ đếm các đặt phòng có `MaKH` trỏ tới một bản ghi khách hàng thật trong bảng `KhachHang`.

## Cách đếm

Trong route render trang báo cáo, hệ thống chạy:

```php
$customerCount = DatPhong::whereNotNull('MaKH')
    ->whereHas('khachHang')
    ->distinct()
    ->count('MaKH');
```

Ý nghĩa:

- `whereNotNull('MaKH')`: bỏ qua đặt phòng chưa có mã khách hàng.
- `whereHas('khachHang')`: chỉ lấy mã khách hàng có thông tin hợp lệ trong bảng `KhachHang`.
- `distinct()->count('MaKH')`: một khách hàng đặt nhiều lần vẫn chỉ được tính một lần.

## Vì sao số tự tăng

Khi một khách hàng mới đặt phòng, hệ thống tạo hoặc gắn `MaKH` vào bản ghi `DatPhong`.

Nếu `MaKH` đó chưa từng xuất hiện trong các đặt phòng trước đó, câu truy vấn `distinct MaKH` sẽ có thêm một giá trị mới. Vì vậy card **Khách hàng** sẽ tăng lên khi trang báo cáo được tải lại.

Nếu một khách hàng cũ đặt thêm phòng, số này không tăng vì `MaKH` đã tồn tại trong tập khách hàng đã đặt phòng.

## Nơi truyền dữ liệu vào giao diện

Hai route đang render cùng trang báo cáo:

- `/admin/dashboard`
- `/hotel/reports`

Cả hai route đều truyền biến:

```php
'customerCount' => $customerCount
```

Trong view `resources/views/hotel-management/report.blade.php`, card hiển thị:

```blade
{{ $customerCount ?? 0 }}
```

Fallback `0` giúp giao diện không lỗi nếu view được mở mà chưa truyền biến.

## Logic card Phòng đang sử dụng và Phòng trống

Hai card này hiển thị số lượng phòng theo trạng thái hiện tại trong bảng `Phong`.

Nguồn dữ liệu:

- Bảng `Phong`
- Trường `Phong.TinhTrang`

Mapping trạng thái phòng đang dùng trong hệ thống:

- `TinhTrang = 0`: Trống
- `TinhTrang = 1`: Đã đặt
- `TinhTrang = 2`: Đang sử dụng
- `TinhTrang = 3`: Đang dọn dẹp

Trong route render trang báo cáo, hệ thống đếm:

```php
$roomUsingCount = Phong::where('TinhTrang', 2)->count();
$roomEmptyCount = Phong::where('TinhTrang', 0)->count();
```

Ý nghĩa:

- `$roomUsingCount`: tổng số phòng có trạng thái **Đang sử dụng**.
- `$roomEmptyCount`: tổng số phòng có trạng thái **Trống**.

Hai biến này được truyền vào cùng view báo cáo:

```php
'roomUsingCount' => $roomUsingCount,
'roomEmptyCount' => $roomEmptyCount,
```

Trong view `resources/views/hotel-management/report.blade.php`, hai card hiển thị:

```blade
{{ $roomUsingCount ?? 0 }}
{{ $roomEmptyCount ?? 0 }}
```

Fallback `0` giúp giao diện vẫn hiển thị an toàn nếu view chưa nhận được biến.

## Cách cập nhật số liệu

Hiện tại logic này cập nhật theo kiểu server-render.

Nghĩa là khi trạng thái phòng trong database thay đổi, người dùng chỉ cần tải lại trang báo cáo. Route sẽ chạy lại truy vấn, đếm lại dữ liệu mới nhất trong bảng `Phong`, rồi render lại số mới lên card.

Chưa dùng API polling hoặc realtime websocket, nên số liệu không tự đổi ngay khi đang đứng yên trên trang.
