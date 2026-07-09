<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="form-card">
            <h5 class="mb-4 fw-bold"><i class="fa-solid fa-key text-primary me-2"></i>Change Password</h5>
            
            <form action="<?= base_url('change-password') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="old_password" class="form-label">Current Password</label>
                    <input type="password" name="old_password" id="old_password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="form-control" placeholder="At least 6 characters" required>
                </div>

                <div class="mb-4">
                    <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_new_password" id="confirm_new_password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Update Password</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
