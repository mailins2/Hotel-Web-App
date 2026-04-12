<div class="iq-navbar-header" style="height: 215px;">
    <div class="container-fluid iq-container">
        <div class="row">
            <div class="col-md-12">
                <div class="flex-wrap d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="fw-bold text-white mb-2">
                            {{ isReceptionist() ? 'Hệ thống quản lý Peach Valley' : 'Peach Valley Admin' }}
                        </h1>
                        <p class="text-white mb-0">
                            {{ isReceptionist()
                                ? 'Hệ thống quản lý khách sạn Peach Valley dành cho Lễ tân'
                                : 'Hệ thống quản lý khách sạn Peach Valley dành cho Admin' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
