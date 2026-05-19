$filePath = "c:\Github\Hotel-Web-App\resources\views\receptionist\check-in-form.blade.php"
$content = Get-Content $filePath -Raw -Encoding UTF8

# Batch 1: Main titles and navigation
$content = $content -replace "Táº¡o Ä'áº·t phÃ²ng má»›i", "Tạo đặt phòng mới"
$content = $content -replace "Danh sÃ¡ch Ä'áº·t phÃ²ng", "Danh sách đặt phòng"
$content = $content -replace "Äáº·t phÃ²ng chá» nháº­n", "Đặt phòng chờ nhận"
$content = $content -replace "KhÃ¡ch Ä'áº¿n hÃ´m nay", "Khách đến hôm nay"
$content = $content -replace "Äáº·t phÃ²ng nháº­n rá»"i", "Đặt phòng nhận rồi"
$content = $content -replace "Danh sÃ¡ch nháº­n phÃ²ng hÃ´m nay", "Danh sách nhận phòng hôm nay"
$content = $content -replace "TÃ¬m theo mÃ£ Ä'áº·t phÃ²ng, tÃªn khÃ¡ch hÃ ng hoáº·c sá»' phÃ²ng", "Tìm theo mã đặt phòng, tên khách hàng hoặc số phòng"
$content = $content -replace "KhÃ¡ch chÆ°a cÃ³ tÃªn", "Khách chưa có tên"

# Batch 2: Booking details
$content = $content -replace "Äáº·t phÃ²ng #", "Đặt phòng #"
$content = $content -replace "(\d+) Ä'Ãªm", "`$1 đêm"
$content = $content -replace "phÃ²ng", "phòng"
$content = $content -replace "ChÆ°a cÃ³ loáº¡i phÃ²ng", "Chưa có loại phòng"
$content = $content -replace "KhÃ´ng cÃ³ Ä'áº·t phÃ²ng nÃ o Ä'ang chá» nháº­n", "Không có đặt phòng nào đang chờ nhận"

# Batch 3: Form labels
$content = $content -replace "Chi tiáº¿t xÃ¡c nháº­n", "Chi tiết xác nhận"
$content = $content -replace "ChÆ°a chá»n phÃ²ng", "Chưa chọn phòng"
$content = $content -replace "Chá»n phÃ²ng muá»'n cho nháº­n phÃ²ng", "Chọn phòng muốn cho nhận phòng"
$content = $content -replace "NgÆ°á»i lá»›n", "Người lớn"
$content = $content -replace "Há» vÃ  tÃªn", "Họ và tên"
$content = $content -replace "Nháº­p há» vÃ  tÃªn", "Nhập họ và tên"
$content = $content -replace "NgÃ y sinh", "Ngày sinh"
$content = $content -replace "Nháº­p sá»' CCCD", "Nhập số CCCD"
$content = $content -replace "Sá»' Ä'iá»‡n thoáº¡i", "Số điện thoại"
$content = $content -replace "Nháº­p sá»' Ä'iá»‡n thoáº¡i", "Nhập số điện thoại"
$content = $content -replace "Tráº» em", "Trẻ em"
$content = $content -replace "ChÆ°a cÃ³ thÃ´ng tin sá»©c chá»©a", "Chưa có thông tin sức chứa"
$content = $content -replace "â€¢", "•"

# Batch 4: Dialog
$content = $content -replace "XÃ¡c nháº­n nháº­n phÃ²ng", "Xác nhận nhận phòng"
$content = $content -replace "Vui lÃ²ng kiá»ƒm tra láº¡i thÃ´ng tin phÃ²ng trÆ°á»›c khi xÃ¡c nháº­n", "Vui lòng kiểm tra lại thông tin phòng trước khi xác nhận"
$content = $content -replace "MÃ£ Ä'áº·t phÃ²ng", "Mã đặt phòng"
$content = $content -replace "Sá»' phÃ²ng", "Số phòng"
$content = $content -replace "Sá»' lÆ°á»£ng khÃ¡ch", "Số lượng khách"
$content = $content -replace "Thá»i gian lÆ°u trÃº", "Thời gian lưu trú"
$content = $content -replace "Há»§y", "Hủy"
$content = $content -replace "Nháº­n phÃ²ng", "Nhận phòng"
$content = $content -replace "XÃ¡c nháº­n", "Xác nhận"

# Batch 5: JavaScript button text and validation messages
$content = $content -replace "Äang chá»n phÃ²ng", "Đang chọn phòng"
$content = $content -replace "Dang xác nh\?n\.\.\.", "Đang xác nhận..."
$content = $content -replace "Vui lÃ²ng nháº­p ngÃ y sinh\.", "Vui lòng nhập ngày sinh."
$content = $content -replace "NgÃ y sinh khÃ´ng há»£p lá»‡\.", "Ngày sinh không hợp lệ."
$content = $content -replace "NgÃ y sinh khÃ´ng Ä'Æ°á»£c lá»›n hÆ¡n hÃ´m nay\.", "Ngày sinh không được lớn hơn hôm nay."
$content = $content -replace "KhÃ¡ch tá»« 12 tuá»•i trá»Ÿ lÃªn Ä'Æ°á»£c tÃ­nh lÃ  ngÆ°á»i lá»›n\.", "Khách từ 12 tuổi trở lên được tính là người lớn."
$content = $content -replace "KhÃ¡ch nhá» hÆ¡n 12 tuá»•i Ä'Æ°á»£c tÃ­nh lÃ  tráº» em\.", "Khách nhỏ hơn 12 tuổi được tính là trẻ em."
$content = $content -replace "KhÃ´ng cho phÃ©p chá»‰ má»™t ngÆ°á»i dÆ°á»›i 18 tuá»•i check-in\.", "Không cho phép chỉ một người dưới 18 tuổi check-in."
$content = $content -replace "Cáº§n cÃ³ Ã­t nháº¥t má»™t ngÆ°á»i Ä'á»§ 18 tuá»•i trá»Ÿ lÃªn Ä'á»ƒ check-in\.", "Cần có ít nhất một người đủ 18 tuổi trở lên để check-in."
$content = $content -replace "Không thể thu thập thông tin khách\.", "Không thể thu thập thông tin khách."
$content = $content -replace "Không thể nhận phòng\.", "Không thể nhận phòng."

Set-Content $filePath $content -Encoding UTF8
Write-Host "✓ Fix completed successfully"
