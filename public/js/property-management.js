$(document).ready(function() {

    // ---------------- Toast Notifications ----------------
    function showToast(message, type = 'error') {
        const toastContainer = $('#toast-container');
        if (!toastContainer.length) return;

        let iconClass = 'fa-exclamation-circle';
        if (type === 'success') iconClass = 'fa-check-circle';
        else if (type === 'warning') iconClass = 'fa-exclamation-triangle';
        else if (type === 'info') iconClass = 'fa-info-circle';

        const toast = $(`<div class="toast ${type}">
            <i class="fas ${iconClass} toast-icon"></i>
            <span class="toast-message">${message}</span>
            <button class="toast-close"><i class="fas fa-times"></i></button>
        </div>`);

        toastContainer.append(toast);
        toast.find('.toast-close').click(() => toast.remove());
        setTimeout(() => toast.fadeOut(300, () => toast.remove()), 5000);
    }

    window.pmShowToast = showToast;

    const createMultiInput = () => {
        const inputGroup = $('<div class="multi-image-input-group mb-2 d-flex align-items-center gap-2"></div>');
        const input = $('<input type="file" class="form-control" name="multiple_images[]" accept="image/*">');
        const removeBtn = $('<button type="button" class="btn btn-sm btn-danger remove-image-btn"><i class="fas fa-minus"></i></button>');
        
        inputGroup.append(input);
        inputGroup.append(removeBtn);
        
        removeBtn.on('click', function() {
            inputGroup.remove();
        });
        
        return inputGroup;
    };

    $('#add-multi-image-btn').on('click', function(e) {
        e.preventDefault();
        const wrapper = $('#multi-images-wrapper');
        if (wrapper.length) {
            wrapper.append(createMultiInput());
        }
    });

    $('#edit-add-multi-image-btn').on('click', function(e) {
        e.preventDefault();
        const wrapper = $('#edit-multi-images-wrapper');
        if (wrapper.length) {
            wrapper.append(createMultiInput());
        }
    });
    
    // Remove button for existing inputs
    $(document).on('click', '.remove-image-btn', function() {
        $(this).closest('.multi-image-input-group').remove();
    });

    // ---------------- Client-side Search ----------------
    $('.search-bar input').on('keyup', function() {
        let query = $(this).val().toLowerCase();
        $('#properties-list tbody tr').each(function() {
            let rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.indexOf(query) > -1);
        });
    });

    // ---------------- Add Property ----------------
    $('#add-property-form').submit(function(e){
        e.preventDefault();
        
        // Validate required fields before submission
        const transactionType = $('#transaction_type').val();
        const status = $('select[name="status"]').val();
        
        if (!transactionType || transactionType === '') {
            showToast('Please select a transaction type', 'error');
            return false;
        }
        
        if (!status || status === '') {
            showToast('Please select a status', 'error');
            return false;
        }
        
        const formData = new FormData();
        
        // Add all form fields
        formData.append('category', $('input[name="category"]').val());
        formData.append('location', $('input[name="location"]').val());
        formData.append('price', $('input[name="price"]').val());
        formData.append('status', status);
        formData.append('transaction_type', transactionType);
        formData.append('description', $('textarea[name="description"]').val() || '');
        formData.append('installment_years', $('input[name="installment_years"]').val() || '0');
        
        // Add user_id
        const userId = $('input[name="user_id"]').val();
        if (userId) {
            formData.append('user_id', userId);
        }
        
        // Add single image if selected and valid
        const imageInput = $('#property-image')[0];
        if (imageInput && imageInput.files.length > 0) {
            const mainFile = imageInput.files[0];
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            const mainExt = mainFile.name.split('.').pop().toLowerCase();
            const validExts = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
            if (mainFile.size > 0 && (validTypes.includes(mainFile.type) || validExts.includes(mainExt))) {
                formData.append('image', mainFile);
            } else {
                showToast('Main image must be jpeg, png, jpg, gif, or webp.', 'error');
                return false;
            }
        }
        
        // Add multiple images (only if files are selected and valid)
        // Filter out empty file inputs completely
        const fileInputs = $('#multi-images-wrapper input[type="file"]').filter(function() {
            return this.files && this.files.length > 0 && this.files[0].size > 0;
        });
        
        let invalidMulti = false;
        fileInputs.each(function(index) {
            const file = this.files[0];
            
            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            const fileExtension = file.name.split('.').pop().toLowerCase();
            const validExtensions = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
            
            if (validTypes.includes(file.type) || validExtensions.includes(fileExtension)) {
                formData.append('multiple_images[]', file);
            } else {
                showToast('Invalid file type for image ' + (index + 1) + '. Please use jpeg, png, jpg, gif, or webp.', 'error');
                invalidMulti = true;
            }
        });
        if (invalidMulti) {
            return false;
        }
        
        // Ensure CSRF token is included
        const csrfToken = $('meta[name="csrf-token"]').attr("content");
        if (csrfToken) {
            formData.append("_token", csrfToken);
        }

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res){
                showToast('Property added successfully!', 'success');

                const property = res.property;
                const newRow = `<tr data-description="${property.description || ''}" data-installment="${property.installment_years || 0}" data-transaction="${property.transaction_type || ''}">
                    <td>${property.id}</td>
                    <td>${property.category}</td>
                    <td>${property.location}</td>
                    <td>${Number(property.price).toLocaleString()} EGP</td>
                    <td>${property.status.charAt(0).toUpperCase() + property.status.slice(1)}</td>
                    <td>${property.user_id}</td>
                    <td>
                        <button class="btn btn-secondary btn-sm edit-btn" data-id="${property.id}"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="${property.id}"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>`;
                $('#properties-list tbody').prepend(newRow);
                $('#add-property-form')[0].reset();
                // Reset multi-image inputs to just one
                const wrapper = $('#multi-images-wrapper');
                if (wrapper.length) {
                    wrapper.find('.multi-image-input-group').slice(1).remove();
                }
            },
            error: function(xhr){
                let errorMessage = 'Error adding property';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage += ': ' + errors.join(', ');
                    } else if (xhr.responseJSON.message) {
                        errorMessage += ': ' + xhr.responseJSON.message;
                    }
                } else {
                    errorMessage += ': ' + (xhr.statusText || 'Unknown error');
                }
                console.error('Property creation error:', xhr.responseJSON || xhr);
                showToast(errorMessage, 'error');
            }
        });
        
        return false;
    });

    // ---------------- File Input Display ----------------
    $('#property-image').on('change', function() {
        console.log(this.files); // optional debug
    });
});
