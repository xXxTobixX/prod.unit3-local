document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    const signupForm = document.getElementById('signup-form');

    /**
     * Animated Notification System
     * @param {string} title - Title of the notification
     * @param {string} message - Message body
     * @param {string} type - 'success' or 'error'
     */
    function showNotification(title, message, type = 'success') {
        // Create container if it doesn't exist
        let container = document.querySelector('.toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container';
            document.body.appendChild(container);
        }

        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        toast.innerHTML = `
            <i class="fas ${icon}"></i>
            <div class="toast-content">
                <h4>${title}</h4>
                <p>${message}</p>
            </div>
        `;

        container.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => {
                toast.remove();
                if (container.childNodes.length === 0) {
                    container.remove();
                }
            }, 400);
        }, 5000);
    }

    // Handle Login
    if (loginForm) {
        const otpSection = document.getElementById('otp-section');
        const loginHeader = document.querySelector('.auth-header p');

        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(loginForm);

            fetch('ajax/auth.php?action=login', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Transition to OTP
                        loginForm.style.display = 'none';
                        otpSection.style.display = 'block';
                        if (loginHeader) loginHeader.textContent = "Please verify your identity";

                        showNotification('Success!', 'OTP was sent to your email', 'success');

                        // Show temp OTP for demo (remove in production)
                        if (data.temp_otp) {
                            setTimeout(() => {
                                showNotification('OTP Code', `Your demo OTP is: ${data.temp_otp}`, 'success');
                            }, 1000);
                        }

                        const firstInput = document.querySelector('.otp-inputs input');
                        if (firstInput) firstInput.focus();
                        startResendTimer();
                    } else {
                        showNotification('Login Failed', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('System Error', 'An error occurred. Please try again.', 'error');
                });
        });

        // OTP Input Logic
        const otpInputs = document.querySelectorAll('.otp-inputs input');
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasteData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6).split('');
                if (pasteData.length > 0) {
                    otpInputs.forEach((inp, idx) => {
                        if (pasteData[idx]) {
                            inp.value = pasteData[idx];
                        }
                    });
                    const targetIndex = pasteData.length < 6 ? pasteData.length : 5;
                    otpInputs[targetIndex].focus();
                }
            });
        });

        // OTP Verification
        const verifyBtn = document.getElementById('verify-otp');
        if (verifyBtn) {
            verifyBtn.addEventListener('click', () => {
                const code = Array.from(otpInputs).map(i => i.value).join('');
                if (code.length === 6) {
                    const email = document.getElementById('email').value;
                    const formData = new FormData();
                    formData.append('email', email);
                    formData.append('otp', code);

                    fetch('ajax/auth.php?action=verify-otp', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification('Verified', 'Redirecting to your dashboard...', 'success');
                                setTimeout(() => {
                                    if (data.role === 'admin') {
                                        window.location.href = 'dashboard/administrator/index.php';
                                    } else {
                                        if (data.profile_completed) {
                                            window.location.href = 'dashboard/users/index.php';
                                        } else {
                                            window.location.href = 'complete-profile.php';
                                        }
                                    }
                                }, 1500);
                            } else {
                                showNotification('Verification Failed', data.message, 'error');
                            }
                        });
                } else {
                    showNotification('Input Error', 'Please enter the complete 6-digit code.', 'error');
                }
            });
        }

        // Resend Timer
        let timerInterval;
        function startResendTimer() {
            let timeLeft = 120;
            const timerEl = document.getElementById('timer');
            const resendLink = document.getElementById('resend-link');

            if (!timerEl || !resendLink) return;

            resendLink.classList.add('disabled');

            clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                timeLeft--;
                timerEl.textContent = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    resendLink.innerHTML = 'Resend Code';
                    resendLink.classList.remove('disabled');
                }
            }, 1000);
        }
    }

    // Handle Signup
    if (signupForm) {
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        const requirements = document.getElementById('passwordRequirements');

        if (togglePassword) {
            togglePassword.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                togglePassword.classList.toggle('fa-eye');
                togglePassword.classList.toggle('fa-eye-slash');
            });
        }

        if (passwordInput && requirements) {
            passwordInput.addEventListener('focus', () => requirements.classList.add('visible'));
            passwordInput.addEventListener('blur', () => requirements.classList.remove('visible'));
            passwordInput.addEventListener('input', () => {
                const val = passwordInput.value;
                validateReq('length', val.length >= 8);
                validateReq('uppercase', /[A-Z]/.test(val));
                validateReq('number', /[0-9]/.test(val));
                validateReq('special', /[^A-Za-z0-9]/.test(val));
            });
        }

        function validateReq(id, isValid) {
            const el = document.getElementById(id);
            if (isValid) {
                el.classList.add('valid');
                el.querySelector('i').className = 'fas fa-check-circle';
            } else {
                el.classList.remove('valid');
                el.querySelector('i').className = 'fas fa-circle';
            }
        }

        // Auto-capitalize First and Last Name
        const nameInputs = [document.getElementById('firstname'), document.getElementById('lastname')];
        nameInputs.forEach(input => {
            if (input) {
                input.addEventListener('input', (e) => {
                    const words = e.target.value.split(' ');
                    const capitalizedWords = words.map(word => {
                        return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
                    });
                    e.target.value = capitalizedWords.join(' ');
                });
            }
        });

        signupForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const allValid = document.querySelectorAll('.requirement.valid').length === 4;
            if (!allValid) {
                showNotification('Validation Error', 'Please meet all password requirements before signing up.', 'error');
                passwordInput.focus();
                return;
            }

            const formData = new FormData(signupForm);
            fetch('ajax/auth.php?action=signup', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Registration Success!', 'Your account has been created. Redirecting to login...', 'success');
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 2000);
                    } else {
                        showNotification('Registration Failed', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('System Error', 'An error occurred. Please try again.', 'error');
                });
        });
    }

    // Page Transition Animation
    const authLinks = document.querySelectorAll('.auth-footer a, .back-link, .forgot-password');
    const container = document.querySelector('.auth-container');

    authLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            if (href && href !== '#' && href !== 'landing.php') {
                e.preventDefault();
                container.classList.add('fade-out');
                setTimeout(() => {
                    window.location.href = href;
                }, 400);
            }
        });
    });
});
