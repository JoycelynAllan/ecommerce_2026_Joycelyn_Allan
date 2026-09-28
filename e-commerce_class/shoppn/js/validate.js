document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('register-form');

    if (!form) return;

    form.addEventListener('submit', function (e) {

        let valid = true;

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneRegex = /^[0-9+\-\s]{7,15}$/;
        const passRegex = /^(?=.*\d).{8,}$/;

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

        if (!passRegex.test(pass)) {
            showError('pass-error', 'Password must be at least 8 characters and include a number.');
            valid = false;
        }

        if (!phoneRegex.test(contact)) {
            showError('contact-error', 'Please enter a valid phone number.');
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
        document.querySelectorAll('.error').forEach(el => el.textContent = '');
    }
});