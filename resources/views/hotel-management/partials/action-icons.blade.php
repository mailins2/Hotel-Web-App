@php
    $showUrl = $showUrl ?? null;
    $editUrl = $editUrl ?? null;
@endphp

<div class="hm-action-group">
    @if($editUrl)
        <a
            href="{{ $editUrl }}"
            class="btn btn-sm btn-warning btn-icon"
            title="Chỉnh sửa"
        >
            <span class="btn-inner">
                <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13.7476 20H21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M16.8392 3.41187C17.6212 2.62988 18.8891 2.62988 19.6711 3.41187L20.5881 4.32887C21.3701 5.11087 21.3701 6.37875 20.5881 7.16075L8.14912 19.5998C7.65512 20.0938 7.04312 20.4538 6.37112 20.6478L3 21L3.352 17.6289C3.546 16.9569 3.906 16.3448 4.4 15.8508L16.8392 3.41187Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </span>
        </a>
    @endif
</div>
