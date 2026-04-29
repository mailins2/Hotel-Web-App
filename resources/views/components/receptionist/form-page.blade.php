@props([
    'isEdit' => false,
    'indexRoute',
])

<x-app-layout :assets="['animation']">
    <style>
        .hm-readonly-input {
            background-color: #f3f4f6;
            color: #6b7280;
            border-color: #cbd5e1;
        }
    </style>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 pb-4">
                    <div class="header-title">
                        <h4 class="card-title mb-1">{{ $isEdit ? 'Chỉnh sửa' : 'Thêm mới' }}</h4>
                    </div>
                    <a href="{{ $indexRoute }}" class="btn btn-sm btn-primary" style="padding: 10px;">Quay lại</a>
                </div>
                <div class="card-body">
                    <form data-ui-only-form>
                        <div class="row">
                            {{ $slot }}
                        </div>

                        <button type="submit" class="btn btn-primary mt-3" style="padding: 10px;">
                            {{ $isEdit ? 'Lưu thay đổi' : 'Tạo mới' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelector('[data-ui-only-form]')?.addEventListener('submit', function (event) {
                    event.preventDefault();
                });
            });
        </script>
    @endpush
</x-app-layout>
