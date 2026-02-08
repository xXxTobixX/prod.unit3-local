document.addEventListener('DOMContentLoaded', () => {
    const navbar = document.querySelector('.navbar');
    const mobileToggle = document.querySelector('.mobile-toggle');
    const navLinks = document.querySelector('.nav-links');

    // Sticky Navbar on Scroll
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.style.boxShadow = '0 4px 20px rgba(0, 32, 91, 0.1)';
            navbar.style.height = '80px';
        } else {
            navbar.style.boxShadow = 'none';
            navbar.style.height = '90px';
        }
    });

    // Smooth Scroll for Anchor Links with Offset for Sticky Header
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                const navHeight = document.querySelector('.navbar').offsetHeight;
                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - navHeight;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Service Card Button Functionality
    const serviceButtons = document.querySelectorAll('.service-card a');

    const isLoggedIn = () => {
        return localStorage.getItem('isLoggedIn') === 'true';
    };

    serviceButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();

            const serviceName = btn.parentElement.querySelector('h3').innerText;

            if (!isLoggedIn()) {
                // Redirect to Signup if not logged in
                window.location.href = 'signup.php';
            } else {
                // If logged in, redirect to specific section in dashboard
                const serviceSlug = serviceName.toLowerCase().replace(/ & /g, '-').replace(/ /g, '-');
                window.location.href = `index.php#service-${serviceSlug}`;
            }
        });
    });

    // Reveal elements on scroll (simple observer)
    const observerOptions = {
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Modal Logic
    const privacyModal = document.getElementById('privacy-modal');
    const termsModal = document.getElementById('terms-modal');
    const privacyLink = document.getElementById('privacy-link');
    const termsLink = document.getElementById('terms-link');
    const closeBtns = document.querySelectorAll('.close-modal');

    if (privacyLink) {
        privacyLink.onclick = () => privacyModal.style.display = "block";
    }

    if (termsLink) {
        termsLink.onclick = () => termsModal.style.display = "block";
    }

    closeBtns.forEach(btn => {
        btn.onclick = () => {
            const modalId = btn.getAttribute('data-modal');
            document.getElementById(modalId).style.display = "none";
        }
    });

    window.onclick = (event) => {
        if (event.target == privacyModal) privacyModal.style.display = "none";
        if (event.target == termsModal) termsModal.style.display = "none";
    }
});
