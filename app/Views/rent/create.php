<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center">
    <div class="col-md-8 col-12">
        <div class="form-card">
            <h5 class="mb-4 fw-bold"><i class="fa-solid fa-indian-rupee-sign text-primary me-2"></i>Record Rent Payment</h5>
            
            <form action="<?= base_url('rent/store') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label for="lease_id" class="form-label">Select Active Lease / Tenant</label>
                    <select name="lease_id" id="lease_id" class="form-select" required>
                        <option value="">-- Select Active Lease --</option>
                        <?php foreach ($leases as $lease) : ?>
                            <option value="<?= $lease['id'] ?>" data-rent="<?= $lease['monthly_rent'] ?>" <?= old('lease_id') == $lease['id'] ? 'selected' : '' ?>>
                                <?= esc($lease['tenant_name']) ?> &mdash; <?= esc($lease['property_name']) ?> (Agreement: <?= esc($lease['agreement_number']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="amount" class="form-label">Amount Received (INR)</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="amount" id="amount" class="form-control" placeholder="0.00" step="0.01" value="<?= old('amount') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="payment_date" class="form-label">Payment Date</label>
                        <input type="date" name="payment_date" id="payment_date" class="form-control" value="<?= old('payment_date', date('Y-m-d')) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="UPI" <?= old('payment_method') === 'UPI' ? 'selected' : '' ?>>UPI (GPay/PhonePe/Paytm)</option>
                            <option value="Bank Transfer" <?= old('payment_method') === 'Bank Transfer' ? 'selected' : '' ?>>NEFT / IMPS Bank Transfer</option>
                            <option value="Cash" <?= old('payment_method') === 'Cash' ? 'selected' : '' ?>>Cash Payment</option>
                            <option value="Online Payment Gateway" <?= old('payment_method') === 'Online Payment Gateway' ? 'selected' : '' ?>>Debit/Credit Card / Gateway</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Payment Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="Paid" <?= old('status') === 'Paid' ? 'selected' : '' ?>>Paid (Receipt Generated)</option>
                            <option value="Pending" <?= old('status') === 'Pending' ? 'selected' : '' ?>>Pending Verification</option>
                            <option value="Overdue" <?= old('status') === 'Overdue' ? 'selected' : '' ?>>Overdue</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="notes" class="form-label">Internal Notes / Reference Code</label>
                    <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="e.g. Transaction ID, remarks..."><?= old('notes') ?></textarea>
                </div>

                <div class="d-flex gap-3 justify-content-end">
                    <a href="<?= base_url('rent') ?>" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Rent Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {
        // Auto fill amount based on selected lease rent
        $('#lease_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var rentAmount = selectedOption.data('rent');
            if (rentAmount) {
                $('#amount').val(rentAmount);
            }
        });
    });
</script>
<?= $this->endSection() ?>
