document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const { profileUpdateRoute, profileDeleteRoute, profileCheckPasswordRoute } = window;

    const profileForm = document.getElementById('profileForm');
    const profileInput = document.getElementById('profileInput');
    const previewImage = document.getElementById('previewImage');
    const deleteBtnWrapper = document.querySelector('.delete-btn-wrapper');
    const deleteBtn = deleteBtnWrapper?.querySelector('button');
    const alertContainer = document.getElementById('alert-container');
    const savePhotoBtn = document.getElementById('savePhotoBtn');

    const currentPasswordInput = document.getElementById('current_password');
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const passwordIcon = document.getElementById('passwordIcon');
    const passwordText = document.getElementById('passwordText');
    const passwordMatchText = document.getElementById('passwordMatchText');

    if (!profileForm || !profileInput || !previewImage || !deleteBtn || !alertContainer || !savePhotoBtn) return;

    // =================== Utility Functions ===================
    const showAlert = (message, type = 'success') => {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type}`;
        alert.textContent = message;
        alertContainer.prepend(alert);
        setTimeout(() => alert.remove(), 4000);
    };

    const updateNavAvatars = (url) => {
        if (!url) return;
        const stampedUrl = `${url}${url.includes('?') ? '&' : '?'}_=${Date.now()}`;
        document.querySelectorAll('.navbar img.profile-img').forEach(img => img.src = stampedUrl);
    };

    const showDeleteButton = (show) => {
        if (deleteBtnWrapper) deleteBtnWrapper.style.display = show ? 'flex' : 'none';
    };

    const updatePasswordMatchState = () => {
        if (!passwordMatchText) return;

        const newVal = newPasswordInput?.value || '';
        const confirmVal = confirmPasswordInput?.value || '';

        if (!newVal && !confirmVal) {
            passwordMatchText.textContent = '';
            passwordMatchText.style.color = '';
            return;
        }

        if (newVal === confirmVal) {
            passwordMatchText.textContent = 'Passwords match';
            passwordMatchText.style.color = '#10b981';
        } else {
            passwordMatchText.textContent = 'Passwords do not match';
            passwordMatchText.style.color = '#ef4444';
        }
    };

    // =================== Form Setup ===================
    const birthDateInput = profileForm.querySelector('input[name="birth_date"]');
    if (birthDateInput) birthDateInput.max = new Date().toISOString().split('T')[0];

    showDeleteButton(previewImage.dataset.hasImage === '1');

    // =================== Profile Picture Handling ===================
    let selectedFile = null;

    profileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) {
            selectedFile = null;
            savePhotoBtn.style.display = 'none';
            return;
        }

        const allowedTypes = ["image/jpeg", "image/png", "image/jpg", "image/webp", "image/gif"];
        if (!allowedTypes.includes(file.type)) {
            showAlert('Invalid file type. Only images are allowed.', 'danger');
            profileInput.value = '';
            selectedFile = null;
            savePhotoBtn.style.display = 'none';
            return;
        }

        selectedFile = file;
        const reader = new FileReader();
        reader.onload = () => {
            previewImage.src = reader.result;
            // ✅ Show delete button immediately after selecting a new photo
            showDeleteButton(true);
        };
        reader.readAsDataURL(file);

        savePhotoBtn.style.display = 'block';
    });

    savePhotoBtn.addEventListener('click', async () => {
        if (!selectedFile) return showAlert('No file selected.', 'danger');

        savePhotoBtn.disabled = true;
        savePhotoBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Uploading...';

        const formData = new FormData();
        formData.append('profile_image', selectedFile);
        formData.append('_method', 'PUT');
        formData.append('_token', csrfToken);

        ['name', 'email', 'phone', 'birth_date', 'gender', 'location'].forEach(field => {
            const input = profileForm.querySelector(`[name="${field}"]`);
            if (input) formData.append(field, input.value || '');
        });

        try {
            const res = await fetch(profileUpdateRoute, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                previewImage.src = data.profile_image;
                // ✅ Show delete button instantly after saving
                showDeleteButton(true);
                showAlert(data.message || 'Profile picture updated!');
                updateNavAvatars(data.profile_image);

                savePhotoBtn.style.display = 'none';
                selectedFile = null;
                profileInput.value = '';
            } else if (data.errors) {
                Object.values(data.errors).flat().forEach(msg => showAlert(msg, 'danger'));
            }
        } catch (err) {
            console.error(err);
            showAlert('Error uploading photo. Please try again.', 'danger');
        } finally {
            savePhotoBtn.disabled = false;
            savePhotoBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Save Photo';
        }
    });

    deleteBtn.addEventListener('click', async () => {
        if (!confirm('Are you sure you want to delete your profile picture?')) return;

        try {
            const res = await fetch(profileDeleteRoute, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            });
            const data = await res.json();
            if (data.success) {
                // ✅ Show default profile image immediately
                const defaultImage = '/images/default-profile.png';
                previewImage.src = `${defaultImage}?_=${Date.now()}`;
                showDeleteButton(false);
                showAlert(data.message || 'Profile picture deleted!');
                updateNavAvatars(defaultImage);

                selectedFile = null;
                profileInput.value = '';
                savePhotoBtn.style.display = 'none';
            }
        } catch (err) {
            console.error(err);
        }
    });

    // =================== Password Validation ===================
    let passwordTimeout;
    if (currentPasswordInput && passwordIcon && passwordText && profileCheckPasswordRoute) {
        currentPasswordInput.addEventListener('input', () => {
            clearTimeout(passwordTimeout);
            const value = currentPasswordInput.value.trim();

            const feedback = document.getElementById('passwordFeedback');
            if (!value) {
                passwordIcon.className = '';
                passwordIcon.innerHTML = '';
                passwordText.textContent = '';
                passwordText.style.color = '';
                if (feedback) feedback.style.display = 'none';
                return;
            }

            if (feedback) feedback.style.display = 'inline-flex';
            passwordIcon.className = 'fas fa-spinner fa-spin me-1';
            passwordIcon.style.color = '#6b7280';
            passwordText.textContent = 'Checking...';
            passwordText.style.color = '#6b7280';

            passwordTimeout = setTimeout(async () => {
                try {
                    const res = await fetch(profileCheckPasswordRoute, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ current_password: value })
                    });
                    const data = await res.json();
                    if (data.valid) {
                        passwordIcon.className = 'fas fa-check-circle me-1';
                        passwordIcon.style.color = '#10b981';
                        passwordText.textContent = data.message || 'Password is valid';
                        passwordText.style.color = '#10b981';
                    } else {
                        passwordIcon.className = 'fas fa-times-circle me-1';
                        passwordIcon.style.color = '#ef4444';
                        passwordText.textContent = data.message || 'Password is invalid';
                        passwordText.style.color = '#ef4444';
                    }
                } catch (err) {
                    console.error('Password check error:', err);
                    passwordIcon.className = 'fas fa-exclamation-circle me-1';
                    passwordIcon.style.color = '#f59e0b';
                    passwordText.textContent = 'Error checking password';
                    passwordText.style.color = '#f59e0b';
                }
            }, 500);
        });
    }

    // =================== Password Toggle ===================
    document.querySelectorAll('[data-password-toggle]').forEach(button => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.passwordToggle);
            if (!input) return;

            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            button.setAttribute('aria-pressed', !showing);

            const icon = button.querySelector('i');
            if (icon) icon.classList.toggle('bi-eye-slash', !showing);
        });
    });

    // =================== Password Match Checking ===================
    newPasswordInput?.addEventListener('input', updatePasswordMatchState);
    confirmPasswordInput?.addEventListener('input', updatePasswordMatchState);
});
