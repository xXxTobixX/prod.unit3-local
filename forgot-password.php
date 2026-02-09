<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - LGU 3 Local Product & Export Development</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/auth.css?v=2.0">
</head>

<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <a href="login.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Login</a>
                <h2>Forgot Password?</h2>
                <p>No worries, we'll send you reset instructions. Please enter your registered email address.</p>
            </div>

            <form id="forgot-password-form" class="auth-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" placeholder="enter your email" required>
                    </div>
                </div>

                <button type="submit" class="btn-auth">Reset Password</button>
            </form>

            <div class="auth-footer">
                <p>Remember your password? <a href="login.php">Sign In</a></p>
            </div>
        </div>

        <div class="auth-visual forgot-visual">
            <div class="visual-content">
                <h2>Security First</h2>
                <p>We use industry-standard encryption to ensure your data and access remain protected at all times.</p>
            </div>
        </div>
    </div>
    <script src="js/auth.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const forgotForm = document.getElementById('forgot-password-form');
            const authBox = document.querySelector('.auth-box');
            let userEmail = '';

            // Create Reset Section (Hidden by default)
            const resetSection = document.createElement('form');
            resetSection.id = 'reset-password-form';
            resetSection.className = 'auth-form';
            resetSection.style.display = 'none';
            resetSection.innerHTML = `
                <div class="form-group">
                    <label for="otp">Verification Code</label>
                    <div class="input-icon">
                        <i class="fas fa-key"></i>
                        <input type="text" id="otp" placeholder="Enter 6-digit code" maxlength="6" required style="letter-spacing: 2px; font-weight: bold; text-align: center;">
                    </div>
                    <small style="display:block; margin-top:5px; color:#64748b;">We sent a code to your email.</small>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="new_password" placeholder="Enter new password" minlength="8" required>
                    </div>
                </div>
                <button type="submit" class="btn-auth">Set New Password</button>
            `;
            
            // Insert reset form after the forgot form
            forgotForm.parentNode.insertBefore(resetSection, forgotForm.nextSibling);

            // Additional notification helper since auth.js showNotification might not be global
            function showToast(title, message, type = 'success') {
                // Try using the one from auth.js if available, else create simple alert
                if (typeof showNotification === 'function') {
                    showNotification(title, message, type);
                } else {
                    alert(`${title}: ${message}`);
                }
            }

            // Handle Email Submission
            forgotForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const emailInput = document.getElementById('email');
                userEmail = emailInput.value.trim();
                const btn = forgotForm.querySelector('button');
                
                if(!userEmail) return;

                const originalText = btn.innerText;
                btn.disabled = true;
                btn.innerText = 'Sending Code...';

                const formData = new FormData();
                formData.append('email', userEmail);

                fetch('ajax/auth.php?action=forgot-password', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Code Sent', data.message, 'success');
                        forgotForm.style.display = 'none';
                        resetSection.style.display = 'block';
                        document.querySelector('.auth-header h2').innerText = 'Verify & Reset';
                        document.querySelector('.auth-header p').innerText = 'Enter the code sent to ' + userEmail;
                    } else {
                        showToast('Error', data.message, 'error');
                        btn.disabled = false;
                        btn.innerText = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('System Error', 'Could not connect to server.', 'error');
                    btn.disabled = false;
                    btn.innerText = originalText;
                });
            });

            // Handle Reset Submission
            resetSection.addEventListener('submit', function(e) {
                e.preventDefault();
                const otp = document.getElementById('otp').value.trim();
                const password = document.getElementById('new_password').value;
                const btn = resetSection.querySelector('button');

                if(!otp || !password) return;

                const originalText = btn.innerText;
                btn.disabled = true;
                btn.innerText = 'Updating Password...';

                const formData = new FormData();
                formData.append('email', userEmail);
                formData.append('otp', otp);
                formData.append('password', password);

                fetch('ajax/auth.php?action=reset-password', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Success', data.message, 'success');
                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 2000);
                    } else {
                        showToast('Error', data.message, 'error');
                        btn.disabled = false;
                        btn.innerText = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('System Error', 'Could not connect to server.', 'error');
                    btn.disabled = false;
                    btn.innerText = originalText;
                });
            });
        });
    </script>
</body>

</html>
