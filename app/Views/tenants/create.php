<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-12">
        <div class="form-card">
            <h5 class="mb-4 fw-bold"><i class="fa-solid fa-plus text-primary me-2"></i>Add Tenant Profile</h5>
            
            <form action="<?= base_url('tenants/store') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="John Doe" value="<?= old('name') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="mobile" class="form-label">Mobile Number</label>
                        <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter 10-digit number" value="<?= old('mobile') ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="john@example.com" value="<?= old('email') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="user_id" class="form-label">Link to User Account (Optional)</label>
                        <select name="user_id" id="user_id" class="form-select">
                            <option value="">-- Don't Link / Assign Later --</option>
                            <?php foreach ($available_users as $user) : ?>
                                <option value="<?= $user['id'] ?>" <?= old('user_id') == $user['id'] ? 'selected' : '' ?>>
                                    <?= esc($user['username']) ?> (<?= esc($user['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Auto Account Creation Row -->
                <div class="mb-4 mt-2 p-3 rounded bg-light border" id="auto-account-container">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" name="create_account" id="create_account" value="1" <?= old('create_account') == '1' ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold text-dark" for="create_account">
                            Auto-create Tenant login credentials
                        </label>
                    </div>
                    <div class="text-muted mt-1" style="font-size: 13px;">
                        If checked, the system will automatically create a login account. Default credentials: Username: <code>lowercase_name[random_number]</code>, Password: <code>password123</code>.
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="aadhaar_number" class="form-label">Aadhaar Number (12 digits)</label>
                        <input type="text" name="aadhaar_number" id="aadhaar_number" class="form-control" placeholder="1234 5678 9012" value="<?= old('aadhaar_number') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="pan_number" class="form-label">PAN Number</label>
                        <input type="text" name="pan_number" id="pan_number" class="form-control" placeholder="ABCDE1234F" value="<?= old('pan_number') ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Permanent Address</label>
                    <textarea name="address" id="address" class="form-control" rows="3" placeholder="Enter permanent residential address" required><?= old('address') ?></textarea>
                </div>

                <div class="mb-4">
                    <label for="doc" class="form-label">Verification Document (Aadhaar / PAN Copy)</label>
                    <input type="file" name="doc" id="doc" class="form-control" accept=".pdf,image/*">
                    <span class="text-muted" style="font-size: 12px;">Supported: PDF, JPG, JPEG, PNG. Max size 4MB.</span>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="<?= base_url('tenants') ?>" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Tenant Profile</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {
        // Toggle auto account switch based on user_id selection
        $('#user_id').change(function() {
            if ($(this).val() !== '') {
                $('#create_account').prop('checked', false);
                $('#auto-account-container').slideUp();
            } else {
                $('#auto-account-container').slideDown();
            }
        });
        
        // Trigger on load
        $('#user_id').trigger('change');
    });
</script>
<?= $this->endSection() ?>
