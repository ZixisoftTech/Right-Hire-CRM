document.addEventListener('DOMContentLoaded', function() {

    // Toggle Password Visibility
    const togglePassword = document.getElementById('togglePassword');
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const password = document.getElementById('password');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    }

    // Generic toggle for reset password page
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    });

    // Password Strength Meter
    const newPasswordInput = document.getElementById('password');
    if (newPasswordInput && document.getElementById('passwordStrength')) {
        newPasswordInput.addEventListener('input', function() {
            const val = this.value;
            const meter = document.getElementById('passwordStrength');
            let strength = 0;

            if (val.length >= 8) strength += 25;
            if (val.match(/[a-z]+/)) strength += 25;
            if (val.match(/[A-Z]+/)) strength += 25;
            if (val.match(/[0-9]+/) || val.match(/[$@#&!]+/)) strength += 25;

            meter.style.width = strength + '%';
            meter.className = 'progress-bar';

            if (strength <= 25) {
                meter.classList.add('strength-weak');
            } else if (strength <= 50) {
                meter.classList.add('strength-medium');
            } else {
                meter.classList.add('strength-strong');
            }
        });
    }

    // OTP Input Handling (Auto-focus, Backspace, Paste)
    const otpInputs = document.querySelectorAll('.otp-input');
    if (otpInputs.length > 0) {
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').slice(0, 6);
                if (/^\d+$/.test(pastedData)) {
                    pastedData.split('').forEach((char, i) => {
                        if (otpInputs[i]) {
                            otpInputs[i].value = char;
                            if (i < otpInputs.length - 1) otpInputs[i + 1].focus();
                        }
                    });
                }
            });
        });
    }

    // Timer for Resend OTP
    const timerText = document.getElementById('timerText');
    const resendOtpBtn = document.getElementById('resendOtpBtn');

    if (timerText && resendOtpBtn) {
        let timeLeft = 60; // 60 seconds
        const timer = setInterval(() => {
            timeLeft--;
            const m = Math.floor(timeLeft / 60).toString().padStart(2, '0');
            const s = (timeLeft % 60).toString().padStart(2, '0');
            timerText.innerText = `(${m}:${s})`;

            if (timeLeft <= 0) {
                clearInterval(timer);
                timerText.innerText = '';
                resendOtpBtn.disabled = false;
            }
        }, 1000);

        resendOtpBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (this.disabled) return;

            this.disabled = true;
            timerText.innerText = '(Wait...)';

            fetch('/api/auth/resend-otp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': document.querySelector('input[name="csrf_token"]')?.value || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Sent!', text: data.message, showConfirmButton: false, timer: 1500 });
                    // Reload to restart timer logic cleanly
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    resendOtpBtn.disabled = false;
                }
            })
            .catch(error => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong.' });
                resendOtpBtn.disabled = false;
            });
        });
    }

    // Generic AJAX Form Submission
    const handleFormSubmit = (formId) => {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = this.querySelector('button[type="submit"]');
            const btnText = btn.querySelector('.btn-text');
            const spinner = btn.querySelector('.spinner-border');

            btn.disabled = true;
            spinner.classList.remove('d-none');

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json().catch(() => ({ status: 'error', message: 'An unexpected error occurred.' })))
            .then(data => {
                if (data.status === 'success') {
                    if (data.message) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            if (data.redirect) window.location.href = data.redirect;
                        });
                    } else if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message || 'Something went wrong!'
                    });
                    btn.disabled = false;
                    spinner.classList.add('d-none');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Network error or server is down.'
                });
                btn.disabled = false;
                spinner.classList.add('d-none');
            });
        });
    };

    handleFormSubmit('loginForm');
    handleFormSubmit('otpForm');
    handleFormSubmit('recoverForm');
    handleFormSubmit('resetForm');
});
