<!-- Delete Property Modal -->
<div id="modal-overlay" class="modal-overlay" style="display:none;">
    <div class="modal delete-modal-modern">
        <div class="modal-header delete-modal-header">
            <h5 class="modal-title">Confirm Deletion</h5>
            <button type="button" class="modal-close" id="modal-close">&times;</button>
        </div>
        <div class="modal-body delete-modal-body">
            <div class="delete-warning-icon">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <p class="delete-message">Are you sure you want to delete this property?</p>
            <p class="delete-submessage">This action cannot be undone.</p>
        </div>
        <div class="modal-actions delete-modal-actions">
            <button class="btn btn-secondary" id="modal-cancel">Cancel</button>
            <form id="delete-form" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-trash me-2"></i>Delete
                </button>
            </form>
        </div>
    </div>
</div>

