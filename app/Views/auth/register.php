<?= $this->extend('layout/auth') ?>

<?= $this->section('content') ?>
<h4 class="text-center mb-4 text-dark" style="font-weight: 600;">Create Account</h4>

<form action="<?= base_url('register') ?>" method="POST" enctype="multipart/form-data" id="registerForm" novalidate>
    <?= csrf_field() ?>
    
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control" placeholder="username" value="<?= old('username') ?>" required>
            <div class="text-danger mt-1 d-none inline-feedback" id="username-feedback" style="font-size: 11.5px; transition: all 0.2s ease;">
                Username must be at least 3 characters and alphanumeric.
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label for="role" class="form-label">Account Role</label>
            <select name="role" id="role" class="form-select" required>
                <option value="tenant" <?= old('role') === 'tenant' ? 'selected' : '' ?>>Tenant</option>
                <option value="owner" <?= old('role') === 'owner' ? 'selected' : '' ?>>Property Owner</option>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" name="email" id="email" class="form-control" placeholder="name@example.com" value="<?= old('email') ?>" required>
        <div class="text-danger mt-1 d-none inline-feedback" id="email-feedback" style="font-size: 11.5px; transition: all 0.2s ease;">
            Please enter a valid email address.
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0" style="border-color: #e5e7eb;"><i class="fa-solid fa-lock"></i></span>
                <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0" placeholder="At least 6 characters" style="border-color: #e5e7eb;" required>
                <span class="input-group-text bg-light text-muted border-start-0 toggle-password cursor-pointer" style="border-color: #e5e7eb; cursor: pointer;" title="Show/Hide Password"><i class="fa-solid fa-eye"></i></span>
            </div>
            <div class="text-danger mt-1 d-none inline-feedback" id="password-feedback" style="font-size: 11.5px; transition: all 0.2s ease;">
                Password must be at least 6 characters.
            </div>
            <!-- Password strength indicator -->
            <div class="progress mt-2 d-none" id="strength-container" style="height: 5px; border-radius: 3px; background-color: #e2e8f0; transition: all 0.3s ease;">
                <div id="strength-bar" class="progress-bar transition-all" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <small id="strength-text" class="text-muted d-block mt-1 d-none" style="font-size: 11px; font-weight: 500;"></small>
            
            <div class="text-warning mt-1.5 d-none caps-lock-warning" id="caps-lock-warning" style="font-size: 11.5px; transition: all 0.2s ease;">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> Caps Lock is ON
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label for="password_confirm" class="form-label">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-muted border-end-0" style="border-color: #e5e7eb;"><i class="fa-solid fa-lock"></i></span>
                <input type="password" id="password_confirm" class="form-control border-start-0 border-end-0" placeholder="Confirm password" style="border-color: #e5e7eb;" required>
                <span class="input-group-text bg-light text-muted border-start-0 toggle-password cursor-pointer" style="border-color: #e5e7eb; cursor: pointer;" title="Show/Hide Confirm Password"><i class="fa-solid fa-eye"></i></span>
            </div>
            <div class="text-danger mt-1 d-none inline-feedback" id="password_confirm-feedback" style="font-size: 11.5px; transition: all 0.2s ease;">
                Passwords do not match.
            </div>
            <div class="text-warning mt-1.5 d-none caps-lock-warning" id="caps-lock-confirm-warning" style="font-size: 11.5px; transition: all 0.2s ease;">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> Caps Lock is ON
            </div>
        </div>
    </div>

    <!-- Personal Profile Block (Required for both Tenant & Owner profiles) -->
    <div id="profile-fields">
        <hr class="my-4" style="border-top: 1px solid rgba(0,0,0,0.08);">
        <h6 class="mb-3 text-uppercase" style="font-size: 12px; letter-spacing: 1px; color: #2563eb; font-weight: 600;">Profile Information</h6>

        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="John Doe" value="<?= old('name') ?>" required>
            <div class="text-danger mt-1 d-none inline-feedback" id="name-feedback" style="font-size: 11.5px; transition: all 0.2s ease;">
                Full name must be at least 3 characters.
            </div>
        </div>

        <div class="mb-3">
            <label for="mobile" class="form-label">Mobile Number</label>
            <input type="text" name="mobile" id="mobile" class="form-control" placeholder="10-digit number" value="<?= old('mobile') ?>" required>
            <div class="text-danger mt-1 d-none inline-feedback" id="mobile-feedback" style="font-size: 11.5px; transition: all 0.2s ease;">
                Please enter a valid mobile number.
            </div>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea name="address" id="address" class="form-control" rows="2" placeholder="Your address" required><?= old('address') ?></textarea>
            <div class="text-danger mt-1 d-none inline-feedback" id="address-feedback" style="font-size: 11.5px; transition: all 0.2s ease;">
                Address is required.
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="profile_photo" class="form-label">Profile Photo</label>
                <input type="file" name="profile_photo" id="profile_photo" class="form-control" accept="image/*">
            </div>
            <div class="col-md-6 mb-3">
                <label for="doc" class="form-label">ID Proof Document (Optional)</label>
                <input type="file" name="doc" id="doc" class="form-control" accept=".pdf,image/*">
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 my-3" id="btnSubmit">
        <span>Sign Up</span>
    </button>
</form>

<div class="auth-footer text-muted" style="font-size: 13px;">
    Already have an account? <a href="<?= base_url('login') ?>">Sign In</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle visibility for password fields
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.closest('.input-group').querySelector('input');
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Caps lock warning
        const checkCapsLock = (inputEl, warningEl) => {
            if (!inputEl || !warningEl) return;
            const listener = (e) => {
                if (e.getModifierState && e.getModifierState('CapsLock')) {
                    warningEl.classList.remove('d-none');
                } else {
                    warningEl.classList.add('d-none');
                }
            };
            inputEl.addEventListener('keyup', listener);
            inputEl.addEventListener('keydown', listener);
        };
        checkCapsLock(document.getElementById('password'), document.getElementById('caps-lock-warning'));
        checkCapsLock(document.getElementById('password_confirm'), document.getElementById('caps-lock-confirm-warning'));

        // Password strength calculator
        const passwordInput = document.getElementById('password');
        const strengthContainer = document.getElementById('strength-container');
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('strength-text');

        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const val = this.value;
                if (!val) {
                    strengthContainer.classList.add('d-none');
                    strengthText.classList.add('d-none');
                    return;
                }

                strengthContainer.classList.remove('d-none');
                strengthText.classList.remove('d-none');

                let score = 0;
                if (val.length >= 6) score += 20;
                if (val.length >= 8) score += 20;
                if (/[a-z]/.test(val)) score += 15;
                if (/[A-Z]/.test(val)) score += 15;
                if (/[0-9]/.test(val)) score += 15;
                if (/[^A-Za-z0-9]/.test(val)) score += 15;

                strengthBar.style.width = score + '%';

                if (score <= 40) {
                    strengthBar.className = 'progress-bar bg-danger';
                    strengthText.textContent = 'Weak password (add uppercase, numbers, symbols)';
                    strengthText.className = 'text-danger mt-1 d-block';
                } else if (score <= 75) {
                    strengthBar.className = 'progress-bar bg-warning';
                    strengthText.textContent = 'Medium password (add special characters)';
                    strengthText.className = 'text-warning mt-1 d-block';
                } else {
                    strengthBar.className = 'progress-bar bg-success';
                    strengthText.textContent = 'Strong secure password!';
                    strengthText.className = 'text-success mt-1 d-block';
                }
            });
        }

        // Form submission validation & spinner
        const form = document.getElementById('registerForm');
        const submitBtn = document.getElementById('btnSubmit');

        if (form && submitBtn) {
            form.addEventListener('submit', function(e) {
                let isValid = true;

                // Username validation
                const username = document.getElementById('username');
                const usernameFeedback = document.getElementById('username-feedback');
                if (username.value.trim().length < 3) {
                    username.classList.add('is-invalid');
                    usernameFeedback.classList.remove('d-none');
                    isValid = false;
                } else {
                    username.classList.remove('is-invalid');
                    usernameFeedback.classList.add('d-none');
                }

                // Email validation
                const email = document.getElementById('email');
                const emailFeedback = document.getElementById('email-feedback');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email.value.trim())) {
                    email.classList.add('is-invalid');
                    emailFeedback.classList.remove('d-none');
                    isValid = false;
                } else {
                    email.classList.remove('is-invalid');
                    emailFeedback.classList.add('d-none');
                }

                // Password validation
                const password = document.getElementById('password');
                const passwordFeedback = document.getElementById('password-feedback');
                if (password.value.length < 6) {
                    password.classList.add('is-invalid');
                    passwordFeedback.classList.remove('d-none');
                    isValid = false;
                } else {
                    password.classList.remove('is-invalid');
                    passwordFeedback.classList.add('d-none');
                }

                // Password confirm match check
                const confirmPassword = document.getElementById('password_confirm');
                const confirmFeedback = document.getElementById('password_confirm-feedback');
                if (confirmPassword.value !== password.value) {
                    confirmPassword.classList.add('is-invalid');
                    confirmFeedback.classList.remove('d-none');
                    isValid = false;
                } else {
                    confirmPassword.classList.remove('is-invalid');
                    confirmFeedback.classList.add('d-none');
                }

                // Name validation
                const name = document.getElementById('name');
                const nameFeedback = document.getElementById('name-feedback');
                if (name.value.trim().length < 3) {
                    name.classList.add('is-invalid');
                    nameFeedback.classList.remove('d-none');
                    isValid = false;
                } else {
                    name.classList.remove('is-invalid');
                    nameFeedback.classList.add('d-none');
                }

                // Mobile validation
                const mobile = document.getElementById('mobile');
                const mobileFeedback = document.getElementById('mobile-feedback');
                if (mobile.value.trim().length < 10) {
                    mobile.classList.add('is-invalid');
                    mobileFeedback.classList.remove('d-none');
                    isValid = false;
                } else {
                    mobile.classList.remove('is-invalid');
                    mobileFeedback.classList.add('d-none');
                }

                // Address validation
                const address = document.getElementById('address');
                const addressFeedback = document.getElementById('address-feedback');
                if (!address.value.trim()) {
                    address.classList.add('is-invalid');
                    addressFeedback.classList.remove('d-none');
                    isValid = false;
                } else {
                    address.classList.remove('is-invalid');
                    addressFeedback.classList.add('d-none');
                }

                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first invalid element
                    const firstInvalid = document.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    return;
                }

                // Disable submit button and add loading spinner
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
            });

            // Instant clear feedbacks on input type
            const setupInputListener = (inputEl, feedbackEl) => {
                if (!inputEl || !feedbackEl) return;
                inputEl.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                    feedbackEl.classList.add('d-none');
                });
            };

            setupInputListener(document.getElementById('username'), document.getElementById('username-feedback'));
            setupInputListener(document.getElementById('email'), document.getElementById('email-feedback'));
            setupInputListener(document.getElementById('password'), document.getElementById('password-feedback'));
            setupInputListener(document.getElementById('password_confirm'), document.getElementById('password_confirm-feedback'));
            setupInputListener(document.getElementById('name'), document.getElementById('name-feedback'));
            setupInputListener(document.getElementById('mobile'), document.getElementById('mobile-feedback'));
            setupInputListener(document.getElementById('address'), document.getElementById('address-feedback'));
        }
    });
</script>
<?= $this->endSection() ?>
