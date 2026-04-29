@props([
    'title',
    'subtitle',
    'indexRoute',
    'editRoute' => null,
])

<x-app-layout :assets="['animation']">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 pb-4">
                    <div class="header-title">
                        <h4 class="card-title mb-1">{{ $title }}</h4>
                        <p class="mb-0 text-muted">{{ $subtitle }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        @if($editRoute)
                            <a href="{{ $editRoute }}" class="btn btn-sm btn-warning" style="padding: 10px;">Chỉnh sửa</a>
                        @endif
                        <a href="{{ $indexRoute }}" class="btn btn-sm btn-primary" style="padding: 10px;">Quay lại</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
