const registerForm = document.getElementById('registerForm');
const nameInput = document.getElementById('name');
const emailInput = document.getElementById('email');
const phoneInput = document.getElementById('phone');
const birthInput = document.getElementById('birth_date');
const genderInput = document.getElementById('gender');
const locationInput = document.getElementById('location');
const passwordInput = document.getElementById('password');
const confirmInput = document.getElementById('password_confirmation');
const roleInput = document.getElementById('role');

const todayIso = new Date().toISOString().split('T')[0];
if (birthInput) birthInput.setAttribute('max', todayIso);

// Debounce utility
function debounce(fn, delay = 300) {
    let timer;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

// Fields validators and messages
const fields = {
    name: { validator: v => v.trim().length >= 3, message: 'Name must be at least 3 characters.' },
    email: { validator: v => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v), message: 'Enter a valid email address.' },
    phone: { validator: v => /^\d{10,11}$/.test(v), message: 'Phone must be 10 or 11 digits.' },
    birth_date: { validator: v => !!v && new Date(v) <= new Date(), message: 'Select a valid birth date that is not in the future.' },
    gender: { validator: v => !!v, message: 'Select a gender.' },
    location: { validator: v => v.trim().length > 0, message: 'Enter your location.' },
    password: { validator: v => v.length >= 8, message: 'Password must be at least 8 characters.' },
    confirm: { validator: v => v === passwordInput.value && v.length >= 8, message: 'Passwords must match.' },
    role: { validator: v => !!v, message: 'Select a role.' },
};

// Requirement list
const requirementItems = {};
document.querySelectorAll('#requirementsList li').forEach(item => {
    requirementItems[item.dataset.rule] = item;
    item.classList.remove('valid', 'invalid');
});

// Validate a single field
function validateField(element, key) {
    const { validator, message } = fields[key];
    const isValid = validator(element.value);

    // Input styling
    element.classList.remove('is-valid', 'is-invalid');
    element.classList.add(isValid ? 'is-valid' : 'is-invalid');
    element.setAttribute('aria-invalid', !isValid);

    // Feedback
    const feedback = document.getElementById(`${key}Feedback`);
    if (feedback) {
        feedback.textContent = isValid ? 'Looks good' : message;
        feedback.style.color = isValid ? 'green' : 'red';
    }

    // Requirement list
    const reqItem = requirementItems[key];
    if (reqItem) {
        reqItem.classList.remove('valid', 'invalid');
        reqItem.classList.add(isValid ? 'valid' : 'invalid');
        const icon = reqItem.querySelector('.status-icon');
        if (icon) icon.textContent = isValid ? '✔' : '•';
    }

    return isValid;
}

// Input listeners
const inputs = [nameInput, emailInput, phoneInput, birthInput, genderInput, locationInput, passwordInput, confirmInput].filter(Boolean);
inputs.forEach(input => {
    const key = input.id === 'password_confirmation' ? 'confirm' : input.id;
    input.addEventListener('input', debounce(() => validateField(input, key)));
});

// Role change listener
if (roleInput) {
    roleInput.addEventListener('change', () => validateField(roleInput, 'role'));
}

// Form submit
if (registerForm) {
    registerForm.addEventListener('submit', function (e) {
        let isValid = true;
        inputs.forEach(input => {
            const key = input.id === 'password_confirmation' ? 'confirm' : input.id;
            isValid = validateField(input, key) && isValid;
        });
        isValid = validateField(roleInput, 'role') && isValid;

        if (!isValid) e.preventDefault();
    });
}

// Password toggle
function togglePassword(button) {
    const target = document.getElementById(button.dataset.passwordToggle);
    if (!target) return;

    const showing = target.type === 'text';
    target.type = showing ? 'password' : 'text';
    button.setAttribute('aria-pressed', !showing);

    const icon = button.querySelector('i');
    if (icon) {
        icon.classList.toggle('bi-eye', showing);
        icon.classList.toggle('bi-eye-slash', !showing);
    }
}
document.querySelectorAll('[data-password-toggle]').forEach(btn =>
    btn.addEventListener('click', () => togglePassword(btn))
);
