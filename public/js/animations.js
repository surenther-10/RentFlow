/**
 * RentFlow Premium SaaS Animations System (GSAP)
 * Lightweight, under 0.8s, smooth micro-interactions.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Ensure GSAP is loaded
    if (typeof gsap === 'undefined') return;

    // Respect accessibility preferences
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    // Register ScrollTrigger plugin if available
    if (typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
    }

    // Expose dynamic animation initializer on window
    window.initRentFlowAnimations = function() {
        // 1. Sidebar Active Link Highlight Indicator Slide
        const activeLink = document.querySelector('.sidebar-nav a.active');
        if (activeLink) {
            gsap.fromTo(activeLink, 
                { x: -15, opacity: 0, scaleX: 0.9 }, 
                { x: 0, opacity: 1, scaleX: 1, duration: 0.4, ease: 'back.out(1.2)' }
            );
        }

        // 2. Dashboard Stat Cards Stagger Animation
        const statCards = document.querySelectorAll('.stat-card');
        if (statCards.length > 0) {
            gsap.fromTo(statCards, 
                { opacity: 0, y: 25 },
                { 
                    opacity: 1, 
                    y: 0, 
                    duration: 0.6, 
                    stagger: 0.08, 
                    ease: 'power3.out' 
                }
            );
        }

        // 3. Dashboard Chart Draw Scale Animations using ScrollTrigger
        const canvases = document.querySelectorAll('canvas');
        canvases.forEach(canvas => {
            if (typeof ScrollTrigger !== 'undefined') {
                gsap.fromTo(canvas,
                    { scaleY: 0, opacity: 0, transformOrigin: 'bottom center' },
                    { 
                        scaleY: 1, 
                        opacity: 1, 
                        duration: 0.8, 
                        ease: 'power2.out',
                        scrollTrigger: {
                            trigger: canvas,
                            start: 'top 95%',
                            toggleActions: 'play none none none'
                        }
                    }
                );
            } else {
                gsap.fromTo(canvas,
                    { scaleY: 0, opacity: 0, transformOrigin: 'bottom center' },
                    { scaleY: 1, opacity: 1, duration: 0.7, ease: 'power2.out' }
                );
            }
        });

        // 4. Property Cards Stagger Entrance with ScrollTrigger
        const propertyCards = document.querySelectorAll('#grid-pane .glass-card, .properties-grid .glass-card');
        if (propertyCards.length > 0) {
            if (typeof ScrollTrigger !== 'undefined') {
                gsap.fromTo(propertyCards,
                    { opacity: 0, y: 30 },
                    { 
                        opacity: 1, 
                        y: 0, 
                        duration: 0.6, 
                        stagger: 0.05, 
                        ease: 'power3.out',
                        scrollTrigger: {
                            trigger: '#grid-pane, .properties-grid',
                            start: 'top 95%',
                            toggleActions: 'play none none none'
                        }
                    }
                );
            } else {
                gsap.fromTo(propertyCards,
                    { opacity: 0, y: 30 },
                    { opacity: 1, y: 0, duration: 0.6, stagger: 0.05, ease: 'power3.out' }
                );
            }
        }

        // 5. Button Hover & Active Press micro-interactions
        document.querySelectorAll('.btn-primary, .btn-outline-secondary, .btn-outline-primary, .btn-danger, .btn-success, .btn').forEach(btn => {
            if (btn.classList.contains('active')) return;

            // Remove existing listeners to avoid multiple binds
            btn.onmouseenter = null;
            btn.onmouseleave = null;
            btn.onmousedown = null;
            btn.onmouseup = null;

            btn.addEventListener('mouseenter', () => {
                gsap.to(btn, { scale: 1.03, duration: 0.2, ease: 'power2.out' });
            });

            btn.addEventListener('mouseleave', () => {
                gsap.to(btn, { scale: 1, duration: 0.2, ease: 'power2.out' });
            });

            btn.addEventListener('mousedown', () => {
                gsap.to(btn, { scale: 0.96, duration: 0.1, ease: 'power2.out' });
            });

            btn.addEventListener('mouseup', () => {
                gsap.to(btn, { scale: 1.03, duration: 0.15, ease: 'power2.out' });
            });
        });

        // 6. Property card hover lifts
        document.querySelectorAll('.glass-card').forEach(card => {
            if (card.closest('.modal-content') || card.closest('.form-card')) return;
            
            card.onmouseenter = null;
            card.onmouseleave = null;

            card.addEventListener('mouseenter', () => {
                gsap.to(card, {
                    y: -6,
                    scale: 1.015,
                    boxShadow: '0 20px 40px rgba(31, 38, 135, 0.08)',
                    duration: 0.25,
                    ease: 'power2.out'
                });
            });

            card.addEventListener('mouseleave', () => {
                gsap.to(card, {
                    y: 0,
                    scale: 1,
                    boxShadow: 'none',
                    duration: 0.25,
                    ease: 'power2.out'
                });
            });
        });

        // 7. Dashboard Numeric Stats Counter Animation
        const counterElements = document.querySelectorAll('.stat-card h3');
        counterElements.forEach(el => {
            const rawText = el.textContent.trim();
            if (/[a-zA-Z]/.test(rawText)) return;
            const numVal = parseFloat(rawText.replace(/[^\d.]/g, ''));
            
            if (!isNaN(numVal) && numVal > 0) {
                const hasCurrency = rawText.includes('₹');
                const hasDecimal = rawText.includes('.');
                const isPercent = rawText.includes('%');
                
                const obj = { value: 0 };
                gsap.to(obj, {
                    value: numVal,
                    duration: 0.75,
                    delay: 0.15, // Let cards stagger first
                    ease: 'power2.out',
                    onUpdate: function() {
                        let formatted = '';
                        if (hasDecimal) {
                            formatted = obj.value.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                        } else {
                            formatted = Math.round(obj.value).toLocaleString('en-IN');
                        }
                        
                        let finalStr = '';
                        if (hasCurrency) finalStr += '₹';
                        finalStr += formatted;
                        if (isPercent) finalStr += '%';

                        el.innerHTML = finalStr;
                    }
                });
            }
        });

        if (typeof ScrollTrigger !== 'undefined') {
            ScrollTrigger.refresh();
        }
    };

    // Initialize animations on load
    window.initRentFlowAnimations();

    // 8. Page transitions fade between modules
    const contentBody = document.querySelector('.content-body');
    if (contentBody) {
        gsap.set(contentBody, { opacity: 0, y: 15 });
        gsap.to(contentBody, {
            opacity: 1,
            y: 0,
            duration: 0.55,
            ease: 'power2.out',
            clearProps: 'transform'
        });
    }

    // 9. DataTables Row Stagger Entrance Animation
    $(document).on('init.dt', function(e, settings) {
        const table = $(settings.nTable);
        table.on('draw.dt', function() {
            const rows = this.querySelectorAll('tbody tr');
            if (rows.length > 0) {
                gsap.fromTo(rows,
                    { opacity: 0, y: 10 },
                    { opacity: 1, y: 0, duration: 0.45, stagger: 0.03, ease: 'power2.out' }
                );
            }
        });
    });

    // 10. Bootstrap Modals Zoom-in Scale Transition
    document.addEventListener('show.bs.modal', function(event) {
        const dialog = event.target.querySelector('.modal-dialog');
        if (dialog) {
            gsap.fromTo(dialog, 
                { scale: 0.92, opacity: 0, y: -20 }, 
                { scale: 1, opacity: 1, y: 0, duration: 0.4, ease: 'back.out(1.3)' }
            );
        }
    });

    // 11. Custom Alerts Slide-in & Auto-dismiss
    const alertEl = document.querySelector('.custom-alert');
    if (alertEl) {
        gsap.set(alertEl, { x: 320, opacity: 0, scale: 0.95 });
        gsap.to(alertEl, {
            x: 0,
            opacity: 1,
            scale: 1,
            duration: 0.5,
            ease: 'back.out(1.1)'
        });
        
        setTimeout(function() {
            gsap.to(alertEl, {
                x: 320,
                opacity: 0,
                scale: 0.95,
                duration: 0.35,
                ease: 'power2.in',
                onComplete: function() {
                    alertEl.remove();
                }
            });
        }, 5000);
    }

    // 12. Notification Bell Shake micro-interaction
    const badge = document.querySelector('.notification-badge');
    const bell = document.querySelector('.notification-bell-container i');
    if (badge && bell) {
        gsap.set(bell, { transformOrigin: 'top center' });
        function shakeBell() {
            if (!document.querySelector('.notification-badge')) return;
            const tl = gsap.timeline();
            tl.to(bell, { rotation: 15, duration: 0.08, ease: 'power1.inOut' })
              .to(bell, { rotation: -13, duration: 0.08, ease: 'power1.inOut' })
              .to(bell, { rotation: 10, duration: 0.08, ease: 'power1.inOut' })
              .to(bell, { rotation: -8, duration: 0.08, ease: 'power1.inOut' })
              .to(bell, { rotation: 5, duration: 0.08, ease: 'power1.inOut' })
              .to(bell, { rotation: 0, duration: 0.08, ease: 'power1.inOut' });
        }
        setTimeout(shakeBell, 2000);
        setInterval(shakeBell, 15000);
    }
});
