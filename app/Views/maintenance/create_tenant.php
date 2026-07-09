<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-7 col-12">
        <div class="form-card">
            <h5 class="mb-4 fw-bold"><i class="fa-solid fa-screwdriver-wrench text-primary me-2"></i>Raise Service Request</h5>
            
            <form action="<?= base_url('maintenance/store') ?>" method="POST">
                <?= csrf_field() ?>
                
                <!-- Hidden references -->
                <input type="hidden" name="tenant_id" value="<?= esc($tenant_id) ?>">
                <input type="hidden" name="property_id" value="<?= esc($property_id) ?>">

                <div class="mb-3">
                    <label for="title" class="form-label">Issue Title / Summary</label>
                    <input type="text" name="title" id="title" class="form-control" placeholder="e.g. Toilet flush not working / Kitchen tap dripping" value="<?= old('title') ?>" required>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">Full Description of the Problem</label>
                    <textarea name="description" id="description" class="form-control" rows="5" placeholder="Please describe the issue in detail, including since when it has been occurring, room location, etc..." required><?= old('description') ?></textarea>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="<?= base_url('maintenance') ?>" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-circle-check me-2"></i>Submit Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
