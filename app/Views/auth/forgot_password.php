<?= $this->extend('layout/auth') ?>

<?= $this->section('content') ?>
<h5 class="text-center mb-2 text-dark" style="font-weight: 600;">Reset Password</h5>
<p class="text-center text-muted mb-4" style="font-size: 13.5px;">Enter your email address and we'll simulate sending a password reset link.</p>

<form action="<?= base_url('forgot-password') ?>" method="POST">
    <?= csrf_field() ?>
    
    <div class="mb-4">
        <label for="email" class="form-label">Email Address</label>
        <div class="input-group">
            <span class="input-group-text bg-light text-muted border-end-0" style="border-color: #e5e7eb;"><i class="fa-solid fa-envelope"></i></span>
            <input type="email" name="email" id="email" class="form-control border-start-0" placeholder="name@example.com" value="<?= old('email') ?>" style="border-color: #e5e7eb;" required>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">Send Reset Link</button>
</form>

<div class="auth-footer text-muted" style="font-size: 13px;">
    Back to <a href="<?= base_url('login') ?>">Sign In</a>
</div>
<?= $this->endSection() ?>
