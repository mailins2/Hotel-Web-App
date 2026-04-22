<?php

namespace App\Http\Controllers;

use App\Models\DichVu;
use App\Models\Hinh;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HotelManagementController extends Controller
{
    public function dashboard(): View
    {
        return view('hotel-management.report', [
            'assets' => ['animation', 'chart'],
            'report' => config('hotel-management.reports', []),
        ]);
    }

    public function report(): View
    {
        return $this->dashboard();
    }

    public function index(string $moduleKey): View
    {
        $module = $this->getModule($moduleKey);
        $records = $this->applyModuleFilters(request(), $module);

        return view('hotel-management.index', [
            'assets' => ['animation'],
            'moduleKey' => $moduleKey,
            'module' => $module,
            'records' => $records,
        ]);
    }

    public function show(string $moduleKey, string $recordId): View
    {
        $module = $this->getModule($moduleKey);
        $record = $this->findRecord($module, $recordId);

        return view('hotel-management.show', [
            'assets' => ['animation'],
            'moduleKey' => $moduleKey,
            'module' => $module,
            'record' => $record,
        ]);
    }

    public function create(string $moduleKey): View
    {
        $module = $this->getModule($moduleKey);
        abort_unless($this->moduleAllows($module, 'create'), 404);

        return view('hotel-management.form', [
            'assets' => ['animation'],
            'moduleKey' => $moduleKey,
            'module' => $module,
            'record' => [],
            'isEdit' => false,
        ]);
    }

    public function edit(string $moduleKey, string $recordId): View
    {
        $module = $this->getModule($moduleKey);
        abort_unless($this->moduleAllows($module, 'edit'), 404);

        return view('hotel-management.form', [
            'assets' => ['animation'],
            'moduleKey' => $moduleKey,
            'module' => $module,
            'record' => $this->findRecord($module, $recordId),
            'isEdit' => true,
        ]);
    }

    public function store(Request $request, string $moduleKey): RedirectResponse
    {
        $module = $this->getModule($moduleKey);
        abort_unless($this->moduleAllows($module, 'create'), 404);

        return redirect()
            ->route('hotel.modules.index', ['moduleKey' => $moduleKey])
            ->with('success', 'Da ghi nhan tao ' . $module['singular'] . ' o giao dien mau.');
    }

    public function update(Request $request, string $moduleKey, string $recordId): RedirectResponse
    {
        $module = $this->getModule($moduleKey);
        abort_unless($this->moduleAllows($module, 'edit'), 404);

        return redirect()
            ->route('hotel.modules.show', ['moduleKey' => $moduleKey, 'recordId' => $recordId])
            ->with('success', 'Da ghi nhan cap nhat ' . $module['singular'] . ' o giao dien mau.');
    }

    public function destroy(string $moduleKey, string $recordId): RedirectResponse
    {
        $module = $this->getModule($moduleKey);
        abort_unless($this->moduleAllows($module, 'delete'), 404);

        return redirect()
            ->route('hotel.modules.index', ['moduleKey' => $moduleKey])
            ->with('success', 'Da ghi nhan xoa ' . $module['singular'] . ' o giao dien mau.');
    }

    public function termOfUse(): View
    {
        return view('hotel-management.term-of-use', [
            'assets' => ['animation'],
        ]);
    }

    protected function getModule(string $moduleKey): array
    {
        $modules = config('hotel-management.modules', []);

        abort_unless(isset($modules[$moduleKey]), 404);

        $module = $modules[$moduleKey];

        if ($moduleKey === 'accounts') {
            return $this->enrichAccountsModule($module, $modules);
        }

        if ($moduleKey === 'customers') {
            return $this->enrichCustomersModule($module);
        }

        if ($moduleKey === 'invoices') {
            return $this->enrichInvoicesModule($module, $modules);
        }

        if ($moduleKey === 'services') {
            return $this->enrichServicesModule($module);
        }

        return $module;
    }

    protected function findRecord(array $module, string $recordId): array
    {
        $primaryKey = $module['primary_key'];

        foreach ($module['records'] ?? [] as $record) {
            if ((string) ($record[$primaryKey] ?? '') === (string) $recordId) {
                return $record;
            }
        }

        abort(404);
    }

    protected function validateRequest(Request $request, string $moduleKey, array $module, array $record = []): void
    {
        if ($moduleKey === 'customers') {
            $this->prepareCustomerAddressRequest($request);
        }

        $rules = [];
        $fields = $module['fields'];

        if ($moduleKey === 'accounts' && !empty($record)) {
            $accountType = (string) ($record['LoaiTaiKhoan'] ?? '');

            if ($accountType === '0') {
                $fields = [
                    'TrangThai' => $module['fields']['TrangThai'],
                ];
            } elseif ($accountType === '1') {
                $fields = [
                    'MatKhau' => ['label' => 'Máº­t kháº©u', 'type' => 'password', 'required' => false],
                    'TrangThai' => $module['fields']['TrangThai'],
                ];
            }
        }

        foreach ($fields as $fieldKey => $field) {
            if ($field['readonly'] ?? false) {
                continue;
            }

            $fieldRules = [];
            $fieldRules[] = ($field['required'] ?? false) ? 'required' : 'nullable';

            $type = $field['type'] ?? 'text';

            if (in_array($type, ['number'], true)) {
                $fieldRules[] = 'numeric';
            } elseif (in_array($type, ['email'], true)) {
                $fieldRules[] = 'email';
            } elseif (in_array($type, ['date'], true)) {
                $fieldRules[] = 'date';
            } elseif (in_array($type, ['datetime-local'], true)) {
                $fieldRules[] = 'date';
            } else {
                $fieldRules[] = 'string';
            }

            $rules[$fieldKey] = $fieldRules;
        }

        $request->validate($rules);
    }

    protected function enrichCustomersModule(array $module): array
    {
        $addressOptions = $this->customerAddressOptions();

        $module['address_options'] = $addressOptions;
        $module['records'] = array_map(function (array $record) use ($addressOptions) {
            return $this->normalizeCustomerAddressRecord($record, $addressOptions);
        }, $module['records'] ?? []);

        return $module;
    }

    protected function enrichAccountsModule(array $module, array $modules): array
    {
        $module['fields'] = $this->withAccountFullNameField($module['fields'] ?? []);
        $module['list_columns'] = $this->withAccountFullNameColumn($module['list_columns'] ?? []);

        $fullNameByAccountId = $this->buildAccountFullNameMap($modules);

        $module['records'] = array_map(function (array $record) use ($fullNameByAccountId) {
            $record['HoTen'] = $fullNameByAccountId[(string) ($record['MaTK'] ?? '')] ?? '';

            return $record;
        }, $module['records'] ?? []);

        return $module;
    }

    protected function withAccountFullNameField(array $fields): array
    {
        if (isset($fields['HoTen'])) {
            return $fields;
        }

        $result = [];
        $inserted = false;

        foreach ($fields as $fieldKey => $field) {
            $result[$fieldKey] = $field;

            if ($fieldKey === 'Email') {
                $result['HoTen'] = [
                    'label' => 'Há» tĂªn',
                    'type' => 'text',
                    'readonly' => true,
                ];
                $inserted = true;
            }
        }

        if (!$inserted) {
            $result['HoTen'] = [
                'label' => 'Há» tĂªn',
                'type' => 'text',
                'readonly' => true,
            ];
        }

        return $result;
    }

    protected function withAccountFullNameColumn(array $columns): array
    {
        if (in_array('HoTen', $columns, true)) {
            return $columns;
        }

        $result = [];
        $inserted = false;

        foreach ($columns as $column) {
            $result[] = $column;

            if ($column === 'Email') {
                $result[] = 'HoTen';
                $inserted = true;
            }
        }

        if (!$inserted) {
            $result[] = 'HoTen';
        }

        return $result;
    }

    protected function buildAccountFullNameMap(array $modules): array
    {
        $fullNameByAccountId = [];

        foreach ($modules['customers']['records'] ?? [] as $customer) {
            $accountId = (string) ($customer['MaTK'] ?? '');

            if ($accountId !== '') {
                $fullNameByAccountId[$accountId] = $customer['TenKH'] ?? '';
            }
        }

        foreach ($modules['employees']['records'] ?? [] as $employee) {
            $accountId = (string) ($employee['MaTK'] ?? '');

            if ($accountId !== '' && !isset($fullNameByAccountId[$accountId])) {
                $fullNameByAccountId[$accountId] = $employee['TenNV'] ?? '';
            }
        }

        return $fullNameByAccountId;
    }

    protected function enrichInvoicesModule(array $module, array $modules): array
    {
        $module['fields'] = $this->withInvoiceEmployeeNameField($module['fields'] ?? []);
        $module['list_columns'] = $this->withInvoiceEmployeeNameColumn($module['list_columns'] ?? []);

        $employeeNameById = $this->buildEmployeeNameMap($modules);

        $module['records'] = array_map(function (array $record) use ($employeeNameById) {
            $record['TenNV'] = $employeeNameById[(string) ($record['MaNV'] ?? '')] ?? '';

            return $record;
        }, $module['records'] ?? []);

        return $module;
    }

    protected function enrichServicesModule(array $module): array
    {
        $module['fields'] = $this->withServiceImageField($module['fields'] ?? []);
        $module['service_categories'] = $this->serviceCategoryBlueprint();
        $module['records'] = $this->buildServiceRecords($module['records'] ?? []);

        return $module;
    }

    protected function withServiceImageField(array $fields): array
    {
        if (isset($fields['ServiceImage'])) {
            return $fields;
        }

        $result = [];
        $inserted = false;

        foreach ($fields as $fieldKey => $field) {
            $result[$fieldKey] = $field;

            if ($fieldKey === 'GiaDV') {
                $result['ServiceImage'] = [
                    'label' => 'áº¢nh dá»‹ch vá»¥',
                    'type' => 'text',
                    'required' => false,
                ];
                $inserted = true;
            }
        }

        if (!$inserted) {
            $result['ServiceImage'] = [
                'label' => 'áº¢nh dá»‹ch vá»¥',
                'type' => 'text',
                'required' => false,
            ];
        }

        return $result;
    }

    protected function buildServiceRecords(array $fallbackRecords): array
    {
        $databaseRecords = $this->loadServiceRecordsFromDatabase();
        $records = empty($databaseRecords) ? $fallbackRecords : $databaseRecords;

        $normalizedRecords = array_map(function (array $record) {
            return $this->normalizeServiceRecord($record);
        }, $records);

        $mappedRecords = array_values(array_filter($normalizedRecords, fn (array $record) => isset($record['ServiceItemKey'])));

        if (!empty($mappedRecords)) {
            return $mappedRecords;
        }

        if (empty($fallbackRecords)) {
            return [];
        }

        $normalizedFallbackRecords = array_map(function (array $record) {
            return $this->normalizeServiceRecord($record);
        }, $fallbackRecords);

        return array_values(array_filter($normalizedFallbackRecords, fn (array $record) => isset($record['ServiceItemKey'])));
    }

    protected function loadServiceRecordsFromDatabase(): array
    {
        try {
            $services = DichVu::query()->get();
        } catch (\Throwable) {
            return [];
        }

        if ($services->isEmpty()) {
            return [];
        }

        $imagesByServiceId = $this->loadServiceImagesByServiceId();

        return $services->map(function (DichVu $service) use ($imagesByServiceId) {
            $record = $service->getAttributes();
            $serviceId = (string) ($record['MaDV'] ?? '');

            if ($serviceId !== '' && isset($imagesByServiceId[$serviceId])) {
                $record['ServiceImage'] = $imagesByServiceId[$serviceId];
            }

            return $record;
        })->all();
    }

    protected function loadServiceImagesByServiceId(): array
    {
        try {
            $images = Hinh::query()->whereNotNull('MaDV')->get();
        } catch (\Throwable) {
            return [];
        }

        $imagesByServiceId = [];

        foreach ($images as $image) {
            $record = $image->getAttributes();
            $serviceId = (string) ($record['MaDV'] ?? '');

            if ($serviceId === '' || isset($imagesByServiceId[$serviceId])) {
                continue;
            }

            $imagePath = $this->extractImagePathFromRecord($record);

            if ($imagePath !== null) {
                $imagesByServiceId[$serviceId] = $imagePath;
            }
        }

        return $imagesByServiceId;
    }

    protected function extractImagePathFromRecord(array $record): ?string
    {
        $candidateKeys = [
            'DuongDan',
            'DuongDanAnh',
            'HinhAnh',
            'Anh',
            'Url',
            'URL',
            'ImageUrl',
            'LinkAnh',
            'TenHinh',
            'Path',
            'FilePath',
            'Src',
            'src',
        ];

        foreach ($candidateKeys as $key) {
            $value = $record[$key] ?? null;

            if (!is_string($value) || trim($value) === '') {
                continue;
            }

            return trim($value);
        }

        foreach ($record as $value) {
            if (!is_string($value)) {
                continue;
            }

            $trimmed = trim($value);

            if ($trimmed === '') {
                continue;
            }

            if (Str::startsWith($trimmed, ['http://', 'https://', '/', 'storage/', 'images/'])) {
                return $trimmed;
            }
        }

        return null;
    }

    protected function normalizeServiceRecord(array $record): array
    {
        $serviceName = (string) ($record['TenDV'] ?? '');
        $serviceItemKey = $this->findServiceItemKey($serviceName);

        if ($serviceItemKey === null) {
            return $record;
        }

        $serviceCategoryKey = $this->findServiceCategoryByItemKey($serviceItemKey);

        if ($serviceCategoryKey === null) {
            return $record;
        }

        $categoryCode = $this->serviceCategoryCode($serviceCategoryKey);

        $record['ServiceItemKey'] = $serviceItemKey;
        $record['ServiceCategoryKey'] = $serviceCategoryKey;
        $record['LoaiDV'] = $categoryCode;

        return $record;
    }

    protected function findServiceCategoryByItemKey(string $itemKey): ?string
    {
        foreach ($this->serviceCategoryBlueprint() as $category) {
            if (in_array($itemKey, array_keys($category['items']), true)) {
                return $category['key'];
            }
        }

        return null;
    }

    protected function findServiceItemKey(string $serviceName): ?string
    {
        $normalizedName = $this->normalizeSearchValue($serviceName);

        if ($normalizedName === '') {
            return null;
        }

        foreach ($this->serviceCategoryBlueprint() as $category) {
            foreach ($category['items'] as $itemKey => $item) {
                $aliases = array_merge([$item['label']], $item['aliases'] ?? []);

                foreach ($aliases as $alias) {
                    if ($this->normalizeSearchValue($alias) === $normalizedName) {
                        return $itemKey;
                    }
                }
            }
        }

        return null;
    }

    protected function normalizeSearchValue(string $value): string
    {
        $value = Str::of($value)->ascii()->lower()->value();
        $value = preg_replace('/[^a-z0-9]+/', ' ', $value);

        return trim((string) $value);
    }

    protected function serviceCategoryCode(string $categoryKey): int
    {
        return $this->tryServiceCategoryCode($categoryKey) ?? 2;
    }

    protected function tryServiceCategoryCode(string $categoryKey): ?int
    {
        return match ($categoryKey) {
            'food' => 0,
            'room' => 1,
            'entertainment' => 2,
            default => null,
        };
    }

    protected function serviceCategoryBlueprint(): array
    {
        return [
            [
                'key' => 'food',
                'label' => 'Dá»‹ch vá»¥ Äƒn uá»‘ng',
                'items' => [
                    'banh-mi' => ['label' => 'BĂ¡nh mĂ¬', 'aliases' => ['Banh mi']],
                    'com-chien' => ['label' => 'CÆ¡m chiĂªn', 'aliases' => ['Com chien']],
                    'bun-bo' => ['label' => 'BĂºn bĂ²', 'aliases' => ['Bun bo']],
                    'bun-thai' => ['label' => 'BĂºn thĂ¡i', 'aliases' => ['Bun thai']],
                    'ca-phe-sua' => ['label' => 'CĂ  phĂª sá»¯a', 'aliases' => ['Ca phe sua']],
                ],
            ],
            [
                'key' => 'room',
                'label' => 'Dá»‹ch vá»¥ phĂ²ng',
                'items' => [
                    'giat-ui' => ['label' => 'Giáº·t á»§i', 'aliases' => ['Giat ui', 'Giat ui nhanh']],
                    've-sinh' => ['label' => 'Vá»‡ sinh', 'aliases' => ['Ve sinh', 'Don phong theo yeu cau']],
                    'doi-phong' => ['label' => 'Äá»•i phĂ²ng', 'aliases' => ['Doi phong', 'Ho tro doi phong']],
                    'huy-phong' => ['label' => 'Há»§y phĂ²ng', 'aliases' => ['Huy phong', 'Phi huy phong linh hoat']],
                    'them-giuong-phu' => ['label' => 'ThĂªm giÆ°á»ng phá»¥', 'aliases' => ['Them giuong phu']],
                ],
            ],
            [
                'key' => 'entertainment',
                'label' => 'Dá»‹ch vá»¥ giáº£i trĂ­',
                'items' => [
                    'spa' => ['label' => 'Spa', 'aliases' => ['Goi spa thu gian']],
                    'cuoi-ngua' => ['label' => 'CÆ°á»¡i ngá»±a', 'aliases' => ['Cuoi ngua', 'Trai nghiem cuoi ngua']],
                    'golf' => ['label' => 'Golf', 'aliases' => ['Goi choi golf cuoi tuan']],
                ],
            ],
        ];
    }

    protected function withInvoiceEmployeeNameField(array $fields): array
    {
        if (isset($fields['TenNV'])) {
            return $fields;
        }

        $result = [];
        $inserted = false;

        foreach ($fields as $fieldKey => $field) {
            $result[$fieldKey] = $field;

            if ($fieldKey === 'MaNV') {
                $result['TenNV'] = [
                    'label' => 'Há» tĂªn nhĂ¢n viĂªn',
                    'type' => 'text',
                    'readonly' => true,
                ];
                $inserted = true;
            }
        }

        if (!$inserted) {
            $result['TenNV'] = [
                'label' => 'Há» tĂªn nhĂ¢n viĂªn',
                'type' => 'text',
                'readonly' => true,
            ];
        }

        return $result;
    }

    protected function withInvoiceEmployeeNameColumn(array $columns): array
    {
        if (in_array('TenNV', $columns, true)) {
            return $columns;
        }

        $result = [];
        $inserted = false;

        foreach ($columns as $column) {
            $result[] = $column;

            if ($column === 'MaNV') {
                $result[] = 'TenNV';
                $inserted = true;
            }
        }

        if (!$inserted) {
            $result[] = 'TenNV';
        }

        return $result;
    }

    protected function buildEmployeeNameMap(array $modules): array
    {
        $employeeNameById = [];

        foreach ($modules['employees']['records'] ?? [] as $employee) {
            $employeeId = (string) ($employee['MaNV'] ?? '');

            if ($employeeId !== '') {
                $employeeNameById[$employeeId] = $employee['TenNV'] ?? '';
            }
        }

        return $employeeNameById;
    }

    protected function prepareCustomerAddressRequest(Request $request): void
    {
        $street = trim((string) $request->input('DiaChiDuong', ''));
        $district = trim((string) $request->input('DiaChiHuyen', ''));
        $province = trim((string) $request->input('DiaChiTinh', ''));

        if ($street === '' && $district === '' && $province === '') {
            return;
        }

        $request->merge([
            'DiaChi' => $this->composeCustomerAddress($street, $district, $province),
        ]);
    }

    protected function normalizeCustomerAddressRecord(array $record, array $addressOptions): array
    {
        $street = trim((string) ($record['DiaChiDuong'] ?? ''));
        $district = trim((string) ($record['DiaChiHuyen'] ?? ''));
        $province = trim((string) ($record['DiaChiTinh'] ?? ''));

        if ($street === '' && $district === '' && $province === '') {
            $parts = $this->parseCustomerAddress((string) ($record['DiaChi'] ?? ''), $addressOptions);
            $street = $parts['street'];
            $district = $parts['district'];
            $province = $parts['province'];
        }

        $record['DiaChiDuong'] = $street;
        $record['DiaChiHuyen'] = $district;
        $record['DiaChiTinh'] = $province;
        $record['DiaChi'] = $this->composeCustomerAddress($street, $district, $province);

        return $record;
    }

    protected function parseCustomerAddress(string $fullAddress, array $addressOptions): array
    {
        $segments = array_values(array_filter(array_map('trim', explode(',', $fullAddress)), static fn ($segment) => $segment !== ''));

        if ($segments === []) {
            return [
                'street' => '',
                'district' => '',
                'province' => '',
            ];
        }

        if (count($segments) === 1) {
            return [
                'street' => $segments[0],
                'district' => '',
                'province' => '',
            ];
        }

        if (count($segments) === 2) {
            return [
                'street' => $segments[0],
                'district' => '',
                'province' => $segments[1],
            ];
        }

        $province = (string) array_pop($segments);
        $district = (string) array_pop($segments);
        $street = implode(', ', $segments);

        if (!isset($addressOptions[$province])) {
            return [
                'street' => $street,
                'district' => $district,
                'province' => $province,
            ];
        }

        return [
            'street' => $street,
            'district' => $district,
            'province' => $province,
        ];
    }

    protected function composeCustomerAddress(string $street, string $district, string $province): string
    {
        return implode(', ', array_values(array_filter([
            trim($street),
            trim($district),
            trim($province),
        ], static fn ($value) => $value !== '')));
    }

    protected function customerAddressOptions(): array
    {
        return [
            'TP.HCM' => ['Quáº­n 1', 'Quáº­n 3', 'Quáº­n 7', 'Quáº­n 10', 'BĂ¬nh Tháº¡nh', 'GĂ² Váº¥p', 'Thá»§ Äá»©c'],
            'HĂ  Ná»™i' => ['Ba ÄĂ¬nh', 'HoĂ n Kiáº¿m', 'Äá»‘ng Äa', 'Hai BĂ  TrÆ°ng', 'Cáº§u Giáº¥y', 'Thanh XuĂ¢n', 'Nam Tá»« LiĂªm'],
            'ÄĂ  Náºµng' => ['Háº£i ChĂ¢u', 'Thanh KhĂª', 'SÆ¡n TrĂ ', 'NgÅ© HĂ nh SÆ¡n', 'LiĂªn Chiá»ƒu', 'Cáº©m Lá»‡'],
            'Cáº§n ThÆ¡' => ['Ninh Kiá»u', 'BĂ¬nh Thá»§y', 'CĂ¡i RÄƒng', 'Ă” MĂ´n', 'Thá»‘t Ná»‘t'],
            'Háº£i PhĂ²ng' => ['Há»“ng BĂ ng', 'NgĂ´ Quyá»n', 'LĂª ChĂ¢n', 'Háº£i An', 'Kiáº¿n An', 'DÆ°Æ¡ng Kinh'],
            'KhĂ¡nh HĂ²a' => ['Nha Trang', 'Cam Ranh', 'Ninh HĂ²a', 'DiĂªn KhĂ¡nh', 'Váº¡n Ninh'],
        ];
    }

    protected function moduleAllows(array $module, string $action): bool
    {
        return (bool) ($module['allow_' . $action] ?? true);
    }

    protected function applyModuleFilters(Request $request, array $module): array
    {
        $records = $module['records'] ?? [];
        $filters = $module['filters'] ?? [];

        foreach ($filters as $queryKey => $filter) {
            $rawValue = $request->query($queryKey);

            if ($rawValue === null || $rawValue === '') {
                continue;
            }

            $field = $filter['field'] ?? null;
            $operator = $filter['operator'] ?? '=';

            if ($field === null) {
                continue;
            }

            $records = array_values(array_filter($records, function (array $record) use ($field, $operator, $rawValue) {
                $recordValue = $record[$field] ?? null;

                if ($recordValue === null || $recordValue === '') {
                    return false;
                }

                return match ($operator) {
                    '>=' => (string) $recordValue >= (string) $rawValue,
                    '<=' => (string) $recordValue <= (string) $rawValue,
                    default => (string) $recordValue === (string) $rawValue,
                };
            }));
        }

        $serviceCategoryKey = (string) $request->query('service_category', '');
        $hasServiceCategories = !empty($module['service_categories']);

        if ($hasServiceCategories && $serviceCategoryKey !== '' && $serviceCategoryKey !== 'all') {
            $serviceCategoryCode = $this->tryServiceCategoryCode($serviceCategoryKey);

            if ($serviceCategoryCode !== null) {
                $records = array_values(array_filter($records, function (array $record) use ($serviceCategoryCode) {
                    return (string) ($record['LoaiDV'] ?? '') === (string) $serviceCategoryCode;
                }));
            }
        }

        return $records;
    }
}