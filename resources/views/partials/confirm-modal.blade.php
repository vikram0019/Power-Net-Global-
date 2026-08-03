<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmActionModalTitle">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirmActionModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-gold fw-bold" id="confirmActionModalConfirmBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('confirmActionModal');
    if (!modalEl) {
        return;
    }

    var modal = new bootstrap.Modal(modalEl);
    var titleEl = document.getElementById('confirmActionModalTitle');
    var bodyEl = document.getElementById('confirmActionModalBody');
    var confirmBtn = document.getElementById('confirmActionModalConfirmBtn');
    var pendingForm = null;

    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            pendingForm = form;
            titleEl.textContent = form.dataset.confirmTitle || 'Confirm Action';
            bodyEl.textContent = form.dataset.confirm;
            modal.show();
        });
    });

    confirmBtn.addEventListener('click', function () {
        modal.hide();
        if (pendingForm) {
            pendingForm.submit();
        }
    });
});
</script>
