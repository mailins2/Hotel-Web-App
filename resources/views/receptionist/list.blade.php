<x-app-layout :assets="$assets ?? []">
    @php
        $filters = $page['filters'] ?? [];
        $columns = $page['columns'] ?? [];
        $rows = $page['rows'] ?? [];
        $summaryCards = $page['summary_cards'] ?? [];
        $badgeMaps = $page['badge_maps'] ?? [];
        $createButton = $page['create_button'] ?? null;
        $rowActions = $page['row_actions'] ?? null;
        $moneyColumns = ['TienCoc', 'TongTien', 'DaThanhToan', 'ConLai'];
    @endphp

    <style>
        .rd-shell {
            --rd-bg: linear-gradient(135deg, #fff8f1 0%, #fffdf9 52%, #f6fbfb 100%);
            --rd-border: rgba(217, 119, 6, 0.14);
            --rd-shadow: 0 24px 60px rgba(148, 82, 24, 0.08);
            --rd-text: #3f2b1d;
            --rd-muted: #866b59;
            --rd-accent: #c26b2d;
            --rd-accent-dark: #8f3f12;
        }

        .rd-panel {
            background: var(--rd-bg);
            border: 1px solid var(--rd-border);
            border-radius: 28px;
            box-shadow: var(--rd-shadow);
        }

        .rd-hero {
            padding: 1.75rem;
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .rd-hero::after {
            content: '';
            position: absolute;
            inset: auto -60px -60px auto;
            width: 180px;
            height: 180px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.22) 0%, rgba(245, 158, 11, 0) 72%);
        }

        .rd-eyebrow {
            color: var(--rd-accent-dark);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .rd-title {
            color: var(--rd-text);
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .rd-description {
            color: var(--rd-muted);
            max-width: 720px;
            margin-bottom: 0;
        }

        .rd-demo-note {
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(194, 107, 45, 0.14);
            border-radius: 20px;
            padding: 1rem 1.1rem;
            color: var(--rd-text);
        }

        .rd-card {
            border: 1px solid var(--rd-border);
            border-radius: 22px;
            padding: 1.25rem;
            background: #fff;
            height: 100%;
            box-shadow: 0 16px 34px rgba(120, 74, 44, 0.06);
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .rd-card-label {
            color: var(--rd-muted);
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            text-align: center;
        }

        .rd-card-value {
            color: var(--rd-text);
            font-size: 1.8rem;
            font-weight: 800;
            margin-top: 0.6rem;
            text-align: center;
        }

        .rd-card--sunrise {
            background: linear-gradient(180deg, #fff5eb 0%, #fff 100%);
        }

        .rd-card--teal {
            background: linear-gradient(180deg, #eefbfb 0%, #fff 100%);
        }

        .rd-card--amber {
            background: linear-gradient(180deg, #fff8dc 0%, #fff 100%);
        }

        .rd-card--slate {
            background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
        }

        .rd-table-card {
            border: 1px solid var(--rd-border);
            border-radius: 28px;
            background: #fffdfa;
            box-shadow: var(--rd-shadow);
        }

        .rd-toolbar {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 1.5rem 1.5rem 0;
        }

        .rd-toolbar-note {
            color: var(--rd-muted);
            max-width: 520px;
        }

        .rd-toolbar-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-left: auto;
        }

        .rd-create-button {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
        }

        .rd-create-button svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .rd-filter-box {
            padding: 1rem 1.5rem 0;
        }

        .rd-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.38rem 0.78rem;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
        }

        .rd-badge--success {
            background: #dcfce7;
            color: #166534;
        }

        .rd-badge--warning {
            background: #fef3c7;
            color: #9a3412;
        }

        .rd-badge--info {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .rd-badge--muted {
            background: #eceff3;
            color: #475569;
        }

        .rd-badge--danger {
            background: #fee2e2;
            color: #b91c1c;
        }

        .rd-table {
            margin-bottom: 0;
        }

        .rd-table thead th {
            white-space: nowrap;
            font-size: 0.82rem;
            color: var(--rd-muted);
            border-bottom-color: rgba(194, 107, 45, 0.12);
        }

        .rd-table tbody td {
            vertical-align: middle;
            border-bottom-color: rgba(194, 107, 45, 0.08);
            color: var(--rd-text);
        }

        .rd-empty {
            padding: 2rem 1rem !important;
            color: var(--rd-muted) !important;
        }

        .rd-action-group {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .rd-icon-button {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            text-decoration: none;
            border: none;
            transition: transform 0.2s ease, filter 0.2s ease;
        }

        .rd-icon-button svg {
            width: 18px;
            height: 18px;
        }

        .rd-icon-button:hover {
            transform: translateY(-1px);
            filter: brightness(0.98);
        }

        .rd-icon-button--view,
        .rd-icon-button--view:visited,
        .rd-icon-button--view:hover,
        .rd-icon-button--view:focus {
            background: #22c55e;
            color: #ffffff !important;
        }

        .rd-icon-button--edit {
            background: #f59e0b;
            color: #7c2d12;
        }

        .rd-cell-truncate {
            display: block;
            max-width: 240px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .rd-cell-truncate {
                max-width: 180px;
            }
        }
    </style>

    <div class="rd-shell">
        <div class="rd-panel rd-hero">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    @if(!($page['hide_eyebrow'] ?? false))
                        <div class="rd-eyebrow">{{ $page['eyebrow'] ?? 'Front Desk' }}</div>
                    @endif
                    <h2 class="rd-title">{{ $page['title'] ?? 'Trang lễ tân' }}</h2>
                    <p class="rd-description">{{ $page['description'] ?? '' }}</p>
                </div>
                @if(!($page['hide_demo_note'] ?? false))
                    <div class="col-lg-4">
                        <div class="rd-demo-note">
                            <div class="fw-bold mb-2">Chế độ demo</div>
                            <div class="small mb-0">Các bảng đang dùng dữ liệu giả để bạn hoàn thiện UI và luồng chuyển trang trước khi nối API hoặc database thật.</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if(!($page['hide_summary_cards'] ?? false))
            <div class="row g-3 mb-4">
                @foreach($summaryCards as $summaryCard)
                    <div class="col-md-6 col-xl-4">
                        <div class="rd-card rd-card--{{ $summaryCard['tone'] ?? 'sunrise' }}">
                            <div class="rd-card-label">{{ $summaryCard['label'] ?? '' }}</div>
                            <div class="rd-card-value">{{ $summaryCard['value'] ?? '' }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="rd-table-card">
            @if(session('success'))
                <div class="alert alert-success m-4 mb-0">
                    {{ session('success') }}
                </div>
            @endif

            <div class="rd-toolbar">
                @if(!($page['hide_toolbar_intro'] ?? false))
                    <div>
                        <h4 class="mb-1">{{ $page['title'] ?? '' }}</h4>
                        <p class="rd-toolbar-note mb-0">{{ $page['table_note'] ?? '' }}</p>
                    </div>
                @endif
                @if(is_array($createButton) && !empty($createButton['url']))
                    <div class="rd-toolbar-actions">
                        <a href="{{ $createButton['url'] }}" class="btn btn-primary rd-create-button" style="padding: 10px 18px;">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11 13.5C13.4853 13.5 15.5 11.4853 15.5 9C15.5 6.51472 13.4853 4.5 11 4.5C8.51472 4.5 6.5 6.51472 6.5 9C6.5 11.4853 8.51472 13.5 11 13.5Z" fill="currentColor" opacity="0.92"/>
                                <path d="M3.5 19.5C3.5 16.7386 6.18629 14.5 9.5 14.5H12.5C14.163 14.5 15.6681 15.063 16.7518 15.9721C15.6497 16.5803 14.9048 17.7537 14.9048 19.0952V19.5H3.5Z" fill="currentColor" opacity="0.92"/>
                                <path d="M18.5 14.5V22.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M14.5 18.5H22.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            {{ $createButton['label'] ?? 'Thêm mới' }}
                        </a>
                    </div>
                @endif
            </div>

            @if(!empty($filters))
                <div class="rd-filter-box">
                    <form method="GET">
                        <div class="row g-3 align-items-end">
                            @foreach($filters as $filter)
                                @php
                                    $value = request()->query($filter['key'] ?? '', '');
                                @endphp
                                <div class="col-md-4">
                                    <label class="form-label" for="{{ $filter['key'] }}">{{ $filter['label'] }}</label>
                                    @if(($filter['type'] ?? 'text') === 'select')
                                        <select class="form-control" id="{{ $filter['key'] }}" name="{{ $filter['key'] }}">
                                            @foreach($filter['options'] ?? [] as $optionValue => $optionLabel)
                                                <option value="{{ $optionValue }}" {{ (string) $value === (string) $optionValue ? 'selected' : '' }}>
                                                    {{ $optionLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input
                                            class="form-control"
                                            type="{{ $filter['type'] ?? 'text' }}"
                                            id="{{ $filter['key'] }}"
                                            name="{{ $filter['key'] }}"
                                            value="{{ $value }}"
                                            placeholder="{{ $filter['placeholder'] ?? '' }}"
                                        >
                                    @endif
                                </div>
                            @endforeach
                            <div class="col-md-auto">
                                <button type="submit" class="btn btn-primary" style="padding: 10px 18px;">Lọc dữ liệu</button>
                            </div>
                            <div class="col-md-auto">
                                <a href="{{ url()->current() }}" class="btn btn-light" style="padding: 10px 18px;">Đặt lại</a>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            <div class="table-responsive p-4">
                <table class="table rd-table align-middle">
                    <thead>
                        <tr>
                            @foreach($columns as $column)
                                <th>{{ $column['label'] ?? $column['key'] }}</th>
                            @endforeach
                            @if(is_array($rowActions))
                                <th>Thao tác</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr>
                                @foreach($columns as $column)
                                    @php
                                        $columnKey = $column['key'] ?? '';
                                        $value = $row[$columnKey] ?? '';
                                        $badgeDefinition = $badgeMaps[$columnKey][(string) $value] ?? null;

                                        if (in_array($columnKey, $moneyColumns, true) && $value !== '') {
                                            $value = number_format((float) $value, 0, ',', '.') . ' VNĐ';
                                        }

                                        if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
                                            $value = \Carbon\Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
                                        }
                                    @endphp
                                    <td>
                                        @if($badgeDefinition)
                                            <span class="{{ $badgeDefinition['class'] ?? 'rd-badge rd-badge--muted' }}">
                                                {{ $badgeDefinition['label'] ?? $value }}
                                            </span>
                                        @elseif($columnKey === 'DiaChi' && is_string($value) && $value !== '')
                                            <span class="rd-cell-truncate" title="{{ $value }}">
                                                {{ $value }}
                                            </span>
                                        @else
                                            {{ $value }}
                                        @endif
                                    </td>
                                @endforeach
                                @if(is_array($rowActions))
                                    @php
                                        $recordId = $row[$rowActions['primary_key'] ?? ''] ?? null;
                                        $routeParameterName = $rowActions['parameter_name'] ?? 'id';
                                        $showUrl = ($recordId !== null && !empty($rowActions['show_route']))
                                            ? route($rowActions['show_route'], [$routeParameterName => $recordId])
                                            : null;
                                        $editUrl = ($recordId !== null && !empty($rowActions['edit_route']))
                                            ? route($rowActions['edit_route'], [$routeParameterName => $recordId])
                                            : null;
                                    @endphp
                                    <td>
                                        <div class="rd-action-group">
                                            @if($showUrl)
                                                <a href="{{ $showUrl }}" class="rd-icon-button rd-icon-button--view" title="Xem chi tiết">
                                                    <svg width="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M2 12C3.73 8.11 7.52 5.5 12 5.5C16.48 5.5 20.27 8.11 22 12C20.27 15.89 16.48 18.5 12 18.5C7.52 18.5 3.73 15.89 2 12Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if($editUrl)
                                                <a href="{{ $editUrl }}" class="rd-icon-button rd-icon-button--edit" title="Chỉnh sửa">
                                                    <svg width="18" viewBox="0 0 24 24" fill="none" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M13.7476 20H21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M16.8392 3.41187C17.6212 2.62988 18.8891 2.62988 19.6711 3.41187L20.5881 4.32887C21.3701 5.11087 21.3701 6.37875 20.5881 7.16075L8.14912 19.5998C7.65512 20.0938 7.04312 20.4538 6.37112 20.6478L3 21L3.352 17.6289C3.546 16.9569 3.906 16.3448 4.4 15.8508L16.8392 3.41187Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($columns) + (is_array($rowActions) ? 1 : 0) }}" class="text-center rd-empty">
                                    {{ $page['empty_text'] ?? 'Chưa có dữ liệu để hiển thị.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
