<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const adminWrapper = document.getElementById('adminWrapper');
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const submenuToggles = document.querySelectorAll('.submenu-toggle');

        function isMobile() {
            return window.innerWidth <= 991;
        }

        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', function () {
                if (isMobile()) {
                    sidebar.classList.toggle('mobile-open');

                    if (sidebarOverlay) {
                        sidebarOverlay.classList.toggle('show');
                    }
                } else {
                    if (adminWrapper) {
                        adminWrapper.classList.toggle('sidebar-collapsed');
                    }
                }
            });
        }

        if (sidebarOverlay && sidebar) {
            sidebarOverlay.addEventListener('click', function () {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('show');
            });
        }

        submenuToggles.forEach(function (toggle) {
            toggle.addEventListener('click', function () {
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

                parent.classList.toggle('open');
                submenu.style.display = submenu.style.display === 'block' ? 'none' : 'block';
            });
        });

        window.addEventListener('resize', function () {
            if (!isMobile()) {
                if (sidebar) {
                    sidebar.classList.remove('mobile-open');
                }

                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove('show');
                }
            }
        });
    });
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