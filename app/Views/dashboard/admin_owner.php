<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!-- Alerts Section -->
<?php if (!empty($alerts)) : ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="glass-card py-3.5 px-4 border-start border-3 border-warning bg-warning bg-opacity-10 shadow-sm">
                <div class="fw-bold text-warning mb-2.5"><i class="fa-solid fa-bell me-2 animate-pulse"></i>Rent & Lease Expiry Action Alerts (<?= count($alerts) ?>)</div>
                <ul class="mb-0 ps-3 text-dark" style="font-size: 13px;">
                    <?php foreach ($alerts as $alert) : ?>
                        <li class="mb-2"><?= $alert['message'] ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Quick Actions Panel -->
<div class="row mb-4 no-print">
    <div class="col-12">
        <div class="glass-card py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-bolt text-warning fs-5 animate-bounce"></i>
                <h6 class="fw-bold m-0 text-dark" style="letter-spacing: -0.02em;">Quick Action Center</h6>
            </div>
            <div class="d-flex align-items-center gap-2.5 flex-wrap">
                <a href="<?= base_url('properties') ?>" class="btn btn-sm btn-primary py-2 px-3.5"><i class="fa-solid fa-plus me-1.5"></i>Add Property</a>
                <a href="<?= base_url('tenants') ?>" class="btn btn-sm btn-primary py-2 px-3.5"><i class="fa-solid fa-user-plus me-1.5"></i>Add Tenant</a>
                <a href="<?= base_url('leases') ?>" class="btn btn-sm btn-primary py-2 px-3.5"><i class="fa-solid fa-file-contract me-1.5"></i>Create Lease</a>
                <a href="<?= base_url('rent') ?>" class="btn btn-sm btn-primary py-2 px-3.5"><i class="fa-solid fa-indian-rupee-sign me-1.5"></i>Record Rent</a>
                <a href="<?= base_url('maintenance') ?>" class="btn btn-sm btn-primary py-2 px-3.5"><i class="fa-solid fa-screwdriver-wrench me-1.5"></i>Raise Ticket</a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics KPI widgets - Compact SaaS Style -->
<div class="row g-4 mb-4">
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card primary h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <p>Total Properties</p>
                <div class="icon-box">
                    <i class="fa-solid fa-building"></i>
                </div>
            </div>
            <h3><?= esc($stats['total_properties']) ?></h3>
            <small class="text-muted d-block mt-2">Registered units</small>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card success h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <p>Occupied Units</p>
                <div class="icon-box">
                    <i class="fa-solid fa-house-circle-check"></i>
                </div>
            </div>
            <h3 class="text-primary"><?= esc($stats['occupied_properties']) ?></h3>
            <small class="text-muted d-block mt-2">Active leases</small>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card danger h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <p>Vacant Units</p>
                <div class="icon-box">
                    <i class="fa-solid fa-house-circle-xmark"></i>
                </div>
            </div>
            <h3 class="text-danger"><?= esc($stats['vacant_properties']) ?></h3>
            <small class="text-muted d-block mt-2">Available for rent</small>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card warning h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <p>Total Tenants</p>
                <div class="icon-box">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <h3><?= esc($stats['total_tenants']) ?></h3>
            <small class="text-muted d-block mt-2">Lease occupants</small>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card success h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <p>Monthly Revenue</p>
                <div class="icon-box">
                    <i class="fa-solid fa-indian-rupee-sign"></i>
                </div>
            </div>
            <h3 class="text-success" style="font-size: 20px;">₹<?= number_format($stats['monthly_revenue'], 2) ?></h3>
            <small class="text-muted d-block mt-2">Collected this month</small>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card danger h-100">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <p>Pending Tickets</p>
                <div class="icon-box">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>
            </div>
            <h3 class="text-warning"><?= esc($stats['pending_tickets']) ?></h3>
            <small class="text-muted d-block mt-2">Active service tasks</small>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-8 col-12">
        <div class="custom-table-card p-4">
            <h6 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-chart-line text-primary me-2"></i>Revenue curve (Last 6 Months)</h6>
            <div style="height: 250px; position: relative;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-12">
        <div class="custom-table-card p-4">
            <h6 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-chart-pie text-info me-2"></i>Occupancy Status</h6>
            <div style="height: 250px; position: relative;" class="d-flex align-items-center justify-content-center">
                <canvas id="occupancyChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Lists & Timelines Row -->
<div class="row g-4">
    <!-- Recent Rent Transactions -->
    <div class="col-lg-6 col-12">
        <div class="custom-table-card">
            <div class="custom-table-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-dark"><i class="fa-solid fa-wallet text-success me-2"></i>Recent Collections</h6>
                <a href="<?= base_url('rent') ?>" class="btn btn-sm btn-outline-secondary py-0.5 px-2" style="font-size: 11.5px;">View Ledger</a>
            </div>
            <div class="table-responsive">
                <table class="table custom-table table-hover">
                    <thead>
                        <tr>
                            <th>Receipt</th>
                            <th>Tenant</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_payments)) : ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No collections logged.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($recent_payments as $payment) : ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('rent/receipt/' . $payment['id']) ?>" class="fw-bold text-primary text-decoration-none">
                                            <?= esc($payment['receipt_number']) ?>
                                        </a>
                                    </td>
                                    <td><?= esc($payment['tenant_name']) ?></td>
                                    <td class="fw-bold text-success">₹<?= number_format($payment['amount'], 2) ?></td>
                                    <td><?= date('d M Y', strtotime($payment['payment_date'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent activities / Service Tickets Timeline -->
    <div class="col-lg-6 col-12">
        <div class="custom-table-card h-100">
            <div class="custom-table-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-dark"><i class="fa-solid fa-list-ul text-primary me-2"></i>Recent Operational activities</h6>
                <a href="<?= base_url('maintenance') ?>" class="btn btn-sm btn-outline-secondary py-0.5 px-2" style="font-size: 11.5px;">View Tickets</a>
            </div>
            <div class="p-3" style="max-height: 290px; overflow-y: auto;">
                <?php if (empty($recent_tickets)) : ?>
                    <div class="text-center text-muted py-5">No recent activities logged.</div>
                <?php else : ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($recent_tickets as $ticket) : ?>
                            <div class="p-3 rounded border bg-light bg-opacity-50 d-flex align-items-center justify-content-between gap-3">
                                <div>
                                    <span class="fw-bold text-dark d-block">TKT-<?= str_pad($ticket['id'], 5, '0', STR_PAD_LEFT) ?> &bull; <?= esc($ticket['title']) ?></span>
                                    <small class="text-muted d-block" style="font-size: 11px;">Raised by <?= esc($ticket['tenant_name']) ?> for <?= esc($ticket['property_name']) ?></small>
                                </div>
                                <span class="badge-status <?= strtolower(str_replace(' ', '', $ticket['status'])) ?>" style="font-size: 10px;">
                                    <?= esc($ticket['status']) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        // 1. Revenue collection chart (Line chart with gradient fill)
        var ctxRevenue = document.getElementById('revenueChart').getContext('2d');
        var revenueGradient = ctxRevenue.createLinearGradient(0, 0, 0, 220);
        revenueGradient.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
        revenueGradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

        var revenueChart = new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: <?= json_encode($charts['revenue_labels']) ?>,
                datasets: [{
                    label: 'Revenue (INR)',
                    data: <?= json_encode($charts['revenue_data']) ?>,
                    borderColor: '#6366f1',
                    backgroundColor: revenueGradient,
                    fill: true,
                    tension: 0.38,
                    borderWidth: 3,
                    pointBackgroundColor: '#6366f1',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b', font: { family: 'Inter', size: 11, weight: 500 } }
                    },
                    y: {
                        grid: { color: 'rgba(226, 232, 240, 0.6)', drawBorder: false },
                        ticks: { color: '#64748b', font: { family: 'Inter', size: 11, weight: 500 } }
                    }
                }
            }
        });

        // 2. Occupancy chart (Doughnut chart)
        var ctxOccupancy = document.getElementById('occupancyChart').getContext('2d');
        var occupancyChart = new Chart(ctxOccupancy, {
            type: 'doughnut',
            data: {
                labels: ['Occupied', 'Vacant', 'Maintenance'],
                datasets: [{
                    data: <?= json_encode($charts['occupancy_data']) ?>,
                    backgroundColor: ['#3b82f6', '#ef4444', '#f59e0b'],
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#475569', font: { family: 'Inter', size: 12, weight: 500 }, padding: 15 }
                    }
                }
            }
        });

        // 3. Click navigation for Stat Cards
        $('.stat-card').each(function() {
            var cardTitle = $(this).find('p').text().trim().toLowerCase();
            var redirectUrl = '';
            
            if (cardTitle === 'total properties') {
                redirectUrl = '<?= base_url('properties') ?>';
            } else if (cardTitle === 'occupied units') {
                redirectUrl = '<?= base_url('properties?status=rented') ?>';
            } else if (cardTitle === 'vacant units') {
                redirectUrl = '<?= base_url('properties?status=available') ?>';
            } else if (cardTitle === 'total tenants') {
                redirectUrl = '<?= base_url('tenants') ?>';
            } else if (cardTitle === 'monthly revenue') {
                redirectUrl = '<?= base_url('rent') ?>';
            } else if (cardTitle === 'pending tickets') {
                redirectUrl = '<?= base_url('maintenance?status=open') ?>';
            }
            
            if (redirectUrl) {
                $(this).on('click', function() {
                    window.location.href = redirectUrl;
                });
            }
        });
    });
</script>
<?= $this->endSection() ?>
