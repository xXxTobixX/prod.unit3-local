document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    const signupForm = document.getElementById('signup-form');

    // Handle Login
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();

            // For demo: set login status in localStorage
            localStorage.setItem('isLoggedIn', 'true');
            localStorage.setItem('userEmail', document.getElementById('email').value);

            alert('Login Successful! Redirecting to Dashboard...');
            window.location.href = 'index.html';
        });
    }

    // Handle Signup
    if (signupForm) {
        signupForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const firstname = document.getElementById('firstname').value;
            const business = document.getElementById('business-name').value;

            // For demo: set login status
            localStorage.setItem('isLoggedIn', 'true');
            localStorage.setItem('userName', firstname);
            localStorage.setItem('userBusiness', business);

            alert('Registration Successful! Welcome to LGU 3 Portal.');
            window.location.href = 'index.html';
        });
    }

    // Handle Forgot Password
    const forgotForm = document.getElementById('forgot-password-form');
    if (forgotForm) {
        forgotForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            alert(`Password reset link has been sent to: ${email}\n\nPlease check your inbox.`);
            window.location.href = 'login.html';
        });
    }

    // Page Transition Animation
    const authLinks = document.querySelectorAll('.auth-footer a, .back-link, .forgot-password');
    const container = document.querySelector('.auth-container');

    authLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            // Only animate if it's an internal auth link
            const href = link.getAttribute('href');
            if (href && href !== '#' && href !== 'landing.html') {
                e.preventDefault();
                container.classList.add('fade-out');
                setTimeout(() => {
                    window.location.href = href;
                }, 400);
            }
        });
    });
});
