<?= $this->extend('layout/auth') ?>

<?= $this->section('content') ?>
<h5 class="text-center mb-4 text-dark" style="font-weight: 600;">Sign in to your account</h5>

<form action="<?= base_url('login') ?>" method="POST" id="loginForm" novalidate>
    <?= csrf_field() ?>
    
    <div class="mb-3">
        <label for="username" class="form-label">Username or Email</label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0" style="border-color: #e5e7eb;"><i class="fa-solid fa-user"></i></span>
            <input type="text" name="username" id="username" class="form-control border-start-0" placeholder="Enter username or email" value="<?= old('username') ?>" style="border-color: #e5e7eb;" required>
        </div>
        <div class="text-danger mt-1 d-none inline-feedback" id="username-feedback" style="font-size: 12px; transition: all 0.2s ease;">
            Please enter your username or email.
        </div>
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label for="password" class="form-label m-0">Password</label>
            <a href="<?= base_url('forgot-password') ?>" style="font-size: 12.5px; color: #2563eb; text-decoration: none; font-weight: 500;">Forgot Password?</a>
        </div>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0" style="border-color: #e5e7eb;"><i class="fa-solid fa-lock"></i></span>
            <input type="password" name="password" id="password" class="form-control border-start-0 border-end-0" placeholder="Enter password" style="border-color: #e5e7eb;" required>
            <span class="input-group-text bg-light text-muted border-start-0 toggle-password cursor-pointer" style="border-color: #e5e7eb; cursor: pointer;" title="Show/Hide Password"><i class="fa-solid fa-eye"></i></span>
        </div>
        <div class="text-danger mt-1 d-none inline-feedback" id="password-feedback" style="font-size: 12px; transition: all 0.2s ease;">
            Please enter your password.
        </div>
        <div class="text-warning mt-1.5 d-none caps-lock-warning" id="caps-lock-warning" style="font-size: 12px; transition: all 0.2s ease;">
            <i class="fa-solid fa-triangle-exclamation me-1 animate-pulse"></i> Caps Lock is ON
        </div>
    </div>

    <div class="mb-4 form-check">
        <input class="form-check-input" type="checkbox" name="remember" id="remember" style="cursor: pointer;">
        <label class="form-check-label text-muted" for="remember" style="font-size: 13.5px; cursor: pointer; user-select: none;">
            Remember Me
        </label>
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3" id="btnSubmit">
        <span>Sign In</span>
    </button>
</form>

<div class="auth-footer text-muted" style="font-size: 13px;">
    Don't have an account yet? <a href="<?= base_url('register') ?>">Create an Account</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password visibility toggle
        const toggleBtn = document.querySelector('.toggle-password');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                const passwordInput = document.getElementById('password');
                const icon = this.querySelector('i');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        }

        // Caps lock warning check
        const passwordField = document.getElementById('password');
        const capsWarning = document.getElementById('caps-lock-warning');
        if (passwordField && capsWarning) {
            const checkCapsLock = (e) => {
                if (e.getModifierState && e.getModifierState('CapsLock')) {
                    capsWarning.classList.remove('d-none');
                } else {
                    capsWarning.classList.add('d-none');
                }
            };
            passwordField.addEventListener('keyup', checkCapsLock);
            passwordField.addEventListener('keydown', checkCapsLock);
        }

        // Form submission loading indicators & validation
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('btnSubmit');
        if (form && submitBtn) {
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                const username = document.getElementById('username');
                const usernameFeedback = document.getElementById('username-feedback');
                if (!username.value.trim()) {
                    username.classList.add('is-invalid');
                    usernameFeedback.classList.remove('d-none');
                    isValid = false;
                } else {
                    username.classList.remove('is-invalid');
                    usernameFeedback.classList.add('d-none');
                }

                const password = document.getElementById('password');
                const passwordFeedback = document.getElementById('password-feedback');
                if (!password.value) {
                    password.classList.add('is-invalid');
                    passwordFeedback.classList.remove('d-none');
                    isValid = false;
                } else {
                    password.classList.remove('is-invalid');
                    passwordFeedback.classList.add('d-none');
                }

                if (!isValid) {
                    e.preventDefault();
                    return;
                }

                // Disable button and play GSAP transition before submit
                e.preventDefault();
                submitBtn.disabled = true;

                if (typeof gsap !== 'undefined') {
                    // Fade out card contents
                    gsap.to('.auth-card > *', {
                        opacity: 0,
                        y: -10,
                        duration: 0.25,
                        stagger: 0.04,
                        onComplete: function() {
                            // Hide children rather than replacing HTML to keep the form active in DOM
                            const card = document.querySelector('.auth-card');
                            if (card) {
                                Array.from(card.children).forEach(function(child) {
                                    child.style.display = 'none';
                                });
                                
                                // Render spinner inside the card as a new child
                                const spinnerDiv = document.createElement('div');
                                spinnerDiv.className = 'd-flex flex-column align-items-center justify-content-center py-5';
                                spinnerDiv.innerHTML = `
                                    <div class="spinner-border text-primary mb-3" style="width: 2.5rem; height: 2.5rem;" role="status"></div>
                                    <div class="text-muted fw-bold" style="font-size: 13px;">Signing in...</div>
                                `;
                                card.appendChild(spinnerDiv);
                                gsap.fromTo(spinnerDiv, { opacity: 0, scale: 0.9 }, { opacity: 1, scale: 1, duration: 0.2 });
                            }
                            
                            // Submit after 0.5s spinner delay
                            setTimeout(function() {
                                form.submit();
                            }, 500);
                        }
                    });
                } else {
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
                    form.submit();
                }
            });

            // Instant clear of validation states when user edits
            document.getElementById('username').addEventListener('input', function() {
                this.classList.remove('is-invalid');
                document.getElementById('username-feedback').classList.add('d-none');
            });

            document.getElementById('password').addEventListener('input', function() {
                this.classList.remove('is-invalid');
                document.getElementById('password-feedback').classList.add('d-none');
            });
        }
    });
</script>
<?= $this->endSection() ?>
