<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <h5 class="fw-bold m-0 text-white"><i class="fa-solid fa-receipt text-primary me-2"></i>Rent Payment Invoice</h5>
    <div class="d-flex gap-2">
        <a href="<?= base_url('rent') ?>" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-2"></i>Back to List</a>
        <button class="btn btn-sm btn-outline-info" onclick="window.print();"><i class="fa-solid fa-print me-2"></i>Print Receipt</button>
        <a href="<?= base_url('rent/download/' . $payment['id']) ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-file-pdf me-2"></i>Download PDF</a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8 col-12">
        <!-- Invoice Layout -->
        <div class="form-card p-5" style="background-color: #1e293b; border: 1px solid #3b506c; color: #f8fafc; border-radius: 16px;">
            <!-- Header layout -->
            <div class="row align-items-start mb-4">
                <div class="col-md-6 col-12">
                    <h2 class="fw-bold m-0" style="background: linear-gradient(45deg, #818cf8, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">RentFlow</h2>
                    <span class="text-muted" style="font-size: 13px;">Premium Property Ledger</span>
                </div>
                <div class="col-md-6 col-12 text-md-end text-start mt-md-0 mt-3">
                    <h5 class="fw-bold text-white mb-1">Receipt: <?= esc($payment['receipt_number']) ?></h5>
                    <div style="font-size: 13px; color: #94a3b8;">Issued Date: <?= date('d F Y', strtotime($payment['payment_date'])) ?></div>
                    <span class="badge bg-success bg-opacity-20 text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-bold mt-2" style="font-size: 12px;"><?= esc($payment['status']) ?></span>
                </div>
            </div>

            <hr class="border-secondary opacity-50 mb-4">

            <!-- Address grid -->
            <div class="row g-4 mb-5">
                <div class="col-md-6 col-12">
                    <h6 class="text-primary uppercase fw-bold mb-3" style="font-size: 12px; letter-spacing: 0.5px;">Tenant Details</h6>
                    <div class="fw-bold text-white fs-6 mb-1"><?= esc($payment['tenant_name']) ?></div>
                    <div class="text-muted" style="font-size: 13px;"><i class="fa-solid fa-phone me-2"></i><?= esc($payment['tenant_mobile']) ?></div>
                    <div class="text-muted" style="font-size: 13px;"><i class="fa-solid fa-envelope me-2"></i><?= esc($payment['tenant_email']) ?></div>
                </div>
                <div class="col-md-6 col-12 text-md-end text-start">
                    <h6 class="text-primary uppercase fw-bold mb-3" style="font-size: 12px; letter-spacing: 0.5px;">Property Details</h6>
                    <div class="fw-bold text-white fs-6 mb-1"><?= esc($payment['property_name']) ?></div>
                    <div class="text-muted" style="font-size: 13px;"><?= esc($lease['property_address']) ?></div>
                    <div class="text-muted" style="font-size: 13px;"><?= esc($lease['city']) ?>, <?= esc($lease['state']) ?> - <?= esc($lease['pincode']) ?></div>
                </div>
            </div>

            <!-- Ledger Grid -->
            <div class="table-responsive mb-4">
                <table class="table text-white" style="border-color: #3b506c;">
                    <thead>
                        <tr class="text-muted" style="font-size: 11px; text-transform: uppercase;">
                            <th class="border-bottom-0 pb-3">Billing Item</th>
                            <th class="border-bottom-0 pb-3">Payment Method</th>
                            <th class="border-bottom-0 pb-3">Period</th>
                            <th class="border-bottom-0 pb-3 text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="align-middle">
                            <td class="py-4">
                                <span class="fw-bold text-white">Monthly Rent Payment</span>
                                <small class="d-block text-muted mt-1" style="font-size: 12px;"><?= esc($payment['notes'] ?: 'Regular rent ledger entry.') ?></small>
                            </td>
                            <td class="py-4 text-white-50"><?= esc($payment['payment_method']) ?></td>
                            <td class="py-4 text-white-50"><?= date('F Y', strtotime($payment['payment_date'])) ?></td>
                            <td class="py-4 text-end fw-bold text-success fs-5">₹<?= number_format($payment['amount'], 2) ?></td>
                        </tr>
                        <tr style="border-top: 2px solid #3b506c;" class="bg-black bg-opacity-20">
                            <td colspan="3" class="py-3 fw-bold text-end">Grand Total Received</td>
                            <td class="py-3 text-end fw-bold text-primary fs-4">₹<?= number_format($payment['amount'], 2) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Notes -->
            <div class="text-center mt-5 text-muted" style="font-size: 11px;">
                This invoice receipt serves as proof of payment of rent for the specified property unit.<br>
                Rendered electronically by RentFlow Ledger Systems.
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
