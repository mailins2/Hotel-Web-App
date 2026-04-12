<x-app-layout :assets="$assets ?? []">
    @php
        $revenueChart = $report['revenue_chart'] ?? [];
        $revenueChartJson = json_encode(
            $revenueChart,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
        );
        $defaultRevenuePeriod = $revenueChart['default_period'] ?? 'month';
        $defaultRevenueItems = $revenueChart['periods'][$defaultRevenuePeriod]['items'] ?? [];
        $defaultRevenueListLimit = match ($defaultRevenuePeriod) {
            'day' => 7,
            'year' => 5,
            default => 6,
        };
        $defaultRevenueListItems = array_slice($defaultRevenueItems, -$defaultRevenueListLimit);
        $defaultRevenueHeading = match ($defaultRevenuePeriod) {
            'day' => 'Ngày',
            'year' => 'Năm',
            default => 'Tháng',
        };
    @endphp

    <style>
        .revenue-icon-blue {
            color: #2f80ed;
        }

        .report-revenue-chart {
            min-height: 320px;
            margin-bottom: 1.5rem;
        }

        .report-revenue-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .report-revenue-filter {
            min-width: 180px;
        }

        .report-revenue-controls {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .report-revenue-date-filter {
            min-width: 180px;
        }

        .room-status-card .card-header {
            padding-bottom: 1.25rem;
        }

        .room-status-card .card-body {
            padding-top: 1.5rem;
        }

        @media (max-width: 575.98px) {
            .report-revenue-filter {
                width: 100%;
            }

            .report-revenue-date-filter {
                width: 100%;
            }
        }
    </style>

    <div class="row">
        @foreach($report['summary_cards'] as $card)
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="{{ $card['icon_class'] ?? 'text-primary' }} d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width: 56px; height: 56px;">
                                @if(($card['icon'] ?? '') === 'customers')
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M11.9488 14.54C8.49884 14.54 5.58789 15.1038 5.58789 17.2795C5.58789 19.4562 8.51765 20.0001 11.9488 20.0001C15.3988 20.0001 18.3098 19.4364 18.3098 17.2606C18.3098 15.084 15.38 14.54 11.9488 14.54Z" fill="currentColor"></path>
                                        <path opacity="0.4" d="M11.949 12.467C14.2851 12.467 16.1583 10.5831 16.1583 8.23351C16.1583 5.88306 14.2851 4 11.949 4C9.61293 4 7.73975 5.88306 7.73975 8.23351C7.73975 10.5831 9.61293 12.467 11.949 12.467Z" fill="currentColor"></path>
                                        <path opacity="0.4" d="M21.0881 9.21923C21.6925 6.84176 19.9205 4.70654 17.664 4.70654C17.4187 4.70654 17.1841 4.73356 16.9549 4.77949C16.9244 4.78669 16.8904 4.802 16.8725 4.82902C16.8519 4.86324 16.8671 4.90917 16.8895 4.93889C17.5673 5.89528 17.9568 7.0597 17.9568 8.30967C17.9568 9.50741 17.5996 10.6241 16.9728 11.5508C16.9083 11.6462 16.9656 11.775 17.0793 11.7948C17.2369 11.8227 17.3981 11.8371 17.5629 11.8416C19.2059 11.8849 20.6807 10.8213 21.0881 9.21923Z" fill="currentColor"></path>
                                    </svg>
                                @elseif(($card['icon'] ?? '') === 'rooms')
                                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path opacity="0.4" d="M5 9C5 7.34315 6.34315 6 8 6H16C17.6569 6 19 7.34315 19 9V16H5V9Z" fill="currentColor"></path>
                                        <path d="M7 10C7 9.44772 7.44772 9 8 9H10C10.5523 9 11 9.44772 11 10V12H7V10Z" fill="currentColor"></path>
                                        <path d="M13 10C13 9.44772 13.4477 9 14 9H16C16.5523 9 17 9.44772 17 10V12H13V10Z" fill="currentColor"></path>
                                        <path d="M4 13C4 12.4477 4.44772 12 5 12H19C19.5523 12 20 12.4477 20 13V15C20 15.5523 19.5523 16 19 16H5C4.44772 16 4 15.5523 4 15V13Z" fill="currentColor"></path>
                                        <path d="M6 16H8V18C8 18.5523 7.55228 19 7 19C6.44772 19 6 18.5523 6 18V16Z" fill="currentColor"></path>
                                        <path d="M16 16H18V18C18 18.5523 17.5523 19 17 19C16.4477 19 16 18.5523 16 18V16Z" fill="currentColor"></path>
                                    </svg>
                                @elseif(($card['icon'] ?? '') === 'revenue')
                                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path opacity="0.2" d="M4 8C4 6.89543 4.89543 6 6 6H18C19.1046 6 20 6.89543 20 8V16C20 17.1046 19.1046 18 18 18H6C4.89543 18 4 17.1046 4 16V8Z" fill="currentColor"></path>
                                        <path d="M4 9C4 8.44772 4.44772 8 5 8H19C19.5523 8 20 8.44772 20 9V10.5H4V9Z" fill="currentColor"></path>
                                        <path d="M7 12.5C7 11.9477 7.44772 11.5 8 11.5H12.5C13.0523 11.5 13.5 11.9477 13.5 12.5V14.5C13.5 15.0523 13.0523 15.5 12.5 15.5H8C7.44772 15.5 7 15.0523 7 14.5V12.5Z" fill="currentColor"></path>
                                        <path d="M8.75 12.75C8.75 12.3358 9.08579 12 9.5 12H11C11.4142 12 11.75 12.3358 11.75 12.75V14.25C11.75 14.6642 11.4142 15 11 15H9.5C9.08579 15 8.75 14.6642 8.75 14.25V12.75Z" fill="white"></path>
                                        <path d="M15 13.5C15 12.1193 16.1193 11 17.5 11C18.8807 11 20 12.1193 20 13.5C20 14.8807 18.8807 16 17.5 16C16.1193 16 15 14.8807 15 13.5Z" fill="currentColor"></path>
                                        <path d="M16.75 13.5C16.75 13.0858 17.0858 12.75 17.5 12.75C17.9142 12.75 18.25 13.0858 18.25 13.5C18.25 13.9142 17.9142 14.25 17.5 14.25C17.0858 14.25 16.75 13.9142 16.75 13.5Z" fill="white"></path>
                                    </svg>
                                @elseif(($card['icon'] ?? '') === 'reviews')
                                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path opacity="0.4" d="M12 4L14.4721 9.00872L20 9.8123L16 13.7106L16.9443 19.2154L12 16.6154L7.05573 19.2154L8 13.7106L4 9.8123L9.52786 9.00872L12 4Z" fill="currentColor"></path>
                                        <path d="M12 7.5L13.2361 10.0044C13.3816 10.2994 13.6631 10.5039 13.9889 10.5513L16.7526 10.9529L14.7526 12.9024C14.5168 13.1323 14.4091 13.4635 14.4648 13.788L14.9365 16.5401L12.4648 15.2401C12.1738 15.087 11.8262 15.087 11.5352 15.2401L9.06353 16.5401L9.53519 13.788C9.59085 13.4635 9.4832 13.1323 9.24736 12.9024L7.24736 10.9529L10.0111 10.5513C10.3369 10.5039 10.6184 10.2994 10.7639 10.0044L12 7.5Z" fill="currentColor"></path>
                                    </svg>
                                @endif
                            </div>
                            <div class="fw-bold text-uppercase mb-0" style="font-size: 14px; line-height: 1.3;">{{ $card['label'] }}</div>
                        </div>
                        <h3 class="mb-0 text-center">{{ $card['value'] }}</h3>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <div class="report-revenue-toolbar">
                        <div class="header-title">
                            <h4 class="card-title mb-0" id="revenue-chart-title">
                                {{ $revenueChart['periods'][$revenueChart['default_period'] ?? 'month']['title'] ?? 'Doanh thu theo tháng' }}
                            </h4>
                        </div>
                        <div class="report-revenue-controls">
                            <div class="report-revenue-filter">
                                <select class="form-select" id="revenue-period-select" aria-label="Chọn kiểu thống kê doanh thu">
                                    <option value="day" @selected($defaultRevenuePeriod === 'day')>Theo ngày</option>
                                    <option value="month" @selected($defaultRevenuePeriod === 'month')>Theo tháng</option>
                                    <option value="year" @selected($defaultRevenuePeriod === 'year')>Theo năm</option>
                                </select>
                            </div>
                            <div class="report-revenue-date-filter" id="revenue-time-filter"></div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(!empty($revenueChart['periods']))
                        <div id="monthly-revenue-chart" class="report-revenue-chart"></div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th id="revenue-list-heading">{{ $defaultRevenueHeading }}</th>
                                    <th>Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody id="revenue-list-body">
                                @foreach($defaultRevenueListItems as $item)
                                    <tr>
                                        <td>{{ $item['label'] ?? $item['month'] ?? $item['key'] ?? '' }}</td>
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
            <div class="card room-status-card">
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

    <div id="revenue-chart-data" data-chart="{{ $revenueChartJson }}" hidden></div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const chartElement = document.querySelector('#monthly-revenue-chart');
                const periodSelectElement = document.querySelector('#revenue-period-select');
                const timeFilterElement = document.querySelector('#revenue-time-filter');
                const chartTitleElement = document.querySelector('#revenue-chart-title');
                const revenueListHeadingElement = document.querySelector('#revenue-list-heading');
                const revenueListBodyElement = document.querySelector('#revenue-list-body');
                const revenueChartDataElement = document.querySelector('#revenue-chart-data');
                const revenueChart = revenueChartDataElement
                    ? JSON.parse(revenueChartDataElement.dataset.chart || '{}')
                    : null;

                if (!chartElement || !periodSelectElement || !timeFilterElement || !revenueListBodyElement || !revenueChart || !revenueChart.periods || typeof window.ApexCharts === 'undefined') {
                    return;
                }

                const listHeadingMap = {
                    day: 'Ngày',
                    month: 'Tháng',
                    year: 'Năm',
                };

                const formatCurrency = function (value) {
                    return new Intl.NumberFormat('vi-VN', {
                        style: 'currency',
                        currency: 'VND',
                        maximumFractionDigits: 0,
                    }).format(value);
                };

                const formatCompactCurrency = function (value) {
                    if (value >= 1000000000) {
                        return (value / 1000000000).toFixed(value % 1000000000 === 0 ? 0 : 1) + ' tỷ';
                    }

                    if (value >= 1000000) {
                        return (value / 1000000).toFixed(value % 1000000 === 0 ? 0 : 1) + ' triệu';
                    }

                    return new Intl.NumberFormat('vi-VN').format(value);
                };

                const getPeriodItems = function (periodKey) {
                    const period = revenueChart.periods[periodKey];

                    if (!period || !Array.isArray(period.items)) {
                        return [];
                    }

                    return period.items;
                };

                const getDefaultFilterKey = function (periodKey) {
                    const items = getPeriodItems(periodKey);

                    if (items.length === 0) {
                        return '';
                    }

                    return items[items.length - 1].key || '';
                };

                const normalizeFilterKey = function (periodKey, filterKey) {
                    const items = getPeriodItems(periodKey);

                    if (items.some(function (item) {
                        return item.key === filterKey;
                    })) {
                        return filterKey;
                    }

                    return getDefaultFilterKey(periodKey);
                };

                const getVisibleItems = function (periodKey, filterKey) {
                    const items = getPeriodItems(periodKey);
                    const normalizedFilterKey = normalizeFilterKey(periodKey, filterKey);
                    const filteredItems = items.filter(function (item) {
                        return (item.key || '') <= normalizedFilterKey;
                    });

                    const limit = periodKey === 'day'
                        ? 7
                        : (periodKey === 'year' ? 5 : 6);

                    return filteredItems.slice(-limit);
                };

                const defaultPeriod = revenueChart.default_period && revenueChart.periods[revenueChart.default_period]
                    ? revenueChart.default_period
                    : 'month';

                periodSelectElement.value = defaultPeriod;
                let currentFilterKey = getDefaultFilterKey(defaultPeriod);

                const chart = new window.ApexCharts(chartElement, {
                    chart: {
                        type: 'line',
                        height: 320,
                        toolbar: {
                            show: false,
                        },
                        zoom: {
                            enabled: false,
                        },
                    },
                    series: [
                        {
                            name: 'Doanh thu',
                            data: [],
                        },
                    ],
                    colors: ['#2f80ed'],
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 4,
                    },
                    markers: {
                        size: 5,
                        strokeWidth: 0,
                        hover: {
                            size: 7,
                        },
                    },
                    grid: {
                        borderColor: 'rgba(138, 146, 166, 0.18)',
                        strokeDashArray: 4,
                        padding: {
                            left: 10,
                            right: 18,
                            top: 10,
                        },
                    },
                    xaxis: {
                        categories: [],
                        axisBorder: {
                            show: false,
                        },
                        axisTicks: {
                            show: false,
                        },
                        labels: {
                            style: {
                                colors: '#8a92a6',
                                fontSize: '12px',
                            },
                        },
                    },
                    yaxis: {
                        labels: {
                            formatter: function (value) {
                                return formatCompactCurrency(value);
                            },
                            style: {
                                colors: '#8a92a6',
                                fontSize: '12px',
                            },
                        },
                    },
                    tooltip: {
                        y: {
                            formatter: function (value) {
                                return formatCurrency(value);
                            },
                        },
                    },
                    legend: {
                        show: false,
                    },
                });

                chart.render();

                const renderTable = function (periodKey, items) {
                    if (revenueListHeadingElement) {
                        revenueListHeadingElement.textContent = listHeadingMap[periodKey] || 'Thời gian';
                    }

                    revenueListBodyElement.innerHTML = items.map(function (item) {
                        return '<tr><td>' + (item.label || item.key || '') + '</td><td>' + formatCurrency(item.value || 0) + '</td></tr>';
                    }).join('');
                };

                const renderTimeFilter = function (periodKey, filterKey) {
                    const items = getPeriodItems(periodKey);
                    const normalizedFilterKey = normalizeFilterKey(periodKey, filterKey);
                    const firstKey = items.length > 0 ? (items[0].key || '') : '';
                    const lastKey = items.length > 0 ? (items[items.length - 1].key || '') : '';

                    if (periodKey === 'year') {
                        timeFilterElement.innerHTML = '<select class="form-select" id="revenue-time-input" aria-label="Chọn năm xem doanh thu">' +
                            items.map(function (item) {
                                const selected = item.key === normalizedFilterKey ? ' selected' : '';

                                return '<option value="' + item.key + '"' + selected + '>' + item.label + '</option>';
                            }).join('') +
                            '</select>';
                    } else {
                        const inputType = periodKey === 'day' ? 'date' : 'month';
                        const ariaLabel = periodKey === 'day' ? 'Chọn ngày xem doanh thu' : 'Chọn tháng xem doanh thu';

                        timeFilterElement.innerHTML = '<input class="form-control" id="revenue-time-input" type="' + inputType + '" min="' + firstKey + '" max="' + lastKey + '" value="' + normalizedFilterKey + '" aria-label="' + ariaLabel + '">';
                    }

                    const timeInputElement = document.querySelector('#revenue-time-input');

                    if (!timeInputElement) {
                        return;
                    }

                    timeInputElement.addEventListener('change', function (event) {
                        renderPeriod(periodKey, event.target.value, true);
                    });
                };

                const renderPeriod = function (periodKey, filterKey, keepCurrentFilterControl) {
                    const period = revenueChart.periods[periodKey];

                    if (!period) {
                        return;
                    }

                    const normalizedFilterKey = normalizeFilterKey(periodKey, filterKey);
                    const visibleItems = getVisibleItems(periodKey, normalizedFilterKey);

                    if (visibleItems.length === 0) {
                        return;
                    }

                    currentFilterKey = normalizedFilterKey;

                    if (!keepCurrentFilterControl) {
                        renderTimeFilter(periodKey, normalizedFilterKey);
                    }

                    if (chartTitleElement) {
                        chartTitleElement.textContent = period.title || 'Biểu đồ doanh thu';
                    }

                    chart.updateOptions({
                        xaxis: {
                            categories: visibleItems.map(function (item) {
                                return item.label;
                            }),
                        },
                    });

                    chart.updateSeries([
                        {
                            name: 'Doanh thu',
                            data: visibleItems.map(function (item) {
                                return Number(item.value) || 0;
                            }),
                        },
                    ]);

                    renderTable(periodKey, visibleItems);
                };

                renderPeriod(defaultPeriod, currentFilterKey, false);

                periodSelectElement.addEventListener('change', function (event) {
                    const nextPeriod = event.target.value;
                    const nextFilterKey = getDefaultFilterKey(nextPeriod);

                    renderPeriod(nextPeriod, nextFilterKey, false);
                });
            });
        </script>
    @endpush
</x-app-layout>
