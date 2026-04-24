@php($isReceptionPortal = request()->routeIs('reception.*'))

<div class="iq-navbar-header" style="height: 215px;">
    <div class="container-fluid iq-container">
        <div class="row">
            <div class="col-md-12">
                <div class="flex-wrap d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="fw-bold text-white mb-2">
                            {{ $isReceptionPortal ? 'Nhân viên Peach Valley' : 'Quản lý Peach Valley' }}
                        </h1>
                        <p class="text-white mb-0">
                            {{ $isReceptionPortal
                                ? 'Không gian giao diện dành cho nhân viên Peach Valley.'
                                : 'Không gian giao diện dành cho bộ phận quản lý Peach Valley.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
