# Phan tich giai thuat migration `2026_05_13_000004_change_khuyen_mai_makm_to_varchar`

## 1. Muc tieu

Migration nay chuyen kieu du lieu cua cot `MaKM` tu `BIGINT UNSIGNED` sang `VARCHAR(10)` trong 3 bang:

- `KhuyenMai`
- `KhoKhuyenMai`
- `HoaDon`

Ly do chinh la cho phep ma khuyen mai dung dang chuoi, vi du: `SALE10`, `SUMMER25`, thay vi chi dung so.

## 2. Bai toan ky thuat

Cot `MaKM` khong chi ton tai o bang `KhuyenMai`, ma con lien quan den:

- khoa ngoai tu `KhoKhuyenMai.MaKM` sang `KhuyenMai.MaKM`
- khoa ngoai tu `HoaDon.MaKM` sang `KhuyenMai.MaKM`
- khoa chinh gop cua bang `KhoKhuyenMai`

Vi vay, khong the doi truc tiep kieu du lieu cua `MaKM` khi cac rang buoc van dang ton tai. Migration giai quyet bai toan theo thu tu:

1. Kiem tra schema hien tai da o dung trang thai chua.
2. Tim va go bo cac khoa ngoai lien quan.
3. Go khoa chinh cua bang trung gian neu can.
4. Doi kieu du lieu tren cac bang.
5. Tao lai khoa chinh.
6. Tao lai khoa ngoai.

## 3. Giai thuat ham `up()`

Ham `up()` chuyen schema sang dang `VARCHAR(10)`.

### Buoc 1: Kiem tra da migrate chua

Neu ca 3 cot `MaKM` trong `KhuyenMai`, `KhoKhuyenMai`, `HoaDon` deu da la `varchar` thi dung ngay:

```php
if ($this->isVarcharSchema()) {
    return;
}
```

Muc dich:

- tranh chay lai migration gay loi
- giu migration co tinh idempotent o muc co ban

### Buoc 2: Tim ten khoa ngoai thuc te

Migration khong gia dinh ten constraint la co dinh, ma truy van `information_schema` de lay dung ten khoa ngoai:

- FK tu `KhoKhuyenMai.MaKM` den `KhuyenMai.MaKM`
- FK tu `HoaDon.MaKM` den `KhuyenMai.MaKM`

Muc dich:

- tranh phu thuoc vao ten do framework hoac DB tao ra
- tang kha nang chay duoc tren schema da ton tai tu truoc

### Buoc 3: Xoa khoa ngoai neu dang ton tai

Neu tim thay khoa ngoai thi drop truoc khi doi kieu du lieu:

```php
ALTER TABLE KhoKhuyenMai DROP FOREIGN KEY ...
ALTER TABLE HoaDon DROP FOREIGN KEY ...
```

Muc dich:

- vi MySQL khong cho sua kieu du lieu cua cot dang bi rang buoc FK

### Buoc 4: Xoa khoa chinh cua `KhoKhuyenMai` neu co

Bang `KhoKhuyenMai` dung khoa chinh gop `(MaKM, MaKH)`. Migration xoa tam khoa chinh truoc khi sua cot `MaKM`.

Muc dich:

- tranh xung dot rang buoc khi sua kieu du lieu trong cot thuoc primary key

### Buoc 5: Sua kieu du lieu

Migration doi kieu du lieu cua 3 cot:

- `KhuyenMai.MaKM` -> `VARCHAR(10) NOT NULL`
- `KhoKhuyenMai.MaKM` -> `VARCHAR(10) NOT NULL`
- `HoaDon.MaKM` -> `VARCHAR(10) NULL`

Luu y:

- `HoaDon.MaKM` duoc phep `NULL` vi FK cua no dung `ON DELETE SET NULL`

### Buoc 6: Tao lai khoa chinh cho `KhoKhuyenMai`

Sau khi sua xong cot, migration tao lai primary key neu hien tai chua co:

```php
ALTER TABLE KhoKhuyenMai ADD PRIMARY KEY (MaKM, MaKH)
```

### Buoc 7: Tao lai cac khoa ngoai

Migration chi them lai neu FK chua ton tai:

- `KhoKhuyenMai.MaKM -> KhuyenMai.MaKM ON DELETE CASCADE`
- `HoaDon.MaKM -> KhuyenMai.MaKM ON DELETE SET NULL`

Muc dich:

- khoi phuc toan ven tham chieu sau khi doi schema

## 4. Giai thuat ham `down()`

Ham `down()` rollback schema ve `BIGINT UNSIGNED`.

### Buoc 1: Kiem tra schema da la `bigint` chua

Neu ca 3 cot da la `bigint` thi dung ngay:

```php
if ($this->isBigIntSchema()) {
    return;
}
```

### Buoc 2: Chan rollback neu co ma khuyen mai khong phai so

Day la buoc bao ve quan trong nhat:

```php
if ($this->hasNonNumericPromotionCodes()) {
    throw new RuntimeException(...);
}
```

Y nghia:

- neu du lieu da co ma nhu `SALE10`, `VIP20` thi khong the ep ve `BIGINT`
- migration chu dong dung lai de tranh mat du lieu hoac loi convert

### Buoc 3 den 7

Phan con lai cua `down()` doi xung voi `up()`:

1. tim FK
2. xoa FK
3. xoa primary key cua `KhoKhuyenMai`
4. doi `MaKM` ve `BIGINT UNSIGNED`
5. dat lai `AUTO_INCREMENT` cho `KhuyenMai.MaKM`
6. tao lai primary key
7. tao lai FK

## 5. Cac ham ho tro

### `isVarcharSchema()`

Kiem tra 3 cot `MaKM` da dong bo o kieu `varchar` chua.

### `isBigIntSchema()`

Kiem tra 3 cot `MaKM` da dong bo o kieu `bigint` chua.

### `getColumnType($table, $column)`

Doc `DATA_TYPE` tu `information_schema.COLUMNS` de lay kieu du lieu thuc te cua cot.

### `hasPrimaryKey($table)`

Kiem tra bang co `PRIMARY KEY` hay khong bang `information_schema.TABLE_CONSTRAINTS`.

### `getForeignKeyName($table, $column, $referencedTable, $referencedColumn)`

Tim ten constraint FK trong `information_schema.KEY_COLUMN_USAGE`.

Ham nay giup migration:

- khong hard-code ten FK
- hoat dong an toan hon tren nhieu moi truong

### `hasNonNumericPromotionCodes()`

Kiem tra trong 3 bang co gia tri `MaKM` nao chua ky tu khong phai so hay khong, bang regex:

```sql
REGEXP '[^0-9]'
```

Neu co, rollback bi chan.

## 6. Gia ma tong quat

```text
UP:
  neu schema da la varchar -> dung
  lay ten FK cua KhoKhuyenMai va HoaDon
  neu FK ton tai -> xoa FK
  neu KhoKhuyenMai co primary key -> xoa primary key
  doi MaKM cua 3 bang sang varchar(10)
  neu KhoKhuyenMai chua co primary key -> tao lai
  neu FK chua co -> tao lai FK

DOWN:
  neu schema da la bigint -> dung
  neu ton tai MaKM khong phai so -> bao loi va dung
  lay ten FK cua KhoKhuyenMai va HoaDon
  neu FK ton tai -> xoa FK
  neu KhoKhuyenMai co primary key -> xoa primary key
  doi MaKM cua 3 bang ve bigint unsigned
  dat lai auto_increment cho KhuyenMai.MaKM
  neu KhoKhuyenMai chua co primary key -> tao lai
  neu FK chua co -> tao lai FK
```

## 7. Diem manh cua cach lam

- Khong hard-code ten foreign key.
- Co kiem tra schema truoc khi sua.
- Co kiem tra du lieu truoc khi rollback.
- Bao toan duoc rang buoc khoa chinh va khoa ngoai sau khi migrate.

## 8. Rui ro can luu y

- Migration nay phu thuoc vao MySQL/MariaDB vi dung `information_schema` va cu phap `ALTER TABLE` cu the.
- Neu ton tai du lieu `MaKM` dai hon 10 ky tu, `VARCHAR(10)` se khong du.
- Neu app da dua ma khuyen mai chu vao van hanh, `down()` co the khong rollback duoc, va do la hanh vi dung de bao ve du lieu.

## 9. Ket luan

Migration nay khong chi la doi kieu du lieu don thuan. No giai quyet dong thoi 3 van de:

- chuyen khoa chinh tu dang so sang dang chuoi
- bao toan lien ket giua cac bang
- ngan rollback nguy hiem khi du lieu khong con phu hop voi `BIGINT`

Vi vay, day la mot migration theo huong "doi schema an toan", uu tien tinh on dinh va toan ven du lieu.
