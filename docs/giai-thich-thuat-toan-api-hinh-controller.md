# Giai thich thuat toan API HinhController

File duoc giai thich: `app/Http/Controllers/Api/HinhController.php`

Controller nay quan ly API cho bang `Hinh`. Cac chuc nang chinh gom: lay danh sach hinh, them hinh, xem chi tiet, cap nhat, xoa, kiem tra du lieu dau vao, tao payload de luu database va upload anh len Cloudinary.

## 1. Tong quan du lieu

Model su dung la `App\Models\Hinh`.

Bang chinh: `Hinh`

Khoa chinh: `Id`

Quan he:

- `MaLoaiPhong` lien ket den bang `LoaiPhong`.
- `MaDV` lien ket den bang `DichVu`.
- Model `Hinh` dung `withTrashed()` o quan he, nen ve mat quan he van co the truy van ca ban ghi da bi soft delete. Tuy nhien trong controller, nhieu cho chu dong loc `deleted_at = null` de chi lay loai phong/dich vu con hoat dong.

Mot hinh co the thuoc:

- Mot loai phong, neu co `MaLoaiPhong`.
- Mot dich vu, neu co `MaDV`.
- Khong thuoc loai phong hay dich vu nao, neu ca hai cot deu `null`.

## 2. Ham `index()`

Muc dich: lay danh sach hinh anh hop le va tra ve JSON.

Thuat toan:

1. Bat dau truy van tu model `Hinh`.
2. Eager load hai quan he:
   - `loaiPhongs`
   - `dichVus`
3. Khi load `loaiPhongs`, chi lay loai phong co `LoaiPhong.deleted_at = null`.
4. Khi load `dichVus`, chi lay dich vu co `DichVu.deleted_at = null`.
5. Loc danh sach hinh theo 3 truong hop:
   - Truong hop 1: hinh co `MaLoaiPhong` va loai phong lien ket chua bi xoa mem.
   - Truong hop 2: hinh co `MaDV` va dich vu lien ket chua bi xoa mem.
   - Truong hop 3: hinh khong gan voi loai phong va cung khong gan voi dich vu, tuc `MaLoaiPhong = null` va `MaDV = null`.
6. Goi `get()` de lay tat ca ket qua.
7. Tra ve response JSON voi HTTP status `200`.

Y nghia: ham nay tranh tra ve cac hinh dang gan voi loai phong/dich vu da bi xoa mem. Tuy nhien cac hinh doc lap, khong gan voi doi tuong nao, van duoc hien thi.

## 3. Ham `store(Request $request)`

Muc dich: them mot hinh anh moi.

Thuat toan:

1. Lay toan bo du lieu request.
2. Validate cac truong:
   - `Url`: bat buoc, kieu chuoi, toi da 500 ky tu.
   - `MaLoaiPhong`: co the null, nhung neu co thi phai ton tai trong bang `LoaiPhong` va ban ghi do chua bi soft delete.
   - `MaDV`: co the null, nhung neu co thi phai ton tai trong bang `DichVu` va ban ghi do chua bi soft delete.
3. Neu validate that bai:
   - Tra ve JSON chua danh sach loi.
   - HTTP status la `422`.
4. Neu validate thanh cong:
   - Goi `buildPayload($request)` de tao mang du lieu can luu.
   - Goi `Hinh::create($payload)` de them ban ghi vao database.
5. Tra ve JSON gom:
   - `message`
   - `data`
   - HTTP status `201`.

Luu y quan trong: trong `store()`, validation hien tai bat buoc co `Url`. Mac du `buildPayload()` co ho tro upload file qua field `image`, rieng ham `store()` hien tai chua goi `makeValidator($request, false)`, nen neu chi gui file `image` ma khong gui `Url` thi request se bi fail o validation dau tien.

## 4. Ham `show($id)`

Muc dich: lay chi tiet mot hinh theo `Id`.

Thuat toan:

1. Tim hinh theo `$id`.
2. Load kem hai quan he:
   - `loaiPhongs`
   - `dichVus`
3. Neu khong tim thay:
   - Tra ve JSON thong bao khong tim thay.
   - HTTP status `404`.
4. Neu tim thay:
   - Tra ve thong tin hinh dang JSON.
   - HTTP status `200`.

Luu y: ham nay load quan he truc tiep theo khai bao trong model. Vi model dung `withTrashed()`, quan he co the tra ve ca loai phong/dich vu da bi xoa mem.

## 5. Ham `update(Request $request, $id)`

Muc dich: cap nhat mot hinh anh da ton tai.

Thuat toan:

1. Tim hinh theo `$id`.
2. Neu khong tim thay:
   - Tra ve JSON thong bao khong tim thay.
   - HTTP status `404`.
3. Validate lan 1:
   - `Url`: neu co gui len thi phai la chuoi, toi da 500 ky tu.
   - `MaLoaiPhong`: co the null, nhung neu co thi phai ton tai trong bang `LoaiPhong` va chua bi soft delete.
   - `MaDV`: co the null, nhung neu co thi phai ton tai trong bang `DichVu` va chua bi soft delete.
4. Neu validate lan 1 that bai:
   - Tra ve JSON loi.
   - HTTP status `422`.
5. Validate lan 2 bang `makeValidator($request, true)`:
   - `Url`: neu co thi phai la chuoi, toi da 500 ky tu.
   - `image`: neu co thi phai la file anh, toi da 5120 KB.
   - `MaLoaiPhong`: neu co thi phai ton tai trong bang `LoaiPhong`.
   - `MaDV`: neu co thi phai ton tai trong bang `DichVu`.
   - Sau do chay them logic `after()` de kiem tra dieu kien lien ket.
6. Trong `after()` cua `makeValidator()`:
   - Tinh `$hasUrl`: request co `Url` khac rong hay khong.
   - Tinh `$hasFile`: request co upload file `image` hay khong.
   - Vi `$isUpdate = true`, controller khong bat buoc phai co URL hoac file anh khi update.
   - Neu request khong co `MaLoaiPhong` va cung khong co `MaDV`, them loi vao `MaLoaiPhong`.
7. Neu validate lan 2 that bai:
   - Tra ve JSON loi.
   - HTTP status `422`.
8. Neu validate thanh cong:
   - Goi `buildPayload($request, $hinh)` de tao mang du lieu can update.
   - Goi `$hinh->update($payload)` de cap nhat database.
9. Tra ve JSON gom:
   - `message`
   - `data` la ban ghi moi nhat sau khi goi `$hinh->fresh()`
   - HTTP status `200`.

Luu y quan trong ve logic hien tai:

- `update()` dang validate hai lan. Lan 1 co dieu kien `whereNull('deleted_at')`, lan 2 dung rule `exists` thuong, khong loc soft delete.
- `makeValidator($request, true)` yeu cau request update phai co it nhat mot trong hai truong `MaLoaiPhong` hoac `MaDV`. Neu chi muon update moi `Url` hoac `image` ma khong gui hai truong nay, request co the bi bao loi.
- `buildPayload()` lai co logic giu gia tri cu neu update ma request khong gui `MaLoaiPhong` hoac `MaDV`. Vi vay dieu kien bat buoc lien ket trong `makeValidator()` co the chua thong nhat voi logic build payload.

## 6. Ham `destroy($id)`

Muc dich: xoa mot hinh theo `Id`.

Thuat toan:

1. Tim hinh theo `$id`.
2. Neu khong tim thay:
   - Tra ve JSON thong bao khong tim thay.
   - HTTP status `404`.
3. Neu tim thay:
   - Goi `$hinh->delete()`.
4. Tra ve JSON thong bao da xoa.
5. HTTP status `200`.

Luu y: model `Hinh` trong file hien tai khong khai bao `SoftDeletes`, nen `$hinh->delete()` se la xoa cung neu model khong duoc cau hinh soft delete o noi khac.

## 7. Ham `makeValidator(Request $request, bool $isUpdate)`

Muc dich: tao validator dung chung cho viec kiem tra URL, file anh, loai phong va dich vu.

Thuat toan:

1. Tao validator voi cac rule:
   - `Url`: neu update thi `sometimes`, neu khong update thi `nullable`; sau do van cho phep null, yeu cau string va toi da 500 ky tu.
   - `image`: neu update thi `sometimes`, neu khong update thi `nullable`; sau do van cho phep null, yeu cau la file anh va toi da 5120 KB.
   - `MaLoaiPhong`: neu update thi `sometimes`, neu khong update thi `nullable`; co the null, neu co thi phai ton tai trong bang `LoaiPhong`.
   - `MaDV`: neu update thi `sometimes`, neu khong update thi `nullable`; co the null, neu co thi phai ton tai trong bang `DichVu`.
2. Gan them callback `after()` de validate logic nghiep vu sau khi validate rule co ban.
3. Trong callback:
   - Kiem tra request co URL khac rong hay khong.
   - Kiem tra request co file anh hay khong.
   - Neu dang them moi va khong co ca URL lan file anh, them loi: phai cung cap file anh hoac URL.
   - Neu khong dien `MaLoaiPhong` va cung khong dien `MaDV`, them loi: phai lien ket anh voi loai phong hoac dich vu.
4. Tra ve validator de ham goi tu quyet dinh neu fail thi tra response loi.

Luu y: ham nay hien tai chi duoc goi trong `update()`, chua duoc goi trong `store()`.

## 8. Ham `buildPayload(Request $request, ?Hinh $currentImage = null)`

Muc dich: chuyen request thanh mang du lieu `$payload` de luu vao database.

Thuat toan:

1. Khoi tao `$payload = []`.
2. Xu ly `MaLoaiPhong`:
   - Neu request co field `MaLoaiPhong`, gan vao payload.
   - Neu gia tri rong, chuyen thanh `null`.
   - Neu request khong co field nay va dang them moi, gan `MaLoaiPhong = null`.
   - Neu request khong co field nay va dang update, khong dua vao payload de giu gia tri cu.
3. Xu ly `MaDV` tuong tu `MaLoaiPhong`.
4. Xu ly anh:
   - Neu request co file `image`:
     - Xac dinh folder upload bang `resolveUploadFolder()`.
     - Upload file len Cloudinary bang `uploadToCloudinary()`.
     - Gan `Url` bang `secure_url` Cloudinary tra ve.
     - Gan `public_id` bang `public_id` Cloudinary tra ve, neu co.
   - Neu request khong co file nhung co field `Url`:
     - Cat khoang trang dau cuoi URL.
     - Gan URL vao payload.
     - Neu dang them moi, hoac URL moi khac URL cu, gan `public_id = null` vi URL nay khong chac la anh Cloudinary do he thong upload.
5. Tra ve `$payload`.

Y nghia: ham nay giup tach logic tao du lieu luu database ra khoi `store()` va `update()`.

## 9. Ham `resolveUploadFolder(Request $request, ?Hinh $currentImage = null)`

Muc dich: quyet dinh folder Cloudinary de upload anh.

Thuat toan:

1. Lay `MaLoaiPhong` tu request. Neu request khong co hoac rong thi lay tu hinh hien tai.
2. Lay `MaDV` tu request. Neu request khong co hoac rong thi lay tu hinh hien tai.
3. Dung `match (true)` de chon folder:
   - Neu co `MaLoaiPhong`: upload vao `hotel-web-app/room-types`.
   - Neu khong co `MaLoaiPhong` nhung co `MaDV`: upload vao `hotel-web-app/services`.
   - Neu khong co ca hai: upload vao `hotel-web-app/images`.
4. Tra ve ten folder.

Thu tu uu tien: `MaLoaiPhong` duoc uu tien hon `MaDV`. Neu request co ca hai, anh se vao folder cua room type.

## 10. Ham `uploadToCloudinary(UploadedFile $file, string $folder)`

Muc dich: upload file anh len Cloudinary va tra ve thong tin upload.

Thuat toan:

1. Tao object `Cloudinary`.
2. Lay cau hinh tu bien moi truong:
   - `CLOUDINARY_CLOUD_NAME`
   - `CLOUDINARY_API_KEY`
   - `CLOUDINARY_API_SECRET`
3. Lay duong dan tam cua file upload bang `$file->getRealPath()`.
4. Goi API upload cua Cloudinary:
   - File: duong dan tam.
   - Option: `folder` la folder da tinh o buoc truoc.
5. Chuyen ket qua Cloudinary thanh array bang `getArrayCopy()`.
6. Tra ve array nay cho `buildPayload()`.

Ket qua thuong duoc dung:

- `secure_url`: URL HTTPS cua anh sau khi upload.
- `public_id`: ma dinh danh anh tren Cloudinary.

## 11. Tom tat luong xu ly chinh

Luong them moi:

1. Client gui request them hinh.
2. `store()` validate request.
3. `buildPayload()` tao du lieu can luu.
4. Neu co file `image`, upload Cloudinary va lay URL.
5. Tao ban ghi `Hinh`.
6. Tra ve JSON status `201`.

Luong cap nhat:

1. Client gui request update theo `Id`.
2. `update()` tim hinh.
3. Neu khong co hinh, tra `404`.
4. Validate request hai lan.
5. `buildPayload()` tao du lieu can cap nhat.
6. Neu co file `image`, upload Cloudinary va thay URL cu bang URL moi.
7. Cap nhat database.
8. Tra ve JSON status `200`.

Luong xoa:

1. Client gui request xoa theo `Id`.
2. `destroy()` tim hinh.
3. Neu khong co hinh, tra `404`.
4. Neu co hinh, goi `delete()`.
5. Tra ve JSON status `200`.

## 12. Cac diem can chu y khi bao tri code

- Cac chuoi message trong file hien tai dang bi loi ma hoa ky tu tieng Viet. Nen mo file dung encoding UTF-8 va sua lai message de hien thi dung.
- `store()` va `makeValidator()` chua thong nhat: `store()` bat buoc `Url`, con `makeValidator()` lai thiet ke de chap nhan `Url` hoac file `image`.
- `update()` validate hai lan, co the lam logic kho hieu va phat sinh loi khong mong muon.
- Nen can nhac rang moi hinh chi nen gan voi mot trong hai doi tuong: `MaLoaiPhong` hoac `MaDV`. Code hien tai khong cam viec gui ca hai cung luc.
- Khi update anh moi len Cloudinary, code hien tai chua xoa anh cu tren Cloudinary theo `public_id`, nen co the con file cu tren Cloudinary.
