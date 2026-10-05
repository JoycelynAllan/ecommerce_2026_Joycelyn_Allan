document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-pass').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = btn.parentElement.querySelector('input');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.textContent = show ? 'Hide' : 'Show';
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('register-form');

    if (!form) return;

    form.addEventListener('submit', function (e) {

        let valid = true;

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneRegex = /^[0-9+\-\s]{7,15}$/;

        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const pass = document.getElementById('pass').value;
        const contact = document.getElementById('contact').value.trim();

        clearErrors();

        if (name.length < 2) {
            showError('name-error', 'Please enter your full name.');
            valid = false;
        }

        if (!emailRegex.test(email)) {
            showError('email-error', 'Please enter a valid email.');
            valid = false;
        }

        let passError = '';

        if (pass.length < 8) {
            passError = 'Password must be at least 8 characters.';
        } else if (!/[A-Z]/.test(pass)) {
            passError = 'Password must include at least one uppercase letter.';
        } else if (!/[a-z]/.test(pass)) {
            passError = 'Password must include at least one lowercase letter.';
        } else if (!/[0-9]/.test(pass)) {
            passError = 'Password must include at least one number.';
        }

        if (passError) {
            showError('pass-error', passError);
            valid = false;
        }

        if (!phoneRegex.test(contact)) {
            showError('contact-error', 'Please enter a valid phone number.');
            valid = false;
        }

        const confirmPass = document.getElementById('confirm_pass').value;

        if (pass !== confirmPass) {
            showError('confirm-error', 'Passwords do not match.');
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        } else {
            const btn = document.getElementById('submit-btn');
            btn.disabled = true;
            btn.textContent = 'Submitting...';
        }
    });

    function showError(id, message) {
        const el = document.getElementById(id);
        if (el) el.textContent = message;
    }

    function clearErrors() {
        document.querySelectorAll('.error-text').forEach(el => el.textContent = '');
    }
});