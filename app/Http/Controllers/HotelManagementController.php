<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HotelManagementController extends Controller
{
    public function dashboard(): View
    {
        return view('hotel-management.report', [
            'assets' => ['animation'],
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

        return view('hotel-management.index', [
            'assets' => ['animation'],
            'moduleKey' => $moduleKey,
            'module' => $module,
            'records' => $module['records'] ?? [],
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
        return view('hotel-management.form', [
            'assets' => ['animation'],
            'moduleKey' => $moduleKey,
            'module' => $this->getModule($moduleKey),
            'record' => [],
            'isEdit' => false,
        ]);
    }

    public function edit(string $moduleKey, string $recordId): View
    {
        $module = $this->getModule($moduleKey);

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
        $this->validateRequest($request, $module);

        return redirect()
            ->route('hotel.' . $moduleKey . '.index')
            ->with('success', 'Đã tạo ' . $module['singular'] . ' trong giao diện mẫu.');
    }

    public function update(Request $request, string $moduleKey, string $recordId): RedirectResponse
    {
        $module = $this->getModule($moduleKey);
        $this->findRecord($module, $recordId);
        $this->validateRequest($request, $module);

        return redirect()
            ->route('hotel.' . $moduleKey . '.show', $recordId)
            ->with('success', 'Đã cập nhật ' . $module['singular'] . ' trong giao diện mẫu.');
    }

    public function destroy(string $moduleKey, string $recordId): RedirectResponse
    {
        $module = $this->getModule($moduleKey);
        $this->findRecord($module, $recordId);

        return redirect()
            ->route('hotel.' . $moduleKey . '.index')
            ->with('success', 'Đã xóa ' . $module['singular'] . ' trong giao diện mẫu.');
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

        return $modules[$moduleKey];
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

    protected function validateRequest(Request $request, array $module): void
    {
        $rules = [];

        foreach ($module['fields'] as $fieldKey => $field) {
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
}
