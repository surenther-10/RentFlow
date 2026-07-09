<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-12">
        <div class="form-card">
            <h5 class="mb-4 fw-bold"><i class="fa-solid fa-screwdriver-wrench text-primary me-2"></i>Log Service Ticket</h5>
            
            <form action="<?= base_url('maintenance/store') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="lease_info" class="form-label">Select Tenant & Property</label>
                    <select name="lease_info" id="lease_info" class="form-select" required>
                        <option value="">-- Choose Active Resident --</option>
                        <?php foreach ($leases as $lease) : ?>
                            <option value="<?= $lease['tenant_id'] . '-' . $lease['property_id'] ?>">
                                <?= esc($lease['tenant_name']) ?> &mdash; <?= esc($lease['property_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Hidden variables updated by JS -->
                <input type="hidden" name="tenant_id" id="tenant_id" value="">
                <input type="hidden" name="property_id" id="property_id" value="">

                <div class="mb-3">
                    <label for="title" class="form-label">Issue Title / Summary</label>
                    <input type="text" name="title" id="title" class="form-control" placeholder="e.g. Electrical sparks in kitchen / Water seepage" value="<?= old('title') ?>" required>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">Full Description of the Problem</label>
                    <textarea name="description" id="description" class="form-control" rows="4" placeholder="Detail the complaint..." required><?= old('description') ?></textarea>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="<?= base_url('maintenance') ?>" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {
        $('#lease_info').change(function() {
            var val = $(this).val();
            if (val) {
                var parts = val.split('-');
                $('#tenant_id').val(parts[0]);
                $('#property_id').val(parts[1]);
            } else {
                $('#tenant_id').val('');
                $('#property_id').val('');
            }
        });
    });
</script>
<?= $this->endSection() ?>
