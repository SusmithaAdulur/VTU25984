/* ==========================================
   Authentication Validation JavaScript
   Handles login and register form validation
   ========================================== */

document.addEventListener('DOMContentLoaded', function() {
    // Determine which form we're on and initialize
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    if (loginForm) {
        initializeLoginForm();
    }

    if (registerForm) {
        initializeRegisterForm();
    }
});

/* ==========================================
   LOGIN FORM INITIALIZATION
   ========================================== */
function initializeLoginForm() {
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const rememberMeCheckbox = document.getElementById('rememberMe');

    // Real-time validation
    emailInput.addEventListener('blur', function() {
        validateEmail(this);
    });

    passwordInput.addEventListener('blur', function() {
        validatePassword(this);
    });

    // Clear errors on input
    emailInput.addEventListener('input', function() {
        clearError('emailError');
    });

    passwordInput.addEventListener('input', function() {
        clearError('passwordError');
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        // Clear previous errors
        clearError('formError');
        clearError('emailError');
        clearError('passwordError');

        // Validate all fields
        let isValid = true;

        if (!validateEmail(emailInput)) {
            isValid = false;
        }

        if (!validatePassword(passwordInput, 'password')) {
            isValid = false;
        }

        // if there are validation issues, stop submission
        if (!isValid) {
            e.preventDefault();
        }
        // when valid, the form will submit normally and be handled by server-side PHP
    });

    // Load remembered email if available
    loadRememberedEmail(emailInput, rememberMeCheckbox);
}

/* ==========================================
   REGISTER FORM INITIALIZATION
   ========================================== */
function initializeRegisterForm() {
    const form = document.getElementById('registerForm');
    const fullNameInput = document.getElementById('fullName');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const agreeTermsCheckbox = document.getElementById('agreeTerms');

    // Real-time validation
    fullNameInput.addEventListener('blur', function() {
        validateFullName(this);
    });

    emailInput.addEventListener('blur', function() {
        validateEmail(this);
    });

    passwordInput.addEventListener('input', function() {
        updatePasswordStrength(this);
        clearError('passwordError');
        // Check match when typing in password
        if (confirmPasswordInput.value) {
            validatePasswordMatch(passwordInput, confirmPasswordInput);
        }
    });

    confirmPasswordInput.addEventListener('blur', function() {
        validatePasswordMatch(passwordInput, this);
    });

    confirmPasswordInput.addEventListener('input', function() {
        if (this.value) {
            clearError('confirmPasswordError');
        }
    });

    // Clear errors on input
    fullNameInput.addEventListener('input', function() {
        clearError('fullNameError');
    });

    emailInput.addEventListener('input', function() {
        clearError('emailError');
    });

    agreeTermsCheckbox.addEventListener('change', function() {
        if (this.checked) {
            clearError('termsError');
        }
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        clearError('formError');
        clearError('fullNameError');
        clearError('emailError');
        clearError('passwordError');
        clearError('confirmPasswordError');
        clearError('termsError');

        let isValid = true;

        // Validate all fields
        if (!validateFullName(fullNameInput)) {
            isValid = false;
        }

        if (!validateEmail(emailInput)) {
            isValid = false;
        }

        if (!validatePassword(passwordInput, 'password', 8)) {
            isValid = false;
        }

        if (!validatePasswordMatch(passwordInput, confirmPasswordInput)) {
            isValid = false;
        }

        if (!agreeTermsCheckbox.checked) {
            showError('termsError', 'You must agree to the Terms and Conditions');
            isValid = false;
        }

        if (isValid) {
            // Show success message
            showSuccessMessage('successMessage', 'Registration successful! Redirecting to login...');

            // Log form data (in real app, submit to server)
            console.log('Register Form Data:', {
                fullName: fullNameInput.value,
                email: emailInput.value,
                password: passwordInput.value,
                agreeTerms: agreeTermsCheckbox.checked
            });

            // Reset form
            setTimeout(() => {
                form.reset();
                resetPasswordStrength();
                // In real app, redirect to login
                // window.location.href = '/login.html';
            }, 1500);
        }
    });
}

/* ==========================================
   VALIDATION FUNCTIONS
   ========================================== */

// Email validation
function validateEmail(emailInput) {
    const email = emailInput.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!email) {
        showError('emailError', 'Email is required');
        return false;
    }

    if (!emailRegex.test(email)) {
        showError('emailError', 'Please enter a valid email address');
        return false;
    }

    return true;
}

// Full name validation
function validateFullName(fullNameInput) {
    const fullName = fullNameInput.value.trim();

    if (!fullName) {
        showError('fullNameError', 'Full name is required');
        return false;
    }

    if (fullName.length < 3) {
        showError('fullNameError', 'Full name must be at least 3 characters');
        return false;
    }

    if (fullName.length > 50) {
        showError('fullNameError', 'Full name must not exceed 50 characters');
        return false;
    }

    if (!/^[a-zA-Z\s'-]+$/.test(fullName)) {
        showError('fullNameError', 'Full name can only contain letters, spaces, hyphens, and apostrophes');
        return false;
    }

    return true;
}

// Password validation
function validatePassword(passwordInput, fieldType = 'password', minLength = 6) {
    const password = passwordInput.value;

    if (!password) {
        showError(fieldType + 'Error', 'Password is required');
        return false;
    }

    if (password.length < minLength) {
        showError(fieldType + 'Error', `Password must be at least ${minLength} characters`);
        return false;
    }

    // Optional: Add password strength requirements
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumbers = /\d/.test(password);
    const hasSpecialChar = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);

    if (minLength >= 8) {
        // For registration (stronger requirements)
        if (!hasUpperCase || !hasLowerCase || !hasNumbers) {
            showError(fieldType + 'Error', 'Password must contain uppercase, lowercase, and numbers');
            return false;
        }
    }

    return true;
}

// Password match validation
function validatePasswordMatch(passwordInput, confirmPasswordInput) {
    const password = passwordInput.value;
    const confirmPassword = confirmPasswordInput.value;

    if (!confirmPassword) {
        showError('confirmPasswordError', 'Please confirm your password');
        return false;
    }

    if (password !== confirmPassword) {
        showError('confirmPasswordError', 'Passwords do not match');
        return false;
    }

    return true;
}

// Update password strength meter
function updatePasswordStrength(passwordInput) {
    const password = passwordInput.value;
    const strengthMeter = document.querySelector('.strength-meter::after');
    const strengthText = document.getElementById('strengthText');

    let strength = 0;
    let strengthLevel = 'Weak';
    let strengthColor = '#ef4444';

    if (!password) {
        strengthMeter.style.width = '0%';
        strengthText.textContent = '';
        return;
    }

    // Calculate strength
    if (password.length >= 6) strength += 25;
    if (password.length >= 10) strength += 25;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 25;
    if (/\d/.test(password)) strength += 12.5;
    if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) strength += 12.5;

    // Determine level
    if (strength < 25) {
        strengthLevel = 'Very Weak';
        strengthColor = '#ef4444';
    } else if (strength < 50) {
        strengthLevel = 'Weak';
        strengthColor = '#f59e0b';
    } else if (strength < 75) {
        strengthLevel = 'Good';
        strengthColor = '#3b82f6';
    } else {
        strengthLevel = 'Strong';
        strengthColor = '#10b981';
    }

    strengthMeter.style.width = strength + '%';
    strengthMeter.style.backgroundColor = strengthColor;
    strengthText.textContent = strengthLevel;
    strengthText.style.color = strengthColor;
}

// Reset password strength meter
function resetPasswordStrength() {
    const strengthMeter = document.querySelector('.strength-meter::after');
    const strengthText = document.getElementById('strengthText');
    if (strengthMeter) {
        strengthMeter.style.width = '0%';
    }
    if (strengthText) {
        strengthText.textContent = '';
    }
}

/* ==========================================
   UTILITY FUNCTIONS
   ========================================== */

// Show error message
function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.add('show');
        errorElement.style.display = 'block';
    }
}

// Clear error message
function clearError(elementId) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
        errorElement.textContent = '';
        errorElement.classList.remove('show');
        errorElement.style.display = 'none';
    }
}

// Show success message
function showSuccessMessage(elementId, message) {
    const successElement = document.getElementById(elementId);
    if (successElement) {
        successElement.textContent = message;
        successElement.classList.add('show');
    }
}

// Load remembered email
function loadRememberedEmail(emailInput, rememberMeCheckbox) {
    const rememberedEmail = localStorage.getItem('rememberedEmail');
    if (rememberedEmail) {
        emailInput.value = rememberedEmail;
        rememberMeCheckbox.checked = true;
    }

    rememberMeCheckbox.addEventListener('change', function() {
        if (this.checked) {
            localStorage.setItem('rememberedEmail', emailInput.value || '');
        } else {
            localStorage.removeItem('rememberedEmail');
        }
    });

    emailInput.addEventListener('change', function() {
        if (rememberMeCheckbox.checked) {
            localStorage.setItem('rememberedEmail', this.value);
        }
    });
}

/* ==========================================
   ACCESSIBILITY IMPROVEMENTS
   ========================================== */

// Add ARIA labels and roles
document.addEventListener('DOMContentLoaded', function() {
    // Keyboard navigation for form elements
    const formInputs = document.querySelectorAll('input, button');
    
    formInputs.forEach((input, index) => {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && this.type !== 'textarea') {
                // Move to next input or submit form
                if (index < formInputs.length - 1 && this.type !== 'button') {
                    formInputs[index + 1].focus();
                }
            }
        });
    });
});
