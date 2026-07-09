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
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom Auth Styling (Local) -->
    <link href="<?= base_url('css/auth.css') ?>" rel="stylesheet">
</head>
<body>

    <!-- Premium GSAP Splash Screen -->
    <div id="splash-screen" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at 0% 15%, rgba(217,236,255,.75) 0%, transparent 45%), radial-gradient(circle at 100% 0%, rgba(255,231,214,.70) 0%, transparent 42%), radial-gradient(circle at 100% 100%, rgba(232,217,255,.65) 0%, transparent 40%), linear-gradient(135deg,#EEF8FF 0%,#FFFFFF 45%,#FDFEFF 100%); z-index: 9999; display: flex; align-items: center; justify-content: center; flex-direction: column; color: #1e293b;">
        <div class="text-center" id="splash-content" style="opacity: 0; transform: translateY(20px) scale(0.94); filter: drop-shadow(0 8px 24px rgba(40, 60, 120, 0.05));">
            <div id="splash-logo" style="font-size: 55px; background: linear-gradient(135deg, #3b82f6, #6366f1); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 20px; filter: drop-shadow(0 4px 12px rgba(99, 102, 241, 0.2));">
                <i class="fa-solid fa-house-laptop" style="-webkit-text-fill-color: initial; color: #3b82f6;"></i>
            </div>
            <h1 id="splash-title" style="font-size: 36px; font-weight: 700; margin: 0 0 10px 0; background: linear-gradient(135deg, #1e293b, #475569); -webkit-background-clip: text; -webkit-text-fill-color: transparent; letter-spacing: -0.02em;">RentFlow</h1>
            <p id="splash-subtitle" style="font-size: 14px; color: #64748b; font-weight: 500; margin: 0; letter-spacing: 0.5px;">Premium Real Estate Management System</p>
        </div>
    </div>

    <div class="auth-card">
        <div class="brand-logo">
            <i class="fa-solid fa-house-laptop me-2"></i>RentFlow <span class="fw-normal text-muted" style="font-size: 13px;">SaaS</span>
        </div>

        <!-- Alert messages -->
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success border-0 py-2.5 bg-success bg-opacity-10 text-success">
                <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger border-0 py-2.5 bg-danger bg-opacity-10 text-danger">
                <i class="fa-solid fa-circle-exclamation me-2"></i><?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')) : ?>
            <div class="alert alert-danger border-0 py-2.5 bg-danger bg-opacity-10 text-danger">
                <ul class="mb-0 ps-3">
                    <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Render Content -->
        <?= $this->renderSection('content') ?>
    </div>

    <!-- GSAP Animations (Local) -->
    <script src="<?= base_url('js/gsap.min.js') ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var splash = document.getElementById('splash-screen');
            if (!splash) return;

            if (typeof gsap !== 'undefined') {
                var tl = gsap.timeline({
                    onComplete: function() {
                        gsap.to(splash, {
                            opacity: 0,
                            duration: 0.4,
                            onComplete: function() {
                                splash.style.display = 'none';
                            }
                        });
                    }
                });

                tl.to('#splash-content', {
                    opacity: 1,
                    y: 0,
                    scale: 1,
                    duration: 0.8,
                    ease: 'power2.out'
                }).to({}, { duration: 0.4 }); // Hold state briefly
            } else {
                // Safe CSS fallback animation if GSAP fails to load
                splash.style.opacity = '1';
                var splashContent = document.getElementById('splash-content');
                if (splashContent) {
                    splashContent.style.opacity = '1';
                    splashContent.style.transform = 'translateY(0) scale(1)';
                }
                
                setTimeout(function() {
                    splash.style.transition = 'opacity 0.4s ease';
                    splash.style.opacity = '0';
                    setTimeout(function() {
                        splash.style.display = 'none';
                    }, 400);
                }, 1200);
            }
        });
    </script>

    <!-- Bootstrap 5 Bundle JS (Local) -->
    <script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
