// ------------------------------
// LOGIN JS (cleaned, debounced)
// ------------------------------
const loginForm = document.getElementById('loginForm');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const emailFeedback = document.getElementById('email-feedback');
const passwordFeedback = document.getElementById('password-feedback');

const messages = {
    emailInvalid: 'Enter a valid email address.',
    emailNotFound: 'We could not find an account with this email.',
    emailValid: 'Looks good.',
    passwordShort: 'Password must be at least 8 characters.',
    passwordValid: 'Looks good.'
};

// Simple email regex
const isEmailValid = (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);

// Update input classes & messages
const setFieldState = (input, isValid, messageEl, message = '') => {
    if (!input || !messageEl) return;
    input.classList.remove('is-valid', 'is-invalid');
    input.classList.add(isValid ? 'is-valid' : 'is-invalid');
    messageEl.textContent = message;
    messageEl.classList.toggle('success', isValid && !!message);
};

// Fetch email existence from server
const fetchEmailStatus = async (value) => {
    if (!loginForm) return null;
    const url = loginForm.dataset.emailExists;
    const token = loginForm.dataset.csrf;
    if (!url || !token) return null;

    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json' 
        },
        body: JSON.stringify({ email: value }),
    });
    return response.json();
};

// ------------------------------
// EMAIL VALIDATION (debounced)
// ------------------------------
if (loginForm && emailInput) {
    let emailTimeout;
    emailInput.addEventListener('input', () => {
        clearTimeout(emailTimeout);
        emailTimeout = setTimeout(async () => {
            const value = emailInput.value.trim();
            if (!value) return setFieldState(emailInput, false, emailFeedback, '');
            if (!isEmailValid(value)) return setFieldState(emailInput, false, emailFeedback, messages.emailInvalid);

            try {
                const result = await fetchEmailStatus(value);
                if (result?.exists) setFieldState(emailInput, true, emailFeedback, messages.emailValid);
                else setFieldState(emailInput, false, emailFeedback, messages.emailNotFound);
            } catch {
                setFieldState(emailInput, false, emailFeedback, 'Unable to verify email right now.');
            }
        }, 500);
    });
}

// ------------------------------
// PASSWORD VALIDATION
// ------------------------------
if (passwordInput) {
    passwordInput.addEventListener('input', () => {
        const value = passwordInput.value;
        if (!value) return setFieldState(passwordInput, false, passwordFeedback, '');
        if (value.length < 8) setFieldState(passwordInput, false, passwordFeedback, messages.passwordShort);
        else setFieldState(passwordInput, true, passwordFeedback, messages.passwordValid);
    });
}

// ------------------------------
// FORM SUBMIT CHECK
// ------------------------------
if (loginForm) {
    loginForm.addEventListener('submit', (event) => {
        let isValid = true;
        const emailValue = emailInput.value.trim();

        if (!isEmailValid(emailValue)) {
            setFieldState(emailInput, false, emailFeedback, messages.emailInvalid);
            isValid = false;
        }

        if (passwordInput.value.length < 8) {
            setFieldState(passwordInput, false, passwordFeedback, messages.passwordShort);
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault();
            event.stopPropagation();
        }
    });
}

// ------------------------------
// PASSWORD TOGGLE
// ------------------------------
const togglePasswordButtons = document.querySelectorAll('[data-password-toggle]');
togglePasswordButtons.forEach((button) => {
    button.addEventListener('click', () => {
        const targetId = button.dataset.passwordToggle;
        const targetInput = document.getElementById(targetId);
        if (!targetInput) return;

        const showing = targetInput.type === 'text';
        targetInput.type = showing ? 'password' : 'text';
        button.setAttribute('aria-pressed', showing ? 'false' : 'true');

        const icon = button.querySelector('i');
        if (icon) {
            icon.classList.toggle('bi-eye', showing);
            icon.classList.toggle('bi-eye-slash', !showing);
        }
    });
});
