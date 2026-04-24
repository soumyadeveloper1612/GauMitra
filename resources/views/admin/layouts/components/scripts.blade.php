<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const adminWrapper = document.getElementById('adminWrapper');
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const submenuToggles = document.querySelectorAll('.submenu-toggle');
        const sidebarScroll = document.querySelector('.sidebar-scroll');

        function isMobile() {
            return window.innerWidth <= 991;
        }

        function closeMobileSidebar() {
            if (sidebar) {
                sidebar.classList.remove('mobile-open');
            }

            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('show');
            }

            document.body.style.overflow = '';
        }

        function openMobileSidebar() {
            if (sidebar) {
                sidebar.classList.add('mobile-open');
            }

            if (sidebarOverlay) {
                sidebarOverlay.classList.add('show');
            }

            document.body.style.overflow = 'hidden';
        }

        function toggleSidebar() {
            if (!sidebar) return;

            if (isMobile()) {
                if (sidebar.classList.contains('mobile-open')) {
                    closeMobileSidebar();
                } else {
                    openMobileSidebar();
                }
            } else {
                if (adminWrapper) {
                    adminWrapper.classList.toggle('sidebar-collapsed');
                }
            }
        }

        if (menuToggle) {
            menuToggle.addEventListener('click', function (e) {
                e.preventDefault();
                toggleSidebar();
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', function () {
                closeMobileSidebar();
            });
        }

        submenuToggles.forEach(function (toggle) {
            toggle.addEventListener('click', function (e) {
                e.preventDefault();

                if (adminWrapper && adminWrapper.classList.contains('sidebar-collapsed') && !isMobile()) {
                    return;
                }

                const parent = this.closest('.has-submenu');
                if (!parent) return;

                const submenu = parent.querySelector('.submenu');
                if (!submenu) return;

                document.querySelectorAll('.has-submenu').forEach(function (item) {
                    if (item !== parent) {
                        item.classList.remove('open');

                        const otherMenu = item.querySelector('.submenu');
                        if (otherMenu) {
                            otherMenu.style.display = 'none';
                        }
                    }
                });

                const willOpen = !parent.classList.contains('open');

                if (willOpen) {
                    parent.classList.add('open');
                    submenu.style.display = 'block';

                    setTimeout(function () {
                        if (sidebarScroll) {
                            const submenuBottom = submenu.offsetTop + submenu.offsetHeight;
                            const visibleBottom = sidebarScroll.scrollTop + sidebarScroll.clientHeight;

                            if (submenuBottom > visibleBottom) {
                                sidebarScroll.scrollTo({
                                    top: submenuBottom - sidebarScroll.clientHeight + 30,
                                    behavior: 'smooth'
                                });
                            }
                        }
                    }, 80);
                } else {
                    parent.classList.remove('open');
                    submenu.style.display = 'none';
                }
            });
        });

        document.querySelectorAll('.submenu a, .menu-item > .menu-link').forEach(function (link) {
            link.addEventListener('click', function () {
                const href = this.getAttribute('href');

                if (isMobile() && href && href !== 'javascript:void(0)' && href !== '#') {
                    closeMobileSidebar();
                }
            });
        });

        window.addEventListener('resize', function () {
            if (!isMobile()) {
                closeMobileSidebar();
                document.body.style.overflow = '';
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && isMobile()) {
                closeMobileSidebar();
            }
        });
    });
</script>
<script>
    function updateHeaderDateTime() {
        const now = new Date();

        const liveDate = document.getElementById('liveDate');
        const liveTime = document.getElementById('liveTime');

        if (liveDate) {
            liveDate.textContent = now.toLocaleDateString('en-IN', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        if (liveTime) {
            liveTime.textContent = now.toLocaleTimeString('en-IN', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });
        }
    }

    updateHeaderDateTime();
    setInterval(updateHeaderDateTime, 1000);
</script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: @json(session('success')),
        timer: 2200,
        showConfirmButton: false
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: @json(session('error')),
        confirmButtonColor: '#ea580c'
    });
</script>
@endif