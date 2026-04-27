@if(session('success'))
    <div class="modal fade" id="appMessageModal" tabindex="-1" aria-labelledby="appMessageModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="appMessageModalTitle">Thông báo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    {{ session('success') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const messageModal = document.getElementById('appMessageModal');

            if (messageModal && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(messageModal).show();
            }
        });
    </script>
@endif
