<!-- Edit Property Modal -->
<div id="edit-property-container" class="modal-overlay" style="display:none;">
    <div class="modal edit-modal-modern">
        <div class="modal-header edit-modal-header">
            <div class="modal-header-content">
                <div class="modal-icon-wrapper edit-icon">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h5 class="modal-title">Edit Property</h5>
                    <p class="modal-subtitle">Update property information</p>
                </div>
            </div>
            <button type="button" id="edit-close" class="modal-close"><i class="bi bi-x-lg"></i></button>
        </div>
        <form id="edit-property-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="edit-property-id" name="property_id">
            <div class="edit-modal-body">
                <div class="form-section">
                    <div class="section-header">
                        <i class="bi bi-info-circle me-2"></i>
                        <span>Basic Information</span>
                    </div>
                    <div class="form-grid">
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-tag me-1"></i>Category <span class="required">*</span>
                            </label>
                            <input type="text" id="edit-category" name="category" class="form-control-modern" required>
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-geo-alt me-1"></i>Location <span class="required">*</span>
                            </label>
                            <input type="text" id="edit-location" name="location" class="form-control-modern" required>
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-currency-dollar me-1"></i>Price <span class="required">*</span>
                            </label>
                            <input type="number" id="edit-price" name="price" class="form-control-modern" required>
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-tag-fill me-1"></i>Status <span class="required">*</span>
                            </label>
                            <select id="edit-status" name="status" class="form-control-modern form-select-modern" required>
                                <option value="available">Available</option>
                                <option value="sold">Sold</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-arrow-left-right me-1"></i>Transaction Type <span class="required">*</span>
                            </label>
                            <select id="edit-transaction-type" name="transaction_type" class="form-control-modern form-select-modern" required>
                                <option value="sale">Sale</option>
                                <option value="rent">Rent</option>
                            </select>
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label-modern">
                                <i class="bi bi-calendar-range me-1"></i>Installment Years
                            </label>
                            <input type="number" id="edit-installment-years" name="installment_years" class="form-control-modern" min="0" placeholder="0 for cash only">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-header">
                        <i class="bi bi-file-text me-2"></i>
                        <span>Description</span>
                    </div>
                    <div class="form-group-modern">
                        <textarea id="edit-description" name="description" rows="4" class="form-control-modern" placeholder="Enter property description..."></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <div class="section-header">
                        <i class="bi bi-images me-2"></i>
                        <span>Property Images</span>
                    </div>
                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="bi bi-image me-1"></i>Primary Image
                        </label>
                        <input type="file" id="edit-image" name="image" accept="image/*" class="form-control-modern file-input-modern">
                        <small class="form-help-text">Upload a new primary image (optional)</small>
                    </div>
                    
                    <div class="multi-upload-group">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label-modern mb-0">
                                <i class="bi bi-images me-1"></i>Additional Images
                            </label>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="edit-add-multi-image-btn">
                                <i class="bi bi-plus-circle me-1"></i>Add Image
                            </button>
                        </div>
                        <div class="multi-images-wrapper" id="edit-multi-images-wrapper">
                            <div class="multi-image-input-group mb-2 d-flex align-items-center gap-2">
                                <input type="file" class="form-control-modern" name="multiple_images[]" accept="image/*">
                                <button type="button" class="btn btn-sm btn-danger remove-image-btn"><i class="bi bi-dash-circle"></i></button>
                            </div>
                        </div>
                        <div class="existing-images-wrapper mt-3" id="edit-existing-images-wrapper">
                            <label class="form-label-modern mb-2">Existing Images:</label>
                            <div class="existing-images-list" id="edit-existing-images-list"></div>
                        </div>
                    </div>
                </div>

                @auth
                @if(auth()->user()->role === 'admin')
                <div class="form-section">
                    <div class="section-header">
                        <i class="bi bi-person me-2"></i>
                        <span>Owner Information</span>
                    </div>
                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="bi bi-person-badge me-1"></i>User ID <span class="required">*</span>
                        </label>
                        <input type="number" id="edit-user-id" name="user_id" class="form-control-modern" required>
                    </div>
                </div>
                @endif
                @endauth
            </div>
            <div class="edit-modal-footer">
                <button type="button" id="edit-cancel" class="btn btn-cancel-modal">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </button>
                <button type="submit" class="btn btn-save-modal">
                    <i class="bi bi-check-circle me-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
