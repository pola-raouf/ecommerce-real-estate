// users-management.js
// Requires jQuery 3.7.1 (you already include it in the page)

$(function () {
    // ------------------- Config -------------------
    const todayIso = new Date().toISOString().split('T')[0];
    $('input[name="birth_date"], #edit-birth_date').attr('max', todayIso);

    const allowedRoles = ["admin", "seller", "buyer"];
    const allowedGenders = ["male", "female", "other"];

    // ------------------- Utilities -------------------
    function debounce(fn, delay = 300) {
        let timer = null;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    function safeText(value) {
        return value == null ? '' : String(value);
    }

    // ------------------- AJAX wrapper -------------------
    function apiRequest({ url, method = 'GET', data = {}, onSuccess, onError }) {
        $.ajax({
            url,
            type: method,
            dataType: 'json',
            data,
        })
            .done(function (res) {
                if (!res || res.success === false) {
                    const msg = res?.message || 'Server returned an error';
                    (onError || defaultErrorHandler)(msg, res);
                    return;
                }
                (onSuccess || function () {})(res);
            })
            .fail(function (xhr) {
                const msg = xhr.responseJSON?.message || xhr.statusText || 'Network error';
                (onError || defaultErrorHandler)(msg, xhr);
            });
    }

    function defaultErrorHandler(message) {
        showToast(message || 'An error occurred', 'error');
    }

    // ------------------- Toast notifications -------------------
    function showToast(message, type = 'error') {
        const container = $('#toast-container');
        if (!container.length) return;

        const icon = (type === 'success' && 'fa-check-circle') ||
                     (type === 'warning' && 'fa-exclamation-triangle') ||
                     (type === 'info' && 'fa-info-circle') ||
                     'fa-exclamation-circle';

        const $t = $(`
            <div class="toast ${type}">
                <i class="fas ${icon} toast-icon" aria-hidden="true"></i>
                <span class="toast-message">${escapeHtml(message)}</span>
                <button class="toast-close" type="button" aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
        `);

        container.append($t);
        $t.find('.toast-close').on('click', () => $t.remove());
        setTimeout(() => $t.fadeOut(300, () => $t.remove()), 4500);
    }

    // simple string escape to avoid injecting HTML into toast messages
    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    // ------------------- Validation -------------------
    function validateUserPayload(payload, options = { requirePassword: true }) {
        const errors = [];

        if (!payload.name || payload.name.trim().length < 3) {
            errors.push('Name must be at least 3 characters.');
        }

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!payload.email || !emailPattern.test(payload.email)) {
            errors.push('Provide a valid email address.');
        }

        if (!payload.phone || !/^\d{10,11}$/.test(payload.phone)) {
            errors.push('Phone number must contain 10 or 11 digits.');
        }

        if (!payload.role || !allowedRoles.includes((payload.role || '').toLowerCase())) {
            errors.push('Select a valid role.');
        }

        if (!payload.birth_date) {
            errors.push('Birth date is required.');
        } else if (new Date(payload.birth_date) > new Date()) {
            errors.push('Birth date cannot be in the future.');
        }

        if (!payload.gender || !allowedGenders.includes((payload.gender || '').toLowerCase())) {
            errors.push('Select a valid gender.');
        }

        if (!payload.location || payload.location.trim().length < 2) {
            errors.push('Location is required.');
        }

        const password = payload.password || '';
        if (options.requirePassword && password.length < 8) {
            errors.push('Password must be at least 8 characters.');
        } else if (!options.requirePassword && password.length > 0 && password.length < 8) {
            errors.push('New password must be at least 8 characters.');
        }

        return errors;
    }

    // ------------------- DOM helpers -------------------
    function buildUserRow(user) {
        const roleLabel = user.role ? (user.role.charAt(0).toUpperCase() + user.role.slice(1)) : '';
        const tpl = `
        <tr data-id="${escapeAttr(user.id)}"
            data-birth_date="${escapeAttr(user.birth_date || '')}"
            data-gender="${escapeAttr(user.gender || '')}"
            data-location="${escapeAttr(user.location || '')}"
            data-role="${escapeAttr(user.role || '')}">
            <td>${escapeHtml(user.id)}</td>
            <td>${escapeHtml(user.name)}</td>
            <td>${escapeHtml(user.email)}</td>
            <td>${escapeHtml(user.phone)}</td>
            <td>${escapeHtml(roleLabel)}</td>
            <td>
                <button class="btn btn-secondary btn-sm edit-btn" type="button"><i class="fas fa-edit"></i></button>
                <button class="btn btn-danger btn-sm delete-btn" type="button" data-id="${escapeAttr(user.id)}"><i class="fas fa-trash"></i></button>
            </td>
        </tr>`;
        return tpl;
    }

    function escapeAttr(v) {
        return String(v || '').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function findRowById(id) {
        return $(`.users-table tbody tr`).filter(function () {
            return String($(this).data('id')) === String(id);
        }).first();
    }

    function serializeUserForm($form) {
        return {
            name: $form.find('input[name="name"]').val(),
            email: $form.find('input[name="email"]').val(),
            password: $form.find('input[name="password"]').val(),
            phone: $form.find('input[name="phone"]').val(),
            role: $form.find('select[name="role"]').val(),
            birth_date: $form.find('input[name="birth_date"]').val(),
            gender: $form.find('select[name="gender"]').val(),
            location: $form.find('input[name="location"]').val(),
        };
    }

    // ------------------- Modal helpers -------------------
    function openModal($modal) {
        $modal.css({ display: 'flex', opacity: 0 }).animate({ opacity: 1 }, 200);
    }
    function closeModal($modal) {
        $modal.animate({ opacity: 0 }, 200, function () { $modal.css('display', 'none'); });
    }

    // bind modal close buttons
    $('#delete-close, #delete-cancel').on('click', () => closeModal($('#delete-modal')));
    $('#edit-close, #edit-cancel').on('click', () => closeModal($('#edit-modal')));

    // ------------------- CSRF Setup -------------------
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // ------------------- Search (debounced) -------------------
    const searchRoute = $('.search-bar').data('route') || '/users/search';
    $('.search-bar input').on('keyup', debounce(function () {
        const q = $(this).val().trim();
        apiRequest({
            url: searchRoute,
            method: 'GET',
            data: { query: q },
            onSuccess: function (res) {
                // res may be array or an object; the controller returns array for non-ajax view in original code
                const users = Array.isArray(res) ? res : (res.data || []);
                let html = '';
                if (!Array.isArray(users) || users.length === 0) {
                    html = '<tr><td colspan="6" class="text-center">No users found</td></tr>';
                } else {
                    users.forEach(u => { html += buildUserRow(u); });
                }
                $('.users-table tbody').html(html);
            }
        });
    }, 300));

    // ------------------- Add User -------------------
    $('#add-user-form').on('submit', function (e) {
        e.preventDefault();
        const $form = $(this);
        const url = $form.attr('action');

        const payload = serializeUserForm($form);
        const errors = validateUserPayload(payload, { requirePassword: true });
        if (errors.length) {
            errors.forEach(err => showToast(err, 'error'));
            return;
        }

        apiRequest({
            url,
            method: 'POST',
            data: $form.serialize(), // keep original form encoding for server compatibility
            onSuccess: function (res) {
                // controller returns { success: true, data: $user } per cleaned controller
                const user = res.data || res.user || res;
                if (!user || !user.id) {
                    showToast('Invalid server response', 'error');
                    return;
                }
                $('.users-table tbody').append(buildUserRow(user));
                $form[0].reset();
                showToast('User added successfully!', 'success');
            }
        });
    });

    // ------------------- Delete User -------------------
    let deleteState = { url: null, $row: null };
    function openDeleteModal(url, $row) {
        deleteState.url = url;
        deleteState.$row = $row;
        openModal($('#delete-modal'));
    }
    function clearDeleteState() { deleteState = { url: null, $row: null }; }

    $('#delete-form').on('submit', function (e) {
        e.preventDefault();
        if (!deleteState.url || !deleteState.$row) return showToast('Nothing to delete', 'warning');

        apiRequest({
            url: deleteState.url,
            method: 'POST',
            data: $(this).serialize() + '&_method=DELETE',
            onSuccess: function () {
                deleteState.$row.remove();
                closeModal($('#delete-modal'));
                showToast('User deleted successfully!', 'success');
                clearDeleteState();
            }
        });
    });

    $(document).on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        const $row = $(this).closest('tr');
        openDeleteModal(`/users/${id}`, $row);
    });

    // ------------------- Edit User -------------------
    function openEditModalWithUser(user) {
        $('#edit-user-id').val(user.id);
        $('#edit-name').val(user.name);
        $('#edit-email').val(user.email);
        $('#edit-phone').val(user.phone);
        $('#edit-role').val((user.role || '').toLowerCase());
        $('#edit-birth_date').val(user.birth_date || '');
        $('#edit-gender').val((user.gender || '').toLowerCase());
        $('#edit-location').val(user.location || '');
        $('#edit-password').val('');
        openModal($('#edit-modal'));
    }

    $(document).on('click', '.edit-btn', function () {
        const $row = $(this).closest('tr');
        const user = {
            id: $row.data('id'),
            name: $row.find('td:nth-child(2)').text().trim(),
            email: $row.find('td:nth-child(3)').text().trim(),
            phone: $row.find('td:nth-child(4)').text().trim(),
            role: $row.data('role') || $row.find('td:nth-child(5)').text().trim(),
            birth_date: $row.data('birth_date') || '',
            gender: $row.data('gender') || '',
            location: $row.data('location') || ''
        };
        openEditModalWithUser(user);
    });

    $('#edit-user-form').on('submit', function (e) {
        e.preventDefault();
        const userId = $('#edit-user-id').val();
        const payload = {
            name: $('#edit-name').val(),
            email: $('#edit-email').val(),
            phone: $('#edit-phone').val(),
            role: $('#edit-role').val(),
            birth_date: $('#edit-birth_date').val(),
            gender: $('#edit-gender').val(),
            location: $('#edit-location').val(),
            password: $('#edit-password').val(),
        };

        const errors = validateUserPayload(payload, { requirePassword: false });
        if (errors.length) {
            errors.forEach(err => showToast(err, 'error'));
            return;
        }

        apiRequest({
            url: `/users/${userId}`,
            method: 'POST', // using _method=PUT
            data: {
                _method: 'PUT',
                name: payload.name,
                email: payload.email,
                password: payload.password,
                phone: payload.phone,
                role: payload.role,
                birth_date: payload.birth_date,
                gender: payload.gender,
                location: payload.location
            },
            onSuccess: function (res) {
                const updated = res.data || res.user || res;
                // update DOM row if present
                const $row = findRowById(userId);
                if ($row.length) {
                    $row.find('td:nth-child(2)').text(payload.name);
                    $row.find('td:nth-child(3)').text(payload.email);
                    $row.find('td:nth-child(4)').text(payload.phone);
                    $row.find('td:nth-child(5)').text(payload.role ? (payload.role.charAt(0).toUpperCase() + payload.role.slice(1)) : '');
                    $row.data('birth_date', payload.birth_date);
                    $row.data('gender', payload.gender);
                    $row.data('location', payload.location);
                    $row.data('role', payload.role);
                }
                showToast('User updated successfully!', 'success');
                closeModal($('#edit-modal'));
            }
        });
    });

    // ------------------- Initial load focus (small UX) -------------------
    $('#add-user-form input[name="name"]').focus();
});
