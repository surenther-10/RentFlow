<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="form-card text-center py-5">
            <i class="fa-solid fa-circle-exclamation text-warning fs-1 mb-4"></i>
            <h3 class="fw-bold mb-3">Profile Link Required</h3>
            <p class="text-muted mb-4"><?= esc($message) ?></p>
            <p class="text-muted" style="font-size: 14px;">An administrator or owner needs to link your user account <strong><?= esc(session()->get('username')) ?></strong> to a tenant profile before you can access the tenant portal.</p>
            <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger mt-3">Logout</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
