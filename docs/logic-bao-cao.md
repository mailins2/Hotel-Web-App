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

## Logic biểu đồ Doanh thu dịch vụ

Biểu đồ **Doanh thu dịch vụ** lấy dữ liệu từ bảng `SuDungDichVu` và liên kết sang bảng `DichVu`.

Nguồn dữ liệu:

- `SuDungDichVu.MaDV`: mã dịch vụ đã sử dụng.
- `SuDungDichVu.SoLuong`: số lượng dịch vụ đã bán.
- `SuDungDichVu.ThoiGian`: thời điểm sử dụng dịch vụ.
- `DichVu.TenDV`: tên dịch vụ.
- `DichVu.GiaDV`: đơn giá dịch vụ.
- `DichVu.LoaiDV`: loại dịch vụ.

Mapping loại dịch vụ:

- `LoaiDV = 1`: Dịch vụ ăn uống
- `LoaiDV = 2`: Dịch vụ phòng
- `LoaiDV = 3`: Dịch vụ giải trí

### Công thức doanh thu

Doanh thu từng dòng sử dụng dịch vụ được tính bằng:

```php
doanh_thu = SuDungDichVu.SoLuong * DichVu.GiaDV
```

Trong `routes/web.php`, dữ liệu được chuẩn hóa thành từng item:

```php
[
    'date' => Carbon::parse($usage->ThoiGian)->toDateString(),
    'type' => (string) $service->LoaiDV,
    'type_label' => $service->LoaiDVText,
    'service_id' => $service->MaDV,
    'service_name' => $service->TenDV,
    'quantity' => $usage->SoLuong,
    'unit_price' => $service->GiaDV,
    'revenue' => $usage->SoLuong * $service->GiaDV,
]
```

Các item này được truyền sang view qua biến:

```php
'serviceRevenueItems' => $serviceRevenueItems
```

### Thời gian mặc định

Khoảng thời gian của card **Doanh thu dịch vụ** mặc định là ngày hôm nay:

```php
$serviceRevenueToday = Carbon::today()->toDateString();
```

Trong view, cả ô `Từ ngày` và `Đến ngày` đều dùng giá trị này để mặc định lọc trong ngày hiện tại.

### Cách biểu đồ thay đổi theo lựa chọn

Phần JavaScript trong `report.blade.php` xử lý dữ liệu đã được backend truyền sẵn, chưa gọi thêm API.

Khi chọn:

- `Tất cả`: biểu đồ gom doanh thu theo từng loại dịch vụ.
- `Dịch vụ ăn uống`: biểu đồ hiển thị chi tiết từng dịch vụ thuộc loại ăn uống, ví dụ bún thái, bánh mì...
- `Dịch vụ phòng`: biểu đồ hiển thị chi tiết từng dịch vụ phòng.
- `Dịch vụ giải trí`: biểu đồ hiển thị chi tiết từng dịch vụ giải trí.

Khi đổi ngày hoặc loại dịch vụ, JavaScript lọc lại mảng `serviceRevenueItems`, gom nhóm lại, rồi vẽ lại pie chart bằng SVG.

### Tooltip khi hover

Mỗi lát màu trên biểu đồ có dữ liệu:

- Tên loại dịch vụ hoặc tên dịch vụ chi tiết.
- Tổng số lượng bán.
- Tổng doanh thu.

Khi hover vào từng lát màu, tooltip hiển thị:

```text
Tên dịch vụ / loại dịch vụ
Số lượng bán
Doanh thu
```

### Ghi chú màu

Legend bên phải biểu đồ không còn hiển thị phần trăm cố định. Mỗi dòng hiển thị:

- Màu tương ứng với lát biểu đồ.
- Tên loại dịch vụ hoặc tên dịch vụ.
- Tổng doanh thu đã tính bằng `SoLuong * GiaDV`.

Nếu không có dữ liệu trong khoảng thời gian đang chọn, biểu đồ hiển thị trạng thái rỗng và ghi chú báo chưa có doanh thu dịch vụ.

## Logic biểu đồ Tình trạng phòng theo loại phòng

Biểu đồ **Tình trạng phòng** lấy dữ liệu từ bảng `Phong` và liên kết sang bảng `LoaiPhong`.

Nguồn dữ liệu:

- `Phong.MaPhong`: mã phòng.
- `Phong.SoPhong`: số phòng.
- `Phong.MaLoaiPhong`: mã loại phòng.
- `Phong.TinhTrang`: trạng thái hiện tại của phòng.
- `LoaiPhong.TenLoaiPhong`: tên loại phòng.

Trong `routes/web.php`, hệ thống lấy danh sách phòng kèm loại phòng:

```php
$roomStatusItems = Phong::with('loaiPhong')
    ->get()
    ->map(fn (Phong $room) => [
        'room_id' => $room->MaPhong,
        'room_number' => $room->SoPhong,
        'room_type_id' => $room->MaLoaiPhong ? (string) $room->MaLoaiPhong : '',
        'room_type_name' => $room->loaiPhong?->TenLoaiPhong ?? 'Chưa phân loại',
        'status' => (int) ($room->TinhTrang ?? 0),
    ])
    ->values();
```

Danh sách loại phòng cho select được lấy từ bảng `LoaiPhong`:

```php
$roomTypeOptions = LoaiPhong::orderBy('TenLoaiPhong')
    ->get(['MaLoaiPhong', 'TenLoaiPhong']);
```

Hai biến này được truyền vào view:

```php
'roomStatusItems' => $roomStatusItems,
'roomTypeOptions' => $roomTypeOptions,
```

### Mapping trạng thái phòng

JavaScript trong `report.blade.php` dùng mapping:

```js
const roomStatusConfig = [
    { status: 0, label: 'Trống', color: '#F75270' },
    { status: 1, label: 'Đã đặt', color: '#FAE251' },
    { status: 2, label: 'Đang sử dụng', color: '#8CC0EB' },
    { status: 3, label: 'Đang dọn dẹp', color: '#5DD3B6' }
];
```

### Cách lọc theo loại phòng

Select trong card có các lựa chọn:

- `Tất cả`: thống kê trạng thái của toàn bộ phòng.
- Từng loại phòng cụ thể lấy từ bảng `LoaiPhong`.

Khi người dùng chọn một loại phòng, JavaScript lọc lại mảng `roomStatusItems`:

```js
const filteredRooms = roomStatusItems.filter(function (room) {
    return selectedRoomType === 'all' || String(room.room_type_id) === selectedRoomType;
});
```

Sau đó hệ thống đếm số lượng phòng theo từng trạng thái:

```js
const groups = roomStatusConfig.map(function (config) {
    return {
        name: config.label,
        color: config.color,
        value: filteredRooms.filter(function (room) {
            return Number(room.status) === config.status;
        }).length
    };
});
```

### Cách hiển thị biểu đồ

Biểu đồ được render bằng SVG pie chart trong view.

Mỗi lát màu đại diện cho một trạng thái phòng. Legend bên phải hiển thị:

- Màu trạng thái.
- Tên trạng thái.
- Số lượng phòng thuộc trạng thái đó.

Nếu chọn một loại phòng nhưng loại đó chưa có phòng, legend sẽ hiển thị thông báo:

```text
Chưa có phòng thuộc loại phòng này
```

Logic này xử lý hoàn toàn ở trang báo cáo dựa trên dữ liệu server đã truyền lúc load trang. Khi trạng thái phòng hoặc loại phòng thay đổi trong database, người dùng cần tải lại trang để nhận dữ liệu mới nhất.

## Logic tính công suất phòng

Công suất phòng dùng để đo tỷ lệ phòng đã bán/sử dụng so với tổng số phòng khách sạn có thể đưa vào kinh doanh trong một ngày hoặc một khoảng thời gian.

Công thức tổng quát:

```text
Công suất phòng (%) = (Số phòng bán ra / Số phòng có khả năng đáp ứng) * 100
```

Trong dữ liệu hiện tại của hệ thống, công thức nên được hiểu như sau:

```text
Công suất phòng (%) =
    (Số dòng ChiTietDatPhong đã check-in trong ngày hoặc trong kỳ
    / Tổng số phòng trong bảng Phong có thể kinh doanh trong ngày hoặc trong kỳ)
    * 100
```

### Tử số: số phòng bán ra

Nguồn dữ liệu lấy từ bảng `ChiTietDatPhong`.

Lý do dùng `ChiTietDatPhong` là vì mỗi dòng trong bảng này đại diện cho một phòng cụ thể nằm trong một mã đặt phòng. Nếu một booking đặt 3 phòng thì sẽ có 3 dòng chi tiết đặt phòng, vì vậy đếm trên bảng này sẽ đúng số lượng phòng thực tế đã bán ra.

Trạng thái được dùng để xác định phòng đã bán ra là:

```php
ChiTietDatPhong::CHECKED_IN
```

Trong model hiện tại:

```php
const CHECKED_IN = 1;
```

Khi tính cho một ngày cụ thể, ví dụ ngày hôm nay, cần kết hợp thêm khoảng ngày ở bảng `DatPhong`:

```php
$date = now()->toDateString();

$roomsSold = ChiTietDatPhong::where('TrangThai', ChiTietDatPhong::CHECKED_IN)
    ->whereHas('datPhong', function ($query) use ($date) {
        $query->whereDate('NgayNhanPhong', '<=', $date)
              ->whereDate('NgayTraPhong', '>', $date);
    })
    ->count();
```

Ý nghĩa:

- `TrangThai = CHECKED_IN`: chỉ tính phòng khách đã nhận phòng.
- `NgayNhanPhong <= ngày xem`: booking đã bắt đầu trước hoặc đúng ngày đang xem.
- `NgayTraPhong > ngày xem`: ngày đang xem vẫn còn nằm trong thời gian lưu trú.

Dùng `NgayTraPhong > ngày xem` vì ngày trả phòng thường không được tính là một đêm lưu trú mới. Nếu nghiệp vụ muốn tính cả ngày checkout là vẫn còn sử dụng phòng cho đến lúc trả phòng, điều kiện này có thể đổi thành `NgayTraPhong >= ngày xem`.

### Mẫu số: số phòng có khả năng đáp ứng

Nguồn dữ liệu lấy từ bảng `Phong`.

Hiện tại hệ thống đang dùng các trạng thái phòng:

```text
0: Trống
1: Đã đặt
2: Đang sử dụng
3: Đang dọn dẹp
```

Vì chưa có trạng thái riêng cho phòng bảo trì, ngưng kinh doanh hoặc khóa bán, nên mẫu số nên lấy toàn bộ phòng trong bảng `Phong`:

```php
$roomCapacity = Phong::count();
```

Lý do không chỉ lấy phòng `Trống` là vì mẫu số của công suất phòng không phải số phòng còn trống, mà là tổng năng lực phòng có thể kinh doanh. Các phòng `Đã đặt`, `Đang sử dụng` và `Đang dọn dẹp` vẫn thuộc năng lực kinh doanh của khách sạn:

- `Trống`: phòng có thể bán.
- `Đã đặt`: phòng đã được giữ hoặc bán cho khách.
- `Đang sử dụng`: phòng đã bán thành công và đang tạo công suất.
- `Đang dọn dẹp`: trạng thái vận hành tạm thời, không phải phòng bị loại khỏi kinh doanh dài hạn.

Nếu sau này hệ thống bổ sung trạng thái như `Bảo trì`, `Ngưng kinh doanh`, `Khóa bán`, thì các trạng thái đó nên bị loại khỏi mẫu số:

```php
$roomCapacity = Phong::whereNotIn('TinhTrang', [
    $maintenanceStatus,
    $inactiveStatus,
])->count();
```

### Tính theo loại phòng

Khi người dùng chọn một loại phòng cụ thể, cả tử số và mẫu số đều cần lọc theo `MaLoaiPhong`.

Tử số:

```php
$roomsSold = ChiTietDatPhong::where('TrangThai', ChiTietDatPhong::CHECKED_IN)
    ->whereHas('phong', function ($query) use ($roomTypeId) {
        $query->where('MaLoaiPhong', $roomTypeId);
    })
    ->whereHas('datPhong', function ($query) use ($date) {
        $query->whereDate('NgayNhanPhong', '<=', $date)
              ->whereDate('NgayTraPhong', '>', $date);
    })
    ->count();
```

Mẫu số:

```php
$roomCapacity = Phong::where('MaLoaiPhong', $roomTypeId)->count();
```

Sau đó tính tỷ lệ:

```php
$occupancyRate = $roomCapacity > 0
    ? ($roomsSold / $roomCapacity) * 100
    : 0;
```

### Tính theo khoảng thời gian

Nếu xem theo nhiều ngày, nên tính theo đơn vị `phòng-đêm`.

Khi có chọn `Từ ngày` và `Đến ngày`, không nên lấy:

```text
Tổng số phòng đã check-in trong kỳ / Tổng số phòng
```

Cách này dễ sai vì cùng một phòng có thể được bán nhiều đêm trong kỳ. Ví dụ một phòng được khách ở 3 đêm thì phải tính là `3 phòng-đêm`, không phải chỉ là `1 phòng`.

Công thức đúng cho khoảng thời gian:

```text
Công suất kỳ (%) =
    (Tổng phòng-đêm đã bán trong kỳ / Tổng phòng-đêm có khả năng đáp ứng trong kỳ)
    * 100
```

Trong đó:

```text
Tổng phòng-đêm có khả năng đáp ứng trong kỳ =
    Số phòng có khả năng kinh doanh * Số ngày trong kỳ
```

Ví dụ khách sạn có 10 phòng và xem trong 30 ngày:

```text
Số phòng có khả năng đáp ứng trong kỳ = 10 * 30 = 300 phòng-đêm
```

Nếu tổng số phòng-đêm đã bán trong kỳ là 240:

```text
Công suất kỳ = (240 / 300) * 100 = 80%
```

### Cách tính tổng phòng-đêm đã bán

Tử số là tổng số phòng-đêm đã bán ra trong kỳ. Mỗi dòng `ChiTietDatPhong` có `TrangThai = CHECKED_IN` được tính theo số ngày lưu trú giao với khoảng thời gian đang xem.

Ví dụ đang xem từ `2026-05-01` đến `2026-05-31`.

Một phòng có booking:

```text
NgayNhanPhong = 2026-05-10
NgayTraPhong = 2026-05-13
```

Booking này tính là:

```text
3 phòng-đêm
```

Vì khách ở các đêm:

```text
10/05, 11/05, 12/05
```

Ngày `13/05` là ngày trả phòng nên thường không tính là một đêm mới.

Nếu booking bị giao với ranh giới khoảng xem, chỉ tính phần nằm trong khoảng đang xem.

Ví dụ xem từ `2026-05-01` đến `2026-05-31`, booking:

```text
NgayNhanPhong = 2026-04-29
NgayTraPhong = 2026-05-03
```

Chỉ tính các đêm nằm trong tháng 5:

```text
01/05, 02/05 = 2 phòng-đêm
```

Logic tính giao ngày:

```text
Ngày bắt đầu tính = ngày lớn hơn giữa NgayNhanPhong và Từ ngày
Ngày kết thúc tính = ngày nhỏ hơn giữa NgayTraPhong và ngày sau Đến ngày
Số phòng-đêm = số ngày giữa Ngày bắt đầu tính và Ngày kết thúc tính
```

Ví dụ code minh họa:

```php
$fromDate = Carbon::parse($fromDate)->startOfDay();
$toDate = Carbon::parse($toDate)->startOfDay();
$periodEndExclusive = $toDate->copy()->addDay();

$soldRoomNights = ChiTietDatPhong::with('datPhong')
    ->where('TrangThai', ChiTietDatPhong::CHECKED_IN)
    ->whereHas('datPhong', function ($query) use ($fromDate, $periodEndExclusive) {
        $query->whereDate('NgayNhanPhong', '<', $periodEndExclusive)
              ->whereDate('NgayTraPhong', '>', $fromDate);
    })
    ->get()
    ->sum(function ($detail) use ($fromDate, $periodEndExclusive) {
        $checkIn = Carbon::parse($detail->datPhong->NgayNhanPhong)->startOfDay();
        $checkOut = Carbon::parse($detail->datPhong->NgayTraPhong)->startOfDay();

        $start = $checkIn->greaterThan($fromDate) ? $checkIn : $fromDate;
        $end = $checkOut->lessThan($periodEndExclusive) ? $checkOut : $periodEndExclusive;

        return max(0, $start->diffInDays($end));
    });
```

### Cách tính mẫu số theo thời gian

Mẫu số cũng phải nhân theo số ngày trong kỳ:

```php
$daysInPeriod = $fromDate->diffInDays($periodEndExclusive);
$roomCapacity = Phong::count();
$capacityRoomNights = $roomCapacity * $daysInPeriod;
```

Sau đó tính công suất:

```php
$occupancyRate = $capacityRoomNights > 0
    ? ($soldRoomNights / $capacityRoomNights) * 100
    : 0;
```

Nếu lọc theo loại phòng, mẫu số cũng cần lọc theo `MaLoaiPhong`:

```php
$roomCapacity = Phong::where('MaLoaiPhong', $roomTypeId)->count();
```

Tử số cũng lọc thêm qua quan hệ `phong`:

```php
->whereHas('phong', function ($query) use ($roomTypeId) {
    $query->where('MaLoaiPhong', $roomTypeId);
})
```

Với UI hiện tại, nếu chỉ xem theo một ngày thì dùng công thức ngày. Nếu chọn khoảng thời gian từ ngày đến ngày, nên chuyển sang công thức phòng-đêm để kết quả không bị sai khi booking kéo dài qua nhiều ngày.

## Cách triển khai biểu đồ công suất phòng trên trang báo cáo

Biểu đồ chính trong `resources/views/hotel-management/report.blade.php` hiện dùng cùng khu vực giao diện với card **Thống kê công suất phòng**.

Các thành phần UI chính:

- `data-main-report-type`: select chọn nội dung xem, gồm `Công suất phòng` và `Doanh thu`.
- `data-main-report-detail`: select chi tiết. Khi chọn `Công suất phòng`, select này đổi thành danh sách loại phòng.
- `data-main-report-from`: ngày bắt đầu.
- `data-main-report-to`: ngày kết thúc.
- `data-main-report-chart`: SVG dùng để render các cột biểu đồ.
- `data-main-report-xaxis`: trục X.
- `data-main-report-yaxis`: trục Y.
- `data-main-report-tooltip`: tooltip khi hover vào từng cột.

### Dữ liệu backend truyền sang view

Trong `routes/web.php`, route báo cáo chuẩn bị thêm 2 mảng dữ liệu cho công suất phòng:

```php
'roomCapacityItems' => $roomCapacityItems,
'roomOccupancyItems' => $roomOccupancyItems,
```

#### `roomCapacityItems`

Mảng này dùng để tính mẫu số của công suất phòng.

Nguồn dữ liệu:

```php
Phong::select('MaPhong', 'MaLoaiPhong')->get()
```

Mỗi phần tử có dạng:

```php
[
    'room_id' => $room->MaPhong,
    'room_type_id' => $room->MaLoaiPhong ? (string) $room->MaLoaiPhong : '',
]
```

Ý nghĩa:

- Mỗi dòng là một phòng có thể đưa vào thống kê.
- Khi xem tất cả loại phòng, mẫu số là tổng số phần tử trong `roomCapacityItems`.
- Khi lọc theo loại phòng, mẫu số là số phần tử có `room_type_id` đúng với loại phòng được chọn.

Hiện tại hệ thống chưa có trạng thái bảo trì/ngưng kinh doanh, nên mảng này lấy toàn bộ phòng trong bảng `Phong`.

#### `roomOccupancyItems`

Mảng này dùng để tính tử số của công suất phòng và tiền phòng hiển thị trên trục Y/tooltip.

Nguồn dữ liệu:

```php
ChiTietDatPhong::with(['datPhong', 'phong.loaiPhong.khuyenMai'])
    ->where('TrangThai', ChiTietDatPhong::CHECKED_IN)
```

Mỗi phần tử có dạng:

```php
[
    'detail_id' => $detail->MaCTDP,
    'room_id' => $detail->MaPhong,
    'room_type_id' => $room?->MaLoaiPhong ? (string) $room->MaLoaiPhong : '',
    'room_type_name' => $roomType?->TenLoaiPhong ?? 'Loại phòng',
    'check_in' => Carbon::parse($booking->NgayNhanPhong)->toDateString(),
    'check_out' => Carbon::parse($booking->NgayTraPhong)->toDateString(),
    'nightly_price' => (float) $roomType->giaSauKhuyenMai($booking->NgayNhanPhong),
]
```

Ý nghĩa:

- `TrangThai = CHECKED_IN`: chỉ lấy các phòng đã nhận phòng.
- `check_in` và `check_out`: dùng để tính số đêm giao với khoảng thời gian đang xem.
- `nightly_price`: giá phòng sau khuyến mãi tại thời điểm nhận phòng, dùng để tính tiền phòng.
- `room_type_id`: dùng để lọc theo loại phòng khi người dùng chọn chi tiết.

### Cách UI đổi danh sách chi tiết

Khi người dùng chọn `Công suất phòng`, JavaScript gọi hàm:

```js
renderMainReportDetailOptions()
```

Nếu `data-main-report-type` đang là `occupancy`, select chi tiết được đổi thành:

```text
Tất cả loại phòng
Danh sách loại phòng từ bảng LoaiPhong
```

Danh sách loại phòng được backend truyền sang view qua biến `roomTypeOptions`, sau đó JavaScript chuẩn hóa thành:

```js
const roomTypeChoices = [
    { value: '1', label: 'Tên loại phòng' },
    ...
];
```

Khi người dùng đổi loại phòng, ngày bắt đầu hoặc ngày kết thúc, hàm `renderMainReportChart()` chạy lại để tính và vẽ biểu đồ mới.

### Cách chia cột theo ngày hoặc tháng

Hàm:

```js
buildMainReportBuckets(fromDate, toDate)
```

dùng để tạo danh sách cột trên trục X.

Nếu khoảng thời gian từ `Từ ngày` đến `Đến ngày` nhỏ hơn hoặc bằng 31 ngày, biểu đồ hiển thị từng ngày:

```text
22/05
23/05
24/05
...
```

Nếu khoảng thời gian dài hơn 31 ngày, biểu đồ gom theo tháng:

```text
Tháng 1
Tháng 2
Tháng 3
...
```

Mỗi bucket có các thông tin:

```js
{
    label: '22/05',
    tooltipLabel: '22/05/2026',
    start: Date,
    end: Date
}
```

`start` và `end` được dùng để tính số phòng-đêm giao với bucket đó.

### Cách tính số phòng bán, công suất và tiền phòng cho mỗi cột

Hàm:

```js
aggregateMainOccupancyBuckets(buckets, roomTypeId)
```

duyệt từng cột và tính:

- `soldRooms`: tổng phòng-đêm đã bán.
- `occupancyRate`: công suất phòng.
- `roomRevenue`: tiền phòng.

Mẫu số mỗi cột:

```js
capacityRoomNights = roomCapacity * daysInBucket
```

Trong đó:

- `roomCapacity`: số phòng có khả năng đáp ứng.
- `daysInBucket`: số ngày trong cột hiện tại.

Nếu xem theo ngày, `daysInBucket = 1`.

Nếu xem theo tháng, `daysInBucket` là số ngày thuộc tháng đó nằm trong khoảng đang xem.

Tử số mỗi cột:

```js
soldRoomNights += overlapDays
```

Trong đó `overlapDays` là số ngày giao giữa:

- khoảng lưu trú của phòng: `check_in` đến `check_out`
- khoảng thời gian của cột: `bucket.start` đến `bucket.end`

Tiền phòng mỗi cột:

```js
roomRevenue += overlapDays * nightly_price
```

Công suất mỗi cột:

```js
occupancyRate = capacityRoomNights > 0
    ? soldRoomNights / capacityRoomNights * 100
    : 0
```

### Cách lọc theo loại phòng

Khi người dùng chọn một loại phòng cụ thể ở select chi tiết, JavaScript lọc cả mẫu số và tử số theo `room_type_id`.

Mẫu số:

```js
roomCapacityItems.filter(function (room) {
    return roomTypeId === 'all' || String(room.room_type_id) === roomTypeId;
}).length
```

Tử số:

```js
if (roomTypeId !== 'all' && String(item.room_type_id) !== roomTypeId) {
    return;
}
```

Vì vậy khi chọn một loại phòng, biểu đồ chỉ hiển thị công suất, số phòng bán và tiền phòng của riêng loại phòng đó.

### Cách vẽ biểu đồ và tooltip

Biểu đồ được render bằng SVG trong `data-main-report-chart`.

Mỗi cột là một thẻ:

```html
<rect data-main-chart-bar="true">
```

Chiều cao cột đang dựa trên `roomRevenue`, nên trục Y hiển thị tiền phòng. Cột nào có tiền phòng cao hơn thì cao hơn.

Tooltip được hiển thị khi hover vào cột bằng hàm:

```js
showMainTooltip(event, item)
```

Tooltip hiển thị:

```text
Ngày/tháng/năm hoặc tháng/năm
Số phòng được bán
Công suất
Tiền phòng
```

Ví dụ:

```text
22/05/2026
Số phòng được bán: 4
Công suất: 40.0%
Tiền phòng: 3.200.000 VNĐ
```

### Lưu ý về dữ liệu đã checkout

Hiện tại biểu đồ đang tính số phòng bán từ:

```php
ChiTietDatPhong::CHECKED_IN
```

Cách này đúng với yêu cầu hiện tại là tính các phòng đang ở/đã check-in. Tuy nhiên nếu muốn làm báo cáo lịch sử chính xác sau khi khách đã trả phòng, cần cân nhắc tính thêm:

```php
ChiTietDatPhong::CHECKED_OUT
```

Lý do là khi khách checkout, chi tiết đặt phòng có thể chuyển từ `CHECKED_IN` sang `CHECKED_OUT`. Nếu báo cáo tháng chỉ lấy `CHECKED_IN`, các phòng đã ở và đã trả trong tháng có thể không còn được tính vào công suất lịch sử.
