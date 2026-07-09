<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentFlow</title>
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
    <!-- Bootstrap 5 CSS (Local) -->
    <link href="<?= base_url('css/bootstrap.min.css') ?>" rel="stylesheet">
    <!-- FontAwesome Icons (Local) -->
    <link href="<?= base_url('css/all.min.css') ?>" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet">
    <!-- Custom Glassmorphic Premium Styles -->
    <link href="<?= base_url('css/custom.css') ?>" rel="stylesheet">
</head>
<body>
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }
    </script>

    <!-- Sidebar Navigation - White Glass Floating Sidebar -->
    <div class="sidebar no-print">
        <div class="brand">
            <i class="fa-solid fa-house-laptop"></i>
            <span>RentFlow <small class="fw-normal text-muted" style="font-size: 11px;">SaaS</small></span>
        </div>
        <div class="sidebar-nav">
            <a href="<?= base_url('dashboard') ?>" class="<?= url_is('dashboard') || url_is('/') ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>
            
            <?php if (session()->get('role') === 'admin' || session()->get('role') === 'owner') : ?>
                <a href="<?= base_url('properties') ?>" class="<?= url_is('properties*') ? 'active' : '' ?>">
                    <i class="fa-solid fa-building"></i>
                    <span>Properties</span>
                </a>
                <a href="<?= base_url('tenants') ?>" class="<?= url_is('tenants*') ? 'active' : '' ?>">
                    <i class="fa-solid fa-user-tie"></i>
                    <span>Tenants</span>
                </a>
                <a href="<?= base_url('leases') ?>" class="<?= url_is('leases*') ? 'active' : '' ?>">
                    <i class="fa-solid fa-file-signature"></i>
                    <span>Leases</span>
                </a>
                <a href="<?= base_url('rent') ?>" class="<?= url_is('rent*') ? 'active' : '' ?>">
                    <i class="fa-solid fa-credit-card"></i>
                    <span>Rent Collections</span>
                </a>
                <a href="<?= base_url('maintenance') ?>" class="<?= url_is('maintenance*') ? 'active' : '' ?>">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                    <span>Maintenance</span>
                </a>
                <a href="<?= base_url('reports') ?>" class="<?= url_is('reports*') ? 'active' : '' ?>">
                    <i class="fa-solid fa-chart-column"></i>
                    <span>Reports</span>
                </a>
            <?php else : ?>
                <a href="<?= base_url('rent') ?>" class="<?= url_is('rent*') ? 'active' : '' ?>">
                    <i class="fa-solid fa-receipt"></i>
                    <span>Rent History</span>
                </a>
                <a href="<?= base_url('maintenance') ?>" class="<?= url_is('maintenance*') ? 'active' : '' ?>">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                    <span>Service Requests</span>
                </a>
            <?php endif; ?>

            <?php if (session()->get('role') === 'admin') : ?>
                <hr class="my-2 border-secondary border-opacity-25" style="margin-left: 15px; margin-right: 15px;">
                <div class="px-3 mb-1 text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Administration</div>
                <a href="<?= base_url('admin/users') ?>" class="<?= url_is('admin/users*') ? 'active' : '' ?>">
                    <i class="fa-solid fa-users-gear"></i>
                    <span>User Management</span>
                </a>
                <a href="<?= base_url('admin/settings') ?>" class="<?= url_is('admin/settings*') ? 'active' : '' ?>">
                    <i class="fa-solid fa-sliders"></i>
                    <span>Settings</span>
                </a>
            <?php endif; ?>

            <a href="<?= base_url('profile') ?>" class="<?= url_is('profile') ? 'active' : '' ?>">
                <i class="fa-solid fa-user-gear"></i>
                <span>My Profile</span>
            </a>

            <a href="<?= base_url('change-password') ?>" class="<?= url_is('change-password') ? 'active' : '' ?>">
                <i class="fa-solid fa-key"></i>
                <span>Change Password</span>
            </a>

            <a href="<?= base_url('logout') ?>" class="mt-auto text-danger">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        
        <!-- Top Navbar -->
        <div class="top-navbar no-print">
            <div class="d-flex align-items-center gap-3">
                <!-- Breadcrumbs dynamic tracker -->
                <?php
                $uri = service('uri');
                $segments = $uri->getSegments();
                ?>
                <div class="breadcrumb-trail no-print d-flex align-items-center flex-wrap">
                    <a href="<?= base_url('dashboard') ?>" class="text-decoration-none d-flex align-items-center gap-2">
                        <i class="fa-solid fa-house-laptop text-primary" style="font-size: 20px;"></i>
                        <span class="fw-bold text-primary" style="font-size: 18.5px; letter-spacing: -0.02em;">RentFlow <small class="fw-normal text-muted" style="font-size: 12px; margin-left: 2px;">SaaS</small></span>
                    </a>
                    <span class="text-muted d-none d-md-inline" style="opacity: 0.5; margin: 0 10px; font-size: 14px;">|</span>
                    <span class="text-muted fw-normal d-none d-md-inline me-2" style="font-size: 13px; font-weight: 500; color: #94a3b8 !important;">Smart Rental Platform</span>
                    
                    <?php foreach ($segments as $index => $segment) : ?>
                        <?php if ($segment !== 'dashboard') : ?>
                            <span class="mx-2 text-muted">/</span>
                            <span class="text-capitalize <?= ($index === count($segments) - 1) ? 'text-dark fw-bold' : '' ?>" style="font-size: 13.5px; vertical-align: middle;">
                                <?= esc(str_replace('-', ' ', $segment)) ?>
                            </span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-4">
                <!-- Search Box -->
                <div class="input-group search-input-group d-none d-lg-flex">
                    <input type="text" id="global-search-input" class="form-control" placeholder="Search operations..." autocomplete="off">
                    <div id="global-search-results" class="global-search-dropdown"></div>
                </div>

                <!-- Notifications Bell Dropdown -->
                <div class="dropdown notification-bell-container">
                    <?php
                    $notifModel = new \App\Models\NotificationModel();
                    $unreadNotifs = $notifModel->getUnread(session()->get('id'));
                    $unreadCount = count($unreadNotifs);
                    ?>
                    <div class="text-secondary" data-bs-toggle="dropdown" aria-expanded="false" id="notifDropdownBtn">
                        <i class="fa-regular fa-bell fs-5"></i>
                        <?php if ($unreadCount > 0) : ?>
                            <span class="badge bg-danger notification-badge text-white"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark-custom p-0" aria-labelledby="notifDropdownBtn" style="border: 1px solid var(--border-glass) !important; border-radius: 16px !important; overflow: hidden; background: rgba(255, 255, 255, 0.9) !important; backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);">
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top bg-opacity-50">
                            <span class="fw-bold text-dark">Notifications</span>
                            <?php if ($unreadCount > 0) : ?>
                                <a href="<?= base_url('dashboard/markNotificationsRead') ?>" class="text-primary text-decoration-none" style="font-size: 11px;">Mark all read</a>
                            <?php endif; ?>
                        </div>
                        <div style="max-height: 280px; overflow-y: auto;">
                            <?php if (empty($unreadNotifs)) : ?>
                                <div class="p-4 text-center text-muted" style="font-size: 13px;">
                                    <i class="fa-regular fa-bell-slash d-block fs-4 mb-2 opacity-50"></i>
                                    No new notifications.
                                </div>
                            <?php else : ?>
                                <?php foreach ($unreadNotifs as $notif) : ?>
                                    <div class="notification-item unread" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                        <div class="fw-bold text-dark"><?= esc($notif['title']) ?></div>
                                        <div class="text-muted mt-1" style="font-size: 11.5px;"><?= esc($notif['message']) ?></div>
                                        <div class="text-end mt-1 text-muted" style="font-size: 9px;"><?= date('d M h:i A', strtotime($notif['created_at'])) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </ul>
                </div>

                <!-- Theme Sun icon -->
                <div class="text-muted cursor-pointer d-none d-sm-block" title="Bright Mode Active" id="theme-toggle-btn">
                    <i class="fa-solid fa-circle-half-stroke fs-5"></i>
                </div>

                <!-- Profile Dropdown -->
                <div class="dropdown">
                    <div class="d-flex align-items-center gap-3 cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" id="profileDropdown">
                        <div class="text-end d-none d-md-block">
                            <span class="d-block fw-bold text-dark" style="font-size: 13.5px;"><?= esc(session()->get('username')) ?></span>
                            <span class="text-muted text-capitalize" style="font-size: 11px;"><?= esc(session()->get('role')) ?></span>
                        </div>
                        <?php 
                        $profilePhoto = session()->get('profile_photo');
                        if (!empty($profilePhoto) && file_exists(FCPATH . $profilePhoto)) : ?>
                            <img src="<?= base_url($profilePhoto) ?>" alt="avatar" class="rounded-circle shadow-sm" style="width: 36px; height: 36px; object-fit: cover;">
                        <?php else : ?>
                            <div class="avatar text-white bg-primary rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 36px; height: 36px; font-size: 13px; background: var(--primary-gradient) !important;">
                                <?= strtoupper(substr(session()->get('username'), 0, 2)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark-custom p-0" aria-labelledby="profileDropdown" style="min-width: 220px; border: 1px solid var(--border-glass) !important; border-radius: 16px !important; overflow: hidden; background: rgba(255, 255, 255, 0.9) !important; backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);">
                        <div class="p-3 border-bottom bg-light rounded-top bg-opacity-50">
                            <span class="fw-bold text-dark d-block"><?= esc(session()->get('username')) ?></span>
                            <span class="text-muted" style="font-size: 11.5px;"><?= esc(session()->get('email')) ?></span>
                        </div>
                        <a href="<?= base_url('profile') ?>" class="dropdown-item py-2.5 px-3 text-dark d-flex align-items-center">
                            <i class="fa-solid fa-user me-2.5 text-muted" style="width: 16px;"></i>My Profile
                        </a>
                        <?php if (session()->get('role') === 'admin') : ?>
                            <a href="<?= base_url('admin/settings') ?>" class="dropdown-item py-2.5 px-3 text-dark d-flex align-items-center">
                                <i class="fa-solid fa-sliders me-2.5 text-muted" style="width: 16px;"></i>Settings
                            </a>
                        <?php endif; ?>
                        <a href="<?= base_url('change-password') ?>" class="dropdown-item py-2.5 px-3 text-dark d-flex align-items-center">
                            <i class="fa-solid fa-key me-2.5 text-muted" style="width: 16px;"></i>Change Password
                        </a>
                        <a href="<?= base_url('logout') ?>" class="dropdown-item py-2.5 px-3 text-danger d-flex align-items-center" style="border-top: 1px solid rgba(0,0,0,0.05);">
                            <i class="fa-solid fa-right-from-bracket me-2.5 text-danger" style="width: 16px;"></i>Logout
                        </a>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content Body -->
        <div class="content-body">
            <!-- Render Main Page Content -->
            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <!-- Modals Section (outside content-body to prevent stacking context bugs) -->
    <?= $this->renderSection('modals') ?>

    <!-- Toast Notifications Container -->
    <div class="toast-container-custom">
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="toast custom-toast show" role="alert" aria-live="assertive" aria-atomic="true" id="successToast">
                <div class="toast-header border-0 py-2.5 bg-success bg-opacity-10 text-success">
                    <i class="fa-solid fa-circle-check me-2"></i>
                    <strong class="me-auto">Success</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body p-3 text-dark">
                    <?= session()->getFlashdata('success') ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="toast custom-toast show" role="alert" aria-live="assertive" aria-atomic="true" id="errorToast">
                <div class="toast-header border-0 py-2.5 bg-danger bg-opacity-10 text-danger">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                    <strong class="me-auto">Error</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body p-3 text-dark">
                    <?= session()->getFlashdata('error') ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')) : ?>
            <div class="toast custom-toast show" role="alert" aria-live="assertive" aria-atomic="true" id="errorsToast">
                <div class="toast-header border-0 py-2.5 bg-danger bg-opacity-10 text-danger">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                    <strong class="me-auto">Validation Failed</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body p-3 text-dark">
                    <ul class="mb-0 ps-3">
                        <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- GSAP Animations (Local) -->
    <script src="<?= base_url('js/gsap.min.js') ?>"></script>
    <script src="<?= base_url('js/ScrollTrigger.min.js') ?>"></script>
    <script src="<?= base_url('js/animations.js') ?>"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 Bundle JS (Local) -->
    <script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <!-- Chart.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.toast').removeClass('show');
            }, 6000);

            var table = $('.datatable').DataTable({
                "pageLength": 10,
                "ordering": true,
                "responsive": true,
                "language": {
                    "search": "",
                    "searchPlaceholder": "Filter records..."
                }
            });

            // Auto-filter status on pages that use datatables (like maintenance)
            var urlParams = new URLSearchParams(window.location.search);
            var statusFilter = urlParams.get('status');
            if (statusFilter && table.length) {
                if (statusFilter.toLowerCase() === 'open' || statusFilter.toLowerCase() === 'pending') {
                    // search column index 5 (Status) for 'Open' or 'In Progress' using regex
                    table.column(5).search('^(Open|In Progress)$', true, false).draw();
                } else {
                    table.column(5).search('^' + statusFilter + '$', true, false).draw();
                }
            }

            // Global Search Suggestions Dropdown Logic
            var searchTimeout = null;
            $('#global-search-input').on('input', function() {
                var query = $(this).val().trim();
                var resultsDropdown = $('#global-search-results');
                
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    resultsDropdown.hide().html('');
                    return;
                }
                
                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: '<?= base_url('index.php/dashboard/search') ?>',
                        type: 'GET',
                        data: { q: query },
                        dataType: 'json',
                        success: function(data) {
                            if (data.length === 0) {
                                resultsDropdown.html('<div class="no-results">No results found.</div>').show();
                                return;
                            }
                            
                            // Group by category
                            var groups = {};
                            data.forEach(function(item) {
                                if (!groups[item.category]) {
                                    groups[item.category] = [];
                                }
                                groups[item.category].push(item);
                            });
                            
                            var html = '';
                            for (var category in groups) {
                                html += '<div class="search-category-header">' + category + '</div>';
                                groups[category].forEach(function(item) {
                                    html += '<a href="' + item.url + '" class="search-result-item">' +
                                                '<i class="fa-solid ' + item.icon + '"></i>' +
                                                '<span>' + item.title + '</span>' +
                                            '</a>';
                                });
                            }
                            
                            resultsDropdown.html(html).show();
                            
                            // Animate search dropdown open with stagger
                            if (typeof gsap !== 'undefined') {
                                gsap.fromTo(resultsDropdown, 
                                    { opacity: 0, y: -10 }, 
                                    { opacity: 1, y: 0, duration: 0.25, ease: 'power2.out' }
                                );
                                var searchItems = resultsDropdown.find('.search-category-header, .search-result-item');
                                gsap.fromTo(searchItems, 
                                    { opacity: 0, x: -8 }, 
                                    { opacity: 1, x: 0, duration: 0.2, stagger: 0.03, ease: 'power2.out' }
                                );
                            }
                        },
                        error: function() {
                            resultsDropdown.hide();
                        }
                    });
                }, 250);
            });

            // Close search suggest when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-input-group').length) {
                    var resultsDropdown = $('#global-search-results');
                    if (resultsDropdown.is(':visible')) {
                        if (typeof gsap !== 'undefined') {
                            gsap.to(resultsDropdown, {
                                opacity: 0,
                                y: -10,
                                duration: 0.2,
                                ease: 'power2.in',
                                onComplete: function() {
                                    resultsDropdown.hide();
                                }
                            });
                        } else {
                            resultsDropdown.hide();
                        }
                    }
                }
            });

            // Focus again shows results
            $('#global-search-input').on('focus', function() {
                var resultsDropdown = $('#global-search-results');
                if ($(this).val().trim().length >= 2) {
                    resultsDropdown.show();
                    if (typeof gsap !== 'undefined') {
                        gsap.fromTo(resultsDropdown, 
                            { opacity: 0, y: -10 }, 
                            { opacity: 1, y: 0, duration: 0.25, ease: 'power2.out' }
                        );
                    }
                }
            });

            // Light/Dark Theme Toggle Logic
            var themeToggleBtn = $('#theme-toggle-btn');
            if (themeToggleBtn.length) {
                // Read and apply active state
                var currentTheme = localStorage.getItem('theme');
                if (currentTheme === 'dark') {
                    $('body').addClass('dark-mode');
                    themeToggleBtn.attr('title', 'Dark Mode Active');
                } else {
                    $('body').removeClass('dark-mode');
                    themeToggleBtn.attr('title', 'Bright Mode Active');
                }

                themeToggleBtn.on('click', function() {
                    $('body').toggleClass('dark-mode');
                    var isDarkMode = $('body').hasClass('dark-mode');
                    if (isDarkMode) {
                        localStorage.setItem('theme', 'dark');
                        themeToggleBtn.attr('title', 'Dark Mode Active');
                    } else {
                        localStorage.setItem('theme', 'light');
                        themeToggleBtn.attr('title', 'Bright Mode Active');
                    }
                });
            }
        });
    </script>
    
    <!-- Render custom page scripts -->
    <?= $this->renderSection('scripts') ?>
</body>
</html>
