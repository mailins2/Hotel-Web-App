<x-app-layout :assets="$assets ?? []">
    <div class="row">
        @foreach($report['summary_cards'] as $card)
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase mb-2">{{ $card['label'] }}</div>
                        <h3 class="mb-1">{{ $card['value'] }}</h3>
                        <p class="mb-0 text-muted">{{ $card['description'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title mb-0">Doanh thu theo tháng</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tháng</th>
                                    <th>Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report['monthly_revenue'] as $item)
                                    <tr>
                                        <td>{{ $item['month'] }}</td>
                                        <td>{{ number_format($item['value'], 0, ',', '.') }} VNĐ</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title mb-0">Tình trạng phòng</h4>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($report['room_status'] as $status)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                            <span>{{ $status['label'] }}</span>
                            <span class="fw-semibold">{{ $status['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
