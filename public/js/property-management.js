$(document).ready(function() {

    // =================== Toast Function ===================
    function showToast(message, type = 'error') {
        const container = $('#toast-container');
        if (!container.length) return;

        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };

        const toast = $(`
            <div class="toast ${type}">
                <i class="fas ${icons[type] || 'fa-exclamation-circle'} toast-icon"></i>
                <span class="toast-message">${message}</span>
                <button class="toast-close"><i class="fas fa-times"></i></button>
            </div>
        `);

        container.append(toast);
        toast.find('.toast-close').click(() => toast.remove());
        setTimeout(() => toast.fadeOut(300, () => toast.remove()), 5000);
    }

    window.pmShowToast = showToast;

    // =================== Multi-file Inputs ===================
    const createMultiInput = () => $('<input type="file" class="form-control mb-2" name="multiple_images[]" accept="image/*">');

    $('#add-multi-image-btn').click(e => {
        e.preventDefault();
        $('#multi-images-wrapper').append(createMultiInput());
    });

    $('#edit-add-multi-image-btn').click(e => {
        e.preventDefault();
        $('#edit-multi-images-wrapper').append(createMultiInput());
    });

    // =================== Client-side Table Search ===================
    $('.search-bar input').on('keyup', function() {
        const query = $(this).val().toLowerCase();
        $('#properties-list tbody tr').each(function() {
            $(this).toggle($(this).text().toLowerCase().includes(query));
        });
    });

    // =================== Add Property AJAX ===================
    $('#add-property-form').submit(function(e){
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);

        $.ajax({
            url: $(form).attr('action'),
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
                form.reset(); // reset form after success
            },
            error: function(xhr){
                showToast('Error adding property: ' + (xhr.responseJSON?.message || 'Unknown error'), 'error');
            }
        });
    });

    // =================== Future-ready: Edit & Delete Buttons (AJAX placeholders) ===================
    // Example for dynamically added rows
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        // Your existing edit-modal JS handles this
        $('#edit-modal').modal('show');
        // Fill modal with property data as needed
    });

    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        // Your existing delete-modal JS handles this
        $('#delete-modal').modal('show');
        $('#delete-modal').data('id', id);
    });

});

