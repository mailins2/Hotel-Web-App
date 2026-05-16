@props([
    'title',
    'subtitle',
    'indexRoute',
])

<x-app-layout :assets="['animation']">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2 pb-4">
                    <div class="header-title">
                        <h4 class="card-title mb-1">{{ $title }}</h4>
                        <p class="mb-0 text-muted">{{ $subtitle }}</p>
                    </div>

                    <a href="{{ $indexRoute }}" class="btn btn-sm btn-primary" style="padding: 10px;">
                        Quay lại
                    </a>
                </div>

                <div class="card-body">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
