# Soft Delete JS Fetch Examples

Tai lieu nay ghi lai mau `fetch` de dung cho cac man Blade cua du an Hotel Web App khi can thao tac voi API soft delete.

## 1. Restore ban ghi tu thung rac

```js
const restoreRecord = async function (moduleBaseUrl, recordId) {
    const response = await fetch(`/api/${moduleBaseUrl}/${encodeURIComponent(recordId)}/restore`, {
        method: 'POST',
        headers: {
            Accept: 'application/json'
        }
    });

    const payload = await response.json().catch(function () {
        return {};
    });

    if (!response.ok || payload.success === false) {
        throw new Error(payload.message || 'Khong the khoi phuc ban ghi.');
    }

    return payload;
};

// Vi du:
// await restoreRecord('loai-phong', 15);
// await restoreRecord('phong', 10);
// await restoreRecord('tien-nghi', 8);
// await restoreRecord('dich-vu', 12);
// await restoreRecord('khuyen-mai', 3);
```

## 2. Force delete ban ghi

```js
const forceDeleteRecord = async function (moduleBaseUrl, recordId) {
    const response = await fetch(`/api/${moduleBaseUrl}/${encodeURIComponent(recordId)}/force-delete`, {
        method: 'DELETE',
        headers: {
            Accept: 'application/json'
        }
    });

    const payload = await response.json().catch(function () {
        return {};
    });

    if (!response.ok || payload.success === false) {
        throw new Error(payload.message || 'Khong the xoa vinh vien ban ghi.');
    }

    return payload;
};

// Vi du:
// await forceDeleteRecord('loai-phong', 15);
// await forceDeleteRecord('phong', 10);
// await forceDeleteRecord('tien-nghi', 8);
// await forceDeleteRecord('dich-vu', 12);
// await forceDeleteRecord('khuyen-mai', 3);
```

## 3. Tai danh sach thung rac

```js
const loadTrash = async function (moduleBaseUrl) {
    const response = await fetch(`/api/${moduleBaseUrl}/trash`, {
        headers: {
            Accept: 'application/json'
        }
    });

    const payload = await response.json().catch(function () {
        return {};
    });

    if (!response.ok || payload.success === false) {
        throw new Error(payload.message || 'Khong the tai danh sach thung rac.');
    }

    return Array.isArray(payload.data) ? payload.data : [];
};

// Vi du:
// const trashItems = await loadTrash('dich-vu');
```

## 4. Goi y flow Blade + JS hien tai

Trong cac trang danh sach cua du an, co the noi them 1 nut:

- `Khoi phuc`: goi `restoreRecord(...)`
- `Xoa vinh vien`: goi `forceDeleteRecord(...)`

Sau khi thanh cong:

1. hien `alert` hoac `toast`
2. goi lai ham load du lieu danh sach
3. render lai bang
