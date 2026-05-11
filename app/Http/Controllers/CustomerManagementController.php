<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\KhachHangController;
use App\Http\Requests\ManagedCustomerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class CustomerManagementController extends Controller
{
    public function create()
    {
        return $this->renderFormView();
    }

    public function edit($recordId)
    {
        return $this->renderFormView($recordId);
    }

    public function store(ManagedCustomerRequest $request)
    {
        return app(KhachHangController::class)->store(
            $this->buildApiRequest($this->mapPayload($request->validated()), 'POST')
        );
    }

    public function update(ManagedCustomerRequest $request, $recordId)
    {
        return app(KhachHangController::class)->update(
            $this->buildApiRequest($this->mapPayload($request->validated()), 'PUT'),
            $recordId
        );
    }

    private function renderFormView($recordId = null)
    {
        $addressData = $this->getCachedAddressData();

        return view('hotel-management.customers.form', [
            'recordId' => $recordId,
            'provinces' => $addressData['provinces'],
            'communes' => $addressData['communes'],
            'today' => now()->toDateString(),
        ]);
    }

    private function mapPayload(array $validated): array
    {
        return [
            'MaTK' => $validated['account_id'] ?? null,
            'TenKH' => $validated['full_name'],
            'SoDienThoai' => $validated['phone'],
            'CCCD' => $validated['cccd'],
            'NgaySinh' => $validated['birthday'],
            'GioiTinh' => $validated['gender'],
            'DiaChi' => !empty($validated['address']) ? trim($validated['address']) : null,
        ];
    }

    private function buildApiRequest(array $payload, string $method): Request
    {
        $request = Request::create('/api/khach-hang', $method, $payload);
        $request->headers->set('Accept', 'application/json');

        return $request;
    }

    private function getCachedAddressData(): array
    {
        $cacheKey = 'address-kit.2025-07-01.all';

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $provincesResponse = Http::timeout(8)
                ->retry(2, 200)
                ->get('https://production.cas.so/address-kit/2025-07-01/provinces');

            $provinces = collect($provincesResponse->json('provinces', []))
                ->map(fn ($province) => [
                    'code' => $province['code'],
                    'name' => $province['name'],
                ])
                ->toArray();

            $communeResponses = Http::pool(fn ($pool) => collect($provinces)
                ->map(fn ($province) => $pool
                    ->as($province['code'])
                    ->timeout(8)
                    ->get("https://production.cas.so/address-kit/2025-07-01/provinces/{$province['code']}/communes"))
                ->all());

            $communes = [];

            foreach ($provinces as $province) {
                $response = $communeResponses[$province['code']] ?? null;

                $communes[$province['code']] = collect($response?->json('communes', []) ?? [])
                    ->map(fn ($commune) => [
                        'code' => $commune['code'],
                        'name' => $commune['name'],
                    ])
                    ->toArray();
            }

            $addressData = [
                'provinces' => $provinces,
                'communes' => $communes,
            ];

            Cache::put($cacheKey, $addressData, now()->addDays(30));

            return $addressData;
        } catch (Throwable $e) {
            Log::warning('Address data fetch failed in customer management', [
                'message' => $e->getMessage(),
            ]);

            return [
                'provinces' => [],
                'communes' => [],
            ];
        }
    }
}
