# 🏨 Thuật toán Nhận phòng (Check-In) - Peach Valley

## 📌 Tóm tắt
Cho phép nhân viên lễ tân nhận phòng cho khách hàng, lưu thông tin khách vào hệ thống và cập nhật trạng thái phòng + đặt phòng.

---

## 🔄 Luồng Check-In

### 1️⃣ Frontend - Collect Guest Data
**File**: `resources/views/receptionist/check-in-form.blade.php`

#### Form Structure
- **Danh sách đặt phòng chờ**: Hiển thị tất cả booking status=CONFIRMED (1) cho hôm nay
- **Lọc phòng**: Hiển thị chỉ những phòng chưa check-in (khác records trong LuuTru)
- **Form nhập khách**: Cho phép nhập thông tin từng khách:
  - Họ tên (required)
  - Ngày sinh (required, type=date)
  - CCCD (required)
  - Số điện thoại (optional)

#### Validation Logic (Client-side)
```javascript
- Họ tên, CCCD: required & not empty
- Ngày sinh: required & valid date & not in future
- Age calc: Nếu role=child → age < 12, role=adult → age >= 12
- Adult presence: At least 1 người >= 18 tuổi để check-in
```

#### Payload Collection
Hàm `collectVisibleGuestPayload()` collect dữ liệu form thành:
```json
{
  "MaPhong": 5,
  "KhachLuuTru": [
    {
      "TenKhach": "Nguyễn Văn A",
      "NgaySinh": "1990-05-15",
      "CCCD": "123456789012",
      "SoDienThoai": "0912345678"
    },
    {
      "TenKhach": "Nguyễn Thị B",
      "NgaySinh": "2015-08-20",
      "CCCD": "987654321098",
      "SoDienThoai": null
    }
  ]
}
```

#### Submit Handler
```javascript
POST /api/dat-phong/{bookingId}/check-in
Content-Type: application/json
X-CSRF-TOKEN: {token}

Body: {
  "MaPhong": 5,
  "KhachLuuTru": [...]
}
```

---

### 2️⃣ Backend - Process Check-In

**Endpoint**: `POST /api/dat-phong/{id}/check-in`  
**Controller**: `App\Http\Controllers\Api\DatPhongController::checkIn()`

#### Step 1: Validate Request
```php
- MaPhong: required, exists in Phong table (not soft-deleted)
- KhachLuuTru: required array, min 1 guest
- Each guest: TenKhach, NgaySinh (date), CCCD, SoDienThoai (nullable)
```

#### Step 2: Find Booking
```php
$datPhong = DatPhong::with('chiTietDatPhong')->find($id);
```
- Check exists (404 if not)

#### Step 3: Verify Booking Status
```php
if ($datPhong->TinhTrang !== DatPhong::CONFIRMED) {
    // Error: "Đặt phòng chưa được xác nhận hoặc đã nhận phòng"
}
```
- `TinhTrang = 1` (CONFIRMED) mới được check-in

#### Step 4: Verify Room Belongs to Booking
```php
$bookedRoomIds = $datPhong->chiTietDatPhong->pluck('MaPhong');
if (!$bookedRoomIds->contains($data['MaPhong'])) {
    // Error 422: "Phòng không thuộc đặt phòng này"
}
```

#### Step 5: Check for Duplicate Check-in
```php
if (LuuTru::where('MaDatPhong', $id)
           ->where('MaPhong', $data['MaPhong'])
           ->exists()) {
    // Error 409: "Phòng này đã được nhận trước đó"
}
```

#### Step 6: Transaction - Create Records + Update States

**Transaction Block**:
```php
DB::beginTransaction();

try {
    // 6a. Create LuuTru records (guest info)
    foreach ($data['KhachLuuTru'] as $guest) {
        LuuTru::create([
            'TenKhach'   => $guest['TenKhach'],
            'NgaySinh'   => $guest['NgaySinh'],
            'CCCD'       => $guest['CCCD'],
            'SoDienThoai'=> $guest['SoDienThoai'] ?? null,
            'MaPhong'    => $data['MaPhong'],
            'MaDatPhong' => $datPhong->MaDatPhong,
        ]);
    }
    
    // 6b. Update room status to "in-use" (2)
    Phong::where('MaPhong', $data['MaPhong'])
          ->update(['TinhTrang' => 2]);
    
    // 6c. Check if ALL rooms of booking are checked-in
    $checkedInRoomCount = LuuTru::where('MaDatPhong', $id)
                                ->distinct('MaPhong')
                                ->count('MaPhong');
    $totalRoomCount = $bookedRoomIds->unique()->count();
    
    // 6d. If all rooms checked-in, mark booking as CHECKED_IN
    if ($checkedInRoomCount >= $totalRoomCount) {
        $datPhong->update(['TinhTrang' => DatPhong::CHECKED_IN]); // 2
    }
    
    DB::commit();
    
    return success({
        'datPhong': $datPhong.fresh(),
        'checkedInRoomCount': 1,
        'totalRoomCount': 2,
    });
    
} catch (Exception $e) {
    DB::rollBack();
    return error($e->getMessage(), 500);
}
```

---

## 📊 State Transitions

### DatPhong Status
```
0 (HOLD)
  ↓
1 (CONFIRMED) ← Can check-in here
  ↓
2 (CHECKED_IN) ← Auto update when ALL rooms checked-in
  ↓
3 (CHECKED_OUT)
  ↓
4 (CANCELLED)
```

### Phong Status
```
0 (EMPTY)
  ↓
1 (BOOKED) ← When added to DatPhong
  ↓
2 (IN-USE) ← Updated to this during check-in
  ↓
(remains until check-out)
```

---

## 🎯 Key Business Rules

1. **Prerequisite**: Booking must be `CONFIRMED` (status=1)
2. **Atomic**: All guests + room status + booking status updated in single transaction
3. **Partial Check-in**: Allow checking in 1 room at a time of multi-room booking
4. **Auto Complete**: When all rooms of booking checked-in → booking automatically marked as CHECKED_IN
5. **No Duplicate**: Prevent checking in same room twice
6. **Guest Count**: At least 1 guest required per room

---

## 📋 Example Flow

```
Scenario: 2-room booking (Rooms 5 & 7)

1. Guest selects Room 5
   - Fills in 2 guests (Nguyễn Văn A, Nguyễn Thị B)
   - Submits
   
2. Backend:
   - Validates request ✓
   - Creates 2 LuuTru records ✓
   - Updates Phong(5).TinhTrang = 2 ✓
   - Count: 1/2 rooms checked-in
   - DatPhong.TinhTrang remains = 1 (CONFIRMED)
   - Returns: "Check-in Room 5 success" + checkedInRoomCount=1
   
3. Frontend reloads, guest selects Room 7
   - Fills in 2 guests
   - Submits
   
4. Backend:
   - Validates request ✓
   - Creates 2 LuuTru records ✓
   - Updates Phong(7).TinhTrang = 2 ✓
   - Count: 2/2 rooms checked-in
   - **Updates DatPhong.TinhTrang = 2 (CHECKED_IN)** ✓
   - Returns: "Check-in Room 7 success + All rooms checked-in"
```

---

## 🧪 Testing Checklist

- [ ] Form displays only CONFIRMED bookings for today
- [ ] Form shows only rooms NOT yet checked-in for selected booking
- [ ] Validation: Reject empty name, invalid date, age mismatch
- [ ] Validation: Require at least 1 adult (18+)
- [ ] Submit sends correct JSON payload
- [ ] Single room check-in creates LuuTru + updates Phong
- [ ] Partial check-in keeps DatPhong as CONFIRMED
- [ ] Final room check-in auto-updates DatPhong to CHECKED_IN
- [ ] Duplicate check-in rejected (409)
- [ ] Wrong room rejected (422)
- [ ] Unconfirmed booking rejected (400)
- [ ] CSRF token sent with request

---

## 📁 Related Files

- **Frontend**: `resources/views/receptionist/check-in-form.blade.php`
- **API Route**: `routes/api.php` line 52
- **Controller**: `app/Http/Controllers/Api/DatPhongController.php` line 435
- **Models**: 
  - `app/Models/DatPhong.php` (constants: CONFIRMED=1, CHECKED_IN=2)
  - `app/Models/LuuTru.php` (guest records)
  - `app/Models/Phong.php` (room state)

---

## 🔗 Web Route

`GET /reception/check-ins/create` → Shows check-in form with:
- Filtered bookings (today, CONFIRMED status)
- Available rooms (not yet checked-in)
- Guest form by room type capacity
