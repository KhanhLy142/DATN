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
            items.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            console.log('Menu item clicked:', this.href);
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const passwordToggles = document.querySelectorAll('.password-toggle');

    passwordToggles.forEach(function (toggle) {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();

            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (passwordInput && icon) {
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

document.addEventListener('DOMContentLoaded', function () {

    const registerForm = document.querySelector('form[action*="register"]');

    if (registerForm) {
        const password = registerForm.querySelector('input[name="password"]');
        const confirmPassword = registerForm.querySelector('input[name="password_confirmation"]');

        if (password && confirmPassword) {
            confirmPassword.addEventListener('input', function () {
                if (this.value !== password.value) {
                    this.setCustomValidity('Mật khẩu xác nhận không khớp');
                    this.classList.remove('is-invalid', 'is-valid');
                } else {
                    this.setCustomValidity('');
                    this.classList.remove('is-invalid', 'is-valid');
                }
            });

            password.addEventListener('input', function () {
                if (confirmPassword.value && confirmPassword.value !== this.value) {
                    confirmPassword.setCustomValidity('Mật khẩu xác nhận không khớp');
                    confirmPassword.classList.remove('is-invalid', 'is-valid');
                } else if (confirmPassword.value) {
                    confirmPassword.setCustomValidity('');
                    confirmPassword.classList.remove('is-invalid', 'is-valid');
                }
            });
        }
    }
});

function initLogoutHandling() {
    const logoutForms = document.querySelectorAll('form[action*="logout"]');

    logoutForms.forEach(form => {
        form.addEventListener('submit', function(e) {
        });
    });

    const logoutButtons = document.querySelectorAll('a[href*="logout"], button[onclick*="logout"]');
    logoutButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/logout';

            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            form.appendChild(token);
            document.body.appendChild(form);
            form.submit();
        });
    });
}

    function initSuccessNotifications() {
    const urlParams = new URLSearchParams(window.location.search);
    const loginSuccess = urlParams.get('login_success');
    const registerSuccess = urlParams.get('register_success');

    if (loginSuccess === 'true') {
        showCustomNotification('Đăng nhập thành công! Chào mừng bạn trở lại.', 'success');
    }

    if (registerSuccess === 'true') {
        showCustomNotification('Đăng ký thành công! Chào mừng bạn đến với cửa hàng của chúng tôi.', 'success');
    }

    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';

            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 500);
        });
    }, 5000);
}

function showCustomNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show custom-notification`;
    notification.innerHTML = `
        <i class="bi bi-check-circle-fill me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.insertBefore(notification, document.body.firstChild);

    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 4000);
}

function initAvatarUpload() {
    const avatarInput = document.getElementById('avatar-input');
    const avatarPreview = document.getElementById('avatar-preview');

    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Vui lòng chọn file ảnh hợp lệ (JPG, PNG, GIF, WebP)');
                    this.value = '';
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    alert('Kích thước file không được vượt quá 5MB');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
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

function initAccountFormValidation() {
    const accountForm = document.querySelector('form[action*="account"]');

    if (accountForm) {
        const submitBtn = accountForm.querySelector('#submit-btn');
        const password = accountForm.querySelector('#password');
        const passwordConfirmation = accountForm.querySelector('#password_confirmation');

        if (password && passwordConfirmation) {
            function validatePasswordMatch() {
                if (password.value && passwordConfirmation.value) {
                    if (password.value !== passwordConfirmation.value) {
                        passwordConfirmation.setCustomValidity('Mật khẩu xác nhận không khớp');
                        passwordConfirmation.classList.remove('is-invalid', 'is-valid');
                    } else {
                        passwordConfirmation.setCustomValidity('');
                        passwordConfirmation.classList.remove('is-invalid', 'is-valid');
                    }
                }
            }

            password.addEventListener('input', validatePasswordMatch);
            passwordConfirmation.addEventListener('input', validatePasswordMatch);
        }
    }
}

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
                        passwordConfirmation.classList.remove('is-invalid', 'is-valid');
                    } else {
                        matchFeedback.innerHTML = '<small class="text-danger"><i class="bi bi-x-circle-fill me-1"></i>Mật khẩu không khớp</small>';
                        passwordConfirmation.setCustomValidity('Mật khẩu xác nhận không khớp');
                        passwordConfirmation.classList.remove('is-invalid', 'is-valid');
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
        });
    }
}

function initPhoneFormatting() {
    const phoneInput = document.getElementById('phone');

    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                if (value.length <= 10) {
                    value = value.replace(/(\d{4})(\d{3})(\d{3})/, '$1 $2 $3');
                }
            }
            e.target.value = value;
        });

        phoneInput.addEventListener('blur', function() {
            const phoneRegex = /^[0-9]{10,11}$/;
            const cleanPhone = this.value.replace(/\s/g, '');

            if (cleanPhone && !phoneRegex.test(cleanPhone)) {
                this.setCustomValidity('Số điện thoại không hợp lệ');
                this.classList.remove('is-invalid', 'is-valid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        });
    }
}

function initAutoHideAlerts() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert:not(.custom-notification)');
        alerts.forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';

            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 500);
        });
    }, 5000);
}

function resetForm() {
    if (confirm('Bạn có chắc chắn muốn đặt lại form?')) {
        const form = document.getElementById('change-password-form') || document.querySelector('form');
        if (form) {
            form.reset();
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

            const avatarPreview = document.getElementById('avatar-preview');
            if (avatarPreview) {
                avatarPreview.src = avatarPreview.getAttribute('data-default') || '/images/default-avatar.png';
            }

            const inputs = form.querySelectorAll('.form-control');
            inputs.forEach(function(input) {
                input.classList.remove('is-valid', 'is-invalid');
                input.setCustomValidity('');
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showCustomNotification('Đã sao chép vào clipboard!', 'info');
    });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

document.addEventListener('DOMContentLoaded', function() {
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
});
