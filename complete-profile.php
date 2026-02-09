<?php require_once 'includes/init.php'; 
if (!isLoggedIn()) {
    redirect('login.php');
}

// Redirect if profile is already completed
if (isset($_SESSION['profile_completed']) && $_SESSION['profile_completed'] === true) {
    if (in_array($_SESSION['user_role'], ['admin', 'staff', 'superadmin', 'manager'])) {
        redirect('dashboard/administrator/index.php');
    } else {
        redirect('dashboard/users/index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile - LGU 3 Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/auth.css?v=2.5">
    <style>
        body {
            background-color: var(--bg-dark);
            background-image: radial-gradient(circle at top right, rgba(96, 165, 250, 0.05), transparent),
                              radial-gradient(circle at bottom left, rgba(96, 165, 250, 0.05), transparent);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            overflow-y: auto !important;
        }

        .profile-card {
            background: var(--bg-dark-alt);
            width: 100%;
            max-width: 650px;
            border-radius: 24px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-auth);
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .profile-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), #3b82f6);
        }

        .step-container {
            display: none;
            animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .step-container.active {
            display: block;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .step-indicator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--border-color);
            z-index: 1;
            transform: translateY(-50%);
        }

        .step {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--bg-dark);
            border: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            z-index: 2;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            color: var(--text-muted);
            position: relative;
        }

        .step.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 0 20px rgba(96, 165, 250, 0.4);
            transform: scale(1.1);
        }

        .step.completed {
            background: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }

        .step.completed::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 10px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: 10px;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
        }

        .input-style, 
        .input-icon input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-light);
            font-size: 15px;
            transition: all 0.3s ease;
        }

        input[name="business_name"],
        input[name="product_name"] {
            text-transform: capitalize;
        }

        .capacity-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .unit-suffix {
            position: absolute;
            right: 16px;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 14px;
            pointer-events: none;
        }

        #production-capacity {
            padding-right: 45px;
        }

        select.input-style {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394A3B8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 18px;
        }

        .input-style:focus, 
        .input-icon input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.1);
            outline: none;
        }

        textarea.input-style {
            padding-left: 16px;
            min-height: 120px;
            line-height: 1.6;
        }

        .form-actions {
            display: flex;
            gap: 16px;
            margin-top: 40px;
        }

        .btn-auth {
            flex: 1;
            padding: 16px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-next, .btn-submit {
            background: var(--primary-color);
            color: white;
        }

        .btn-next:hover, .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(96, 165, 250, 0.3);
        }

        .btn-prev {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border-color);
            max-width: 150px;
        }

        .btn-prev:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-light);
        }

        .checkbox-group {
            background: rgba(255, 255, 255, 0.02);
            padding: 20px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .checkbox-group:hover {
            border-color: rgba(96, 165, 250, 0.3);
            background: rgba(255, 255, 255, 0.04);
        }

        .progress-bar-stepper {
            height: 6px;
            background: var(--border-color);
            border-radius: 3px;
            margin-bottom: 40px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), #3b82f6);
            width: 20%;
            transition: width 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>
</head>
<body>
    <div class="profile-card">
        <div class="auth-header">
            <h2>Finalize Your Profile</h2>
            <p>Welcome, <span id="user-display-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>! Let's get your business ready for the dashboard.</p>
        </div>

        <div class="progress-bar-stepper">
            <div class="progress-fill" id="progress-fill"></div>
        </div>

        <div class="step-indicator">
            <div class="step active" data-step="1">1</div>
            <div class="step" data-step="2">2</div>
            <div class="step" data-step="3">3</div>
            <div class="step" data-step="4">4</div>
            <div class="step" data-step="5">5</div>
        </div>

        <form id="profile-completion-form">
            <!-- Step 1: Business Profile -->
            <div class="step-container active" id="step-1">
                <div class="form-group">
                    <label>Business Name</label>
                    <div class="input-icon">
                        <i class="fas fa-building"></i>
                        <input type="text" name="business_name" value="<?php echo htmlspecialchars(html_entity_decode($_SESSION['business_name'] ?? $_SESSION['user_name'] . ' Enterprises', ENT_QUOTES, 'UTF-8')); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Business Type</label>
                    <select name="business_type" class="input-style" required>
                        <option value="">Select Business Type</option>
                        <option value="Sole Proprietorship">Sole Proprietorship</option>
                        <option value="Cooperative">Cooperative</option>
                        <option value="Association">Association</option>
                        <option value="Corporation">Corporation</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Sector / Industry</label>
                    <select name="sector" id="sector-select" class="input-style" required>
                        <option value="">Select Sector</option>
                        <option value="Food & Beverages">Food & Beverages</option>
                        <option value="Agriculture">Agriculture</option>
                        <option value="Handicrafts">Handicrafts</option>
                        <option value="Manufacturing">Manufacturing</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
                <div class="form-group" id="sector-others-group" style="display: none; animation: slideUp 0.3s ease-out;">
                    <label>Please Specify Sector</label>
                    <div class="input-icon">
                        <i class="fas fa-edit"></i>
                        <input type="text" name="sector_other" id="sector-other-input" placeholder="e.g. Information Technology">
                    </div>
                </div>
                <div class="form-group">
                    <label>Business Address</label>
                    <div class="input-icon">
                        <i class="fas fa-map-marker-alt" style="top: 18px; transform: none;"></i>
                        <textarea name="business_address" class="input-style" placeholder="Purok, Street, Barangay, City/Municipality" required style="min-height: 100px; padding: 16px 16px 16px 48px; line-height: 1.5;"></textarea>
                    </div>
                </div>
            </div>

            <!-- Step 2: Registration Details -->
            <div class="step-container" id="step-2">
                <div class="form-group">
                    <label>DTI / SEC / CDA Number (Optional)</label>
                    <div class="input-icon">
                        <i class="fas fa-id-card"></i>
                        <input type="text" name="registration_number" placeholder="Enter registration number">
                    </div>
                </div>
                <div class="form-group">
                    <label>Year Started</label>
                    <div class="input-icon">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="number" name="year_started" min="1900" max="2026" value="2024" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Number of Workers</label>
                    <div class="input-icon">
                        <i class="fas fa-users"></i>
                        <input type="number" name="number_of_workers" min="1" value="1" required>
                    </div>
                </div>
            </div>

            <!-- Step 3: Product Info -->
            <div class="step-container" id="step-3">
                <div class="auth-header" style="text-align: left; margin-bottom: 25px;">
                    <h3 style="font-size: 18px; color: var(--primary-color);">Primary Product Details</h3>
                    <p style="font-size: 13px;">Tell us about your main product or service.</p>
                </div>
                <div class="form-group">
                    <label>Product Name</label>
                    <div class="input-icon">
                        <i class="fas fa-box"></i>
                        <input type="text" name="product_name" placeholder="e.g. Arabica Coffee Beans" autocapitalize="words" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Product Category</label>
                    <select name="product_category" id="product-category-select" class="input-style" required>
                        <option value="">Select Category</option>
                        <option value="Food & Beverages">Food & Beverages</option>
                        <option value="Agricultural Produce">Agricultural Produce</option>
                        <option value="Home & Decor">Home & Decor</option>
                        <option value="Fashion & Textiles">Fashion & Textiles</option>
                        <option value="Others">Others</option>
                    </select>
                </div>
                <div class="form-group" id="product-category-others-group" style="display: none; animation: slideUp 0.3s ease-out;">
                    <label>Please Specify Category</label>
                    <div class="input-icon">
                        <i class="fas fa-edit"></i>
                        <input type="text" name="product_category_other" id="product-category-other-input" placeholder="e.g. Technology & Electronics">
                    </div>
                </div>
                <div class="form-group">
                    <label>Product Description</label>
                    <textarea name="product_description" class="input-style" placeholder="Describe what makes your product unique..." required></textarea>
                </div>
                <div class="form-group">
                    <label>Monthly Production Capacity (per month)</label>
                    <div class="input-icon capacity-input-wrapper">
                        <i class="fas fa-chart-line"></i>
                        <input type="number" name="production_capacity" id="production-capacity" placeholder="e.g. 500" min="1" required>
                        <span class="unit-suffix">kg</span>
                    </div>
                </div>
            </div>

            <!-- Step 4: Compliance -->
            <div class="step-container" id="step-4">
                <div class="auth-header" style="text-align: left; margin-bottom: 25px;">
                    <h3 style="font-size: 18px; color: var(--primary-color);">Compliance Declaration</h3>
                    <p style="font-size: 13px;">Please acknowledge the following requirements.</p>
                </div>
                <div class="form-group">
                    <label>Type of Main Product</label>
                    <div class="radio-options" style="display: flex; gap: 20px; margin-top: 10px;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: var(--text-muted); font-weight: 500;">
                            <input type="radio" name="compliance_type" value="Food" required style="accent-color: var(--primary-color);"> Food Item
                        </label>
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: var(--text-muted); font-weight: 500;">
                            <input type="radio" name="compliance_type" value="Non-Food" style="accent-color: var(--primary-color);"> Non-Food
                        </label>
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; color: var(--text-muted); font-weight: 500;">
                            <input type="radio" name="compliance_type" value="Agricultural" style="accent-color: var(--primary-color);"> Agricultural
                        </label>
                    </div>
                </div>
                
                <div class="checkbox-group" style="margin-top: 25px;">
                    <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer;">
                        <input type="checkbox" name="permit_ack" required style="margin-top: 4px; accent-color: var(--primary-color);">
                        <span style="font-size: 13px; color: var(--text-muted); line-height: 1.5;"> I acknowledge that my business requires specific permits (e.g. Mayor's Permit, DTI, FDA) to participate in export programs.</span>
                    </label>
                </div>

                <div class="checkbox-group">
                    <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer;">
                        <input type="checkbox" name="lgu_comply" required style="margin-top: 4px; accent-color: var(--primary-color);">
                        <span style="font-size: 13px; color: var(--text-muted); line-height: 1.5;"> I will comply with all LGU 3 requirements and standard operating procedures for MSME development.</span>
                    </label>
                </div>
            </div>

            <!-- Step 5: Privacy & Consent -->
            <div class="step-container" id="step-5">
                <div class="auth-header" style="text-align: left; margin-bottom: 25px;">
                    <h3 style="font-size: 18px; color: var(--primary-color);">Consent & Data Privacy</h3>
                    <p style="font-size: 13px;">Finalize your registration with your consent.</p>
                </div>
                
                <div class="checkbox-group" style="background: rgba(16, 185, 129, 0.05); border-color: rgba(16, 185, 129, 0.2);">
                    <label style="display: flex; align-items: flex-start; gap: 14px; cursor: pointer;">
                        <input type="checkbox" name="privacy_consent" id="privacy-consent" required style="margin-top: 4px; accent-color: var(--success-color);">
                        <div style="font-size: 14px; color: var(--text-light);">
                            <strong style="color: var(--success-color);">Data Privacy Act Consent</strong>
                            <p style="font-size: 12px; color: var(--text-muted); margin-top: 6px; line-height: 1.6;">I hereby authorize LGU 3 to collect and process the data provided in this form for MSME registry, program evaluation, and export development purposes in accordance with the Data Privacy Act of 2012.</p>
                        </div>
                    </label>
                </div>

                <div class="checkbox-group">
                    <label style="display: flex; align-items: flex-start; gap: 14px; cursor: pointer;">
                        <input type="checkbox" name="terms_consent" required style="margin-top: 4px; accent-color: var(--primary-color);">
                        <div style="font-size: 14px; color: var(--text-light);">
                            <strong>Terms & Conditions Acceptance</strong>
                            <p style="font-size: 12px; color: var(--text-muted); margin-top: 6px; line-height: 1.6;">I agree to the portal's terms of service and certify that all information provided is true and correct to the best of my knowledge.</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-auth btn-prev" id="btn-prev" style="display: none;">
                    <i class="fas fa-arrow-left"></i> Previous
                </button>
                <div style="flex: 1;"></div>
                <button type="button" class="btn-auth btn-next" id="btn-next">
                    Next Step <i class="fas fa-arrow-right"></i>
                </button>
                <button type="submit" class="btn-auth btn-submit" id="btn-submit" style="display: none;">
                    <i class="fas fa-check-circle"></i> Complete Setup
                </button>
            </div>
        </form>
    </div>

    <script src="js/auth.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('profile-completion-form');
            const steps = document.querySelectorAll('.step-container');
            const indicators = document.querySelectorAll('.step');
            const btnNext = document.getElementById('btn-next');
            const btnPrev = document.getElementById('btn-prev');
            const btnSubmit = document.getElementById('btn-submit');
            const progressFill = document.getElementById('progress-fill');
            
            let currentStep = 1;
            const totalSteps = steps.length;

            // Handle Auto-capitalization for specific fields
            const autoCapFields = ['business_name', 'product_name'];
            autoCapFields.forEach(fieldName => {
                const input = document.querySelector(`input[name="${fieldName}"]`);
                if (input) {
                    input.addEventListener('input', (e) => {
                        const words = e.target.value.split(' ');
                        const capitalizedWords = words.map(word => {
                            if (word.length === 0) return '';
                            return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
                        });
                        e.target.value = capitalizedWords.join(' ');
                    });
                }
            });

            // Handle Sector "Others" Selection
            const sectorSelect = document.getElementById('sector-select');
            const sectorOthersGroup = document.getElementById('sector-others-group');
            const sectorOtherInput = document.getElementById('sector-other-input');

            if (sectorSelect) {
                sectorSelect.addEventListener('change', () => {
                    if (sectorSelect.value === 'Others') {
                        sectorOthersGroup.style.display = 'block';
                        sectorOtherInput.setAttribute('required', 'required');
                        sectorOtherInput.focus();
                    } else {
                        sectorOthersGroup.style.display = 'none';
                        sectorOtherInput.removeAttribute('required');
                        sectorOtherInput.value = '';
                    }
                });
            }

            // Handle Product Category "Others" Selection
            const prodCatSelect = document.getElementById('product-category-select');
            const prodCatOthersGroup = document.getElementById('product-category-others-group');
            const prodCatOtherInput = document.getElementById('product-category-other-input');

            if (prodCatSelect) {
                prodCatSelect.addEventListener('change', () => {
                    if (prodCatSelect.value === 'Others') {
                        prodCatOthersGroup.style.display = 'block';
                        prodCatOtherInput.setAttribute('required', 'required');
                        prodCatOtherInput.focus();
                    } else {
                        prodCatOthersGroup.style.display = 'none';
                        prodCatOtherInput.removeAttribute('required');
                        prodCatOtherInput.value = '';
                    }
                });
            }

            const updateStep = () => {
                steps.forEach((s, idx) => {
                    s.classList.toggle('active', idx === currentStep - 1);
                });

                indicators.forEach((ind, idx) => {
                    ind.classList.toggle('active', idx === currentStep - 1);
                    ind.classList.toggle('completed', idx < currentStep - 1);
                });

                btnPrev.style.display = currentStep === 1 ? 'none' : 'flex';
                btnNext.style.display = currentStep === totalSteps ? 'none' : 'flex';
                btnSubmit.style.display = currentStep === totalSteps ? 'flex' : 'none';

                const progress = (currentStep / totalSteps) * 100;
                progressFill.style.width = `${progress}%`;
            };

            // Force initial display state
            btnNext.style.display = 'flex';
            btnPrev.style.display = 'none';
            btnSubmit.style.display = 'none';

            // Initialize button state
            setTimeout(updateStep, 100);

            btnNext.addEventListener('click', () => {
                const currentContainer = document.getElementById(`step-${currentStep}`);
                const inputs = currentContainer.querySelectorAll('input, select, textarea');
                let valid = true;

                inputs.forEach(input => {
                    if (input.hasAttribute('required') && !input.value) {
                        valid = false;
                        input.classList.add('error');
                        setTimeout(() => input.classList.remove('error'), 1000);
                    }
                });

                if (valid && currentStep < totalSteps) {
                    currentStep++;
                    updateStep();
                } else if (!valid) {
                    showNotification('Incomplete', 'Please fill in all required fields.', 'error');
                }
            });

            btnPrev.addEventListener('click', () => {
                if (currentStep > 1) {
                    currentStep--;
                    updateStep();
                }
            });

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                
                const formData = new FormData(form);
                formData.append('action', 'complete-profile'); // Ensure action is in POST data

                // Debug FormData
                for (var pair of formData.entries()) {
                    console.log(pair[0]+ ', ' + pair[1]); 
                }

                const btnText = btnSubmit.innerHTML;
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving Profile...';

                fetch('ajax/auth.php', { // Removed query param
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text()) // Get as text first
                .then(text => {
                    console.log('Raw Server Response:', text);
                    try {
                        // Sanitize response: find the JSON object
                        const jsonStart = text.indexOf('{');
                        const jsonEnd = text.lastIndexOf('}');
                        
                        if (jsonStart === -1 || jsonEnd === -1) {
                            throw new Error('No JSON object found in response');
                        }

                        const cleanJson = text.substring(jsonStart, jsonEnd + 1);
                        const data = JSON.parse(cleanJson);
                        
                        console.log('Profile Completion Response:', data); // Debug log

                        if (data.success) {
                            showNotification('Success!', 'Your profile is now complete. Redirecting...', 'success');
                            
                            // Force immediate redirection
                            btnSubmit.innerHTML = 'Redirecting...';
                            window.location.href = 'dashboard/users/index.php'; 
                        } else {
                            showNotification('Error', data.message, 'error');
                            btnSubmit.disabled = false;
                            btnSubmit.innerHTML = btnText;
                        }
                    } catch (e) {
                        console.error('JSON Parse Error:', e);
                        console.log('Server returned:', text); 
                        alert("Critical Error: Unable to read server response.\n\n" + text.substring(0, 100)); // Show user the content
                        showNotification('System Error', 'Server returned an invalid response format.', 'error');
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = btnText;
                    }
                })
                .catch(error => {
                    console.error('Fetch Error:', error);
                    showNotification('Network Error', 'Could not communicate with server.', 'error');
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = btnText;
                });
            });
        });
    </script>
</body>
</html>
