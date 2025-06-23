// ---------------------------------------------
// Custom Scripts for Beautiec Cosmetic Website
// Updated: Added Account Management Scripts
// ---------------------------------------------

// Auto dropdown on hover (account + cart)
document.addEventListener("DOMContentLoaded", function () {
    const dropdowns = document.querySelectorAll('.position-relative');

    dropdowns.forEach(function (dropdown) {
        dropdown.addEventListener('mouseenter', function () {
            const child = this.querySelector('.account-dropdown, .cart-dropdown');
            if (child) {
                child.style.display = 'flex';
            }
        });

        dropdown.addEventListener('mouseleave', function () {
            const child = this.querySelector('.account-dropdown, .cart-dropdown');
            if (child) {
                child.style.display = 'none';
            }
        });
    });
});

// Scroll to top button (optional nếu bạn thích)
const scrollTopButton = document.createElement('button');
scrollTopButton.innerHTML = '⬆️';
scrollTopButton.style.position = 'fixed';
scrollTopButton.style.bottom = '20px';
scrollTopButton.style.right = '20px';
scrollTopButton.style.display = 'none';
scrollTopButton.style.background = '#f8b6c1';
scrollTopButton.style.border = 'none';
scrollTopButton.style.padding = '10px';
scrollTopButton.style.borderRadius = '50%';
scrollTopButton.style.zIndex = '999';
document.body.appendChild(scrollTopButton);

window.addEventListener('scroll', function () {
    if (window.scrollY > 300) {
        scrollTopButton.style.display = 'block';
    } else {
        scrollTopButton.style.display = 'none';
    }
});

scrollTopButton.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

document.addEventListener('DOMContentLoaded', function () {
    const items = document.querySelectorAll('.dropdown-submenu .dropdown-item');

    items.forEach(function (item) {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            items.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });
});

// ---------------------------------------------
// Password Toggle Functionality (Show/Hide Password)
// ---------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
    // Tìm tất cả các nút toggle password
    const passwordToggles = document.querySelectorAll('.password-toggle');

    passwordToggles.forEach(function (toggle) {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();

            // Lấy target input từ data-target attribute
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (passwordInput && icon) {
                // Toggle giữa password và text type
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        });
    });
});

// ---------------------------------------------
// Form Validation Enhancement (Optional)
// ---------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
    // Tìm form đăng ký để thêm validation cho confirm password
    const registerForm = document.querySelector('form[action*="register"]');

    if (registerForm) {
        const password = registerForm.querySelector('input[name="password"]');
        const confirmPassword = registerForm.querySelector('input[name="password_confirmation"]');

        if (password && confirmPassword) {
            // Kiểm tra mật khẩu khớp khi người dùng nhập
            confirmPassword.addEventListener('input', function () {
                if (this.value !== password.value) {
                    this.setCustomValidity('Mật khẩu xác nhận không khớp');
                    this.classList.add('is-invalid');
                } else {
                    this.setCustomValidity('');
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });

            // Kiểm tra lại khi mật khẩu chính thay đổi
            password.addEventListener('input', function () {
                if (confirmPassword.value && confirmPassword.value !== this.value) {
                    confirmPassword.setCustomValidity('Mật khẩu xác nhận không khớp');
                    confirmPassword.classList.add('is-invalid');
                } else if (confirmPassword.value) {
                    confirmPassword.setCustomValidity('');
                    confirmPassword.classList.remove('is-invalid');
                    confirmPassword.classList.add('is-valid');
                }
            });
        }
    }
});

// ---------------------------------------------
// Loading State for Forms (Optional Enhancement)
// ---------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('form');

    forms.forEach(function (form) {
        form.addEventListener('submit', function () {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';

                // Restore button sau 5 giây nếu form không submit thành công
                setTimeout(function () {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 5000);
            }
        });
    });
});

// ---------------------------------------------
// ACCOUNT MANAGEMENT SCRIPTS
// Scripts từ account.blade.php, account-edit.blade.php, change-password.blade.php
// ---------------------------------------------

document.addEventListener('DOMContentLoaded', function () {
    // Initialize account management features
    initAvatarUpload();
    initAccountFormValidation();
    initPasswordStrengthChecker();
    initPhoneFormatting();
    initAutoHideAlerts();
});

// ---------------------------------------------
// Avatar Upload với Preview (account-edit.blade.php)
// ---------------------------------------------
function initAvatarUpload() {
    const avatarInput = document.getElementById('avatar-input');
    const avatarPreview = document.getElementById('avatar-preview');

    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Vui lòng chọn file ảnh hợp lệ (JPG, PNG, GIF, WebP)');
                    this.value = '';
                    return;
                }

                // Validate file size (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Kích thước file không được vượt quá 5MB');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                    // Add loading effect
                    avatarPreview.style.opacity = '0.5';
                    setTimeout(() => {
                        avatarPreview.style.opacity = '1';
                    }, 300);
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

// ---------------------------------------------
// Account Form Validation (account-edit.blade.php)
// ---------------------------------------------
function initAccountFormValidation() {
    const accountForm = document.querySelector('form[action*="account"]');

    if (accountForm) {
        const submitBtn = accountForm.querySelector('#submit-btn');
        const password = accountForm.querySelector('#password');
        const passwordConfirmation = accountForm.querySelector('#password_confirmation');

        // Form submission handling
        accountForm.addEventListener('submit', function(e) {
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<div class="loading-spinner me-2"></div>Đang lưu...';
            }
        });

        // Password confirmation validation
        if (password && passwordConfirmation) {
            function validatePasswordMatch() {
                if (password.value && passwordConfirmation.value) {
                    if (password.value !== passwordConfirmation.value) {
                        passwordConfirmation.setCustomValidity('Mật khẩu xác nhận không khớp');
                        passwordConfirmation.classList.add('is-invalid');
                        passwordConfirmation.classList.remove('is-valid');
                    } else {
                        passwordConfirmation.setCustomValidity('');
                        passwordConfirmation.classList.remove('is-invalid');
                        passwordConfirmation.classList.add('is-valid');
                    }
                }
            }

            password.addEventListener('input', validatePasswordMatch);
            passwordConfirmation.addEventListener('input', validatePasswordMatch);
        }
    }
}

// ---------------------------------------------
// Password Strength Checker (change-password.blade.php)
// ---------------------------------------------
function initPasswordStrengthChecker() {
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('password-strength');
    const strengthText = document.getElementById('password-strength-text');

    if (passwordInput && strengthBar && strengthText) {
        function checkPasswordStrength(password) {
            let score = 0;
            let feedback = '';

            if (password.length >= 8) score++;
            if (password.match(/[a-z]/)) score++;
            if (password.match(/[A-Z]/)) score++;
            if (password.match(/[0-9]/)) score++;
            if (password.match(/[^a-zA-Z0-9]/)) score++;

            // Remove previous classes
            strengthBar.className = 'progress-bar';

            switch(score) {
                case 0:
                case 1:
                    strengthBar.style.width = '20%';
                    strengthBar.classList.add('bg-danger');
                    feedback = 'Rất yếu';
                    break;
                case 2:
                    strengthBar.style.width = '40%';
                    strengthBar.classList.add('bg-danger');
                    feedback = 'Yếu';
                    break;
                case 3:
                    strengthBar.style.width = '60%';
                    strengthBar.classList.add('bg-warning');
                    feedback = 'Trung bình';
                    break;
                case 4:
                    strengthBar.style.width = '80%';
                    strengthBar.classList.add('bg-info');
                    feedback = 'Tốt';
                    break;
                case 5:
                    strengthBar.style.width = '100%';
                    strengthBar.classList.add('bg-success');
                    feedback = 'Rất mạnh';
                    break;
            }

            strengthText.textContent = feedback;
            return score >= 3;
        }

        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });
    }

    // Password match validation for change password form
    const changePasswordForm = document.getElementById('change-password-form');
    if (changePasswordForm) {
        const password = changePasswordForm.querySelector('#password');
        const passwordConfirmation = changePasswordForm.querySelector('#password_confirmation');
        const matchFeedback = document.getElementById('password-match-feedback');

        if (password && passwordConfirmation && matchFeedback) {
            function validatePasswordMatch() {
                if (password.value && passwordConfirmation.value) {
                    if (password.value === passwordConfirmation.value) {
                        matchFeedback.innerHTML = '<small class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Mật khẩu khớp</small>';
                        passwordConfirmation.setCustomValidity('');
                        passwordConfirmation.classList.remove('is-invalid');
                        passwordConfirmation.classList.add('is-valid');
                    } else {
                        matchFeedback.innerHTML = '<small class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>Mật khẩu không khớp</small>';
                        passwordConfirmation.setCustomValidity('Mật khẩu xác nhận không khớp');
                        passwordConfirmation.classList.add('is-invalid');
                        passwordConfirmation.classList.remove('is-valid');
                    }
                } else {
                    matchFeedback.innerHTML = '';
                    passwordConfirmation.setCustomValidity('');
                    passwordConfirmation.classList.remove('is-invalid', 'is-valid');
                }
            }

            password.addEventListener('input', validatePasswordMatch);
            passwordConfirmation.addEventListener('input', validatePasswordMatch);
        }

        // Form submission validation
        changePasswordForm.addEventListener('submit', function(e) {
            const passwordStrong = checkPasswordStrength(password.value);

            if (!passwordStrong) {
                e.preventDefault();
                alert('Vui lòng sử dụng mật khẩu mạnh hơn (ít nhất 3/5 tiêu chí)');
                return false;
            }

            if (password.value !== passwordConfirmation.value) {
                e.preventDefault();
                alert('Mật khẩu xác nhận không khớp');
                return false;
            }

            // Show loading state
            const submitBtn = this.querySelector('#submit-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<div class="loading-spinner me-2"></div>Đang xử lý...';
            }
        });
    }
}

// ---------------------------------------------
// Phone Number Formatting (account-edit.blade.php)
// ---------------------------------------------
function initPhoneFormatting() {
    const phoneInput = document.getElementById('phone');

    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                if (value.length <= 10) {
                    // Format: 0123 456 789
                    value = value.replace(/(\d{4})(\d{3})(\d{3})/, '$1 $2 $3');
                }
            }
            e.target.value = value;
        });

        // Validate Vietnamese phone number
        phoneInput.addEventListener('blur', function() {
            const phoneRegex = /^[0-9]{10,11}$/;
            const cleanPhone = this.value.replace(/\s/g, '');

            if (cleanPhone && !phoneRegex.test(cleanPhone)) {
                this.setCustomValidity('Số điện thoại không hợp lệ');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        });
    }
}

// ---------------------------------------------
// Auto Hide Alerts (từ tất cả các views)
// ---------------------------------------------
function initAutoHideAlerts() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            if (alert.classList.contains('fade')) {
                alert.classList.remove('show');
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 150);
            }
        });
    }, 5000); // Hide after 5 seconds
}

// ---------------------------------------------
// Reset Form Functions (change-password.blade.php)
// ---------------------------------------------
function resetForm() {
    if (confirm('Bạn có chắc chắn muốn đặt lại form?')) {
        const form = document.getElementById('change-password-form') || document.querySelector('form');
        if (form) {
            form.reset();

            // Reset password strength indicator
            const strengthBar = document.getElementById('password-strength');
            const strengthText = document.getElementById('password-strength-text');
            const matchFeedback = document.getElementById('password-match-feedback');

            if (strengthBar) {
                strengthBar.style.width = '0%';
                strengthBar.className = 'progress-bar';
            }
            if (strengthText) {
                strengthText.textContent = 'Độ mạnh mật khẩu';
            }
            if (matchFeedback) {
                matchFeedback.innerHTML = '';
            }

            // Reset avatar preview
            const avatarPreview = document.getElementById('avatar-preview');
            if (avatarPreview) {
                avatarPreview.src = avatarPreview.getAttribute('data-default') || '/images/default-avatar.png';
            }

            // Reset validation states
            const inputs = form.querySelectorAll('.form-control');
            inputs.forEach(function(input) {
                input.classList.remove('is-valid', 'is-invalid');
                input.setCustomValidity('');
            });
        }
    }
}

// ---------------------------------------------
// CSS Animations và Styles (nếu chưa có)
// ---------------------------------------------
document.addEventListener('DOMContentLoaded', function() {
    // Add CSS if not exists
    if (!document.getElementById('custom-account-styles')) {
        const style = document.createElement('style');
        style.id = 'custom-account-styles';
        style.textContent = `
            .loading-spinner {
                width: 1rem;
                height: 1rem;
                border: 2px solid transparent;
                border-top: 2px solid currentColor;
                border-radius: 50%;
                display: inline-block;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            .progress-bar {
                transition: all 0.3s ease;
            }

            .avatar-wrapper:hover .btn {
                opacity: 1;
            }

            .avatar-wrapper .btn {
                opacity: 0.8;
                transition: opacity 0.2s;
            }

            .password-toggle:hover {
                background-color: #f8f9fa;
            }

            .alert {
                transition: opacity 0.15s linear;
            }

            .form-control:focus {
                border-color: #e91e63;
                box-shadow: 0 0 0 0.2rem rgba(233, 30, 99, 0.25);
            }
        `;
        document.head.appendChild(style);
    }
});
