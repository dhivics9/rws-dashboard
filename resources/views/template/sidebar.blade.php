<aside class="w-64 bg-slate-800 text-white h-screen flex flex-col sticky top-0">
    <style>
        /* Dropdown Animation */
        .dropdown-content {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: all 0.3s ease-in-out;
        }

        .dropdown-content.active {
            max-height: 500px;
            opacity: 1;
        }

        /* Rotate Chevron */
        .rotate-180 {
            transform: rotate(180deg);
        }

        /* Active Link - Main Menu */
        .nav-link.active {
            background-color: #059669;
            /* bg-emerald-600 */
            color: white !important;
        }

        /* Active Link in Dropdown */
        .nav-link.active-dropdown {
            color: #34d399 !important;
            /* text-emerald-400 */
            background-color: #334155 !important;
            /* bg-slate-700 */
            font-weight: 500;
        }

        /* Optional: Highlight dropdown button when active */
        .dropdown-btn.active-dropdown-button {
            background-color: #334155;
            color: #34d399;
        }
    </style>

    <!-- Logo & Brand -->
    <div class="p-6 flex flex-col items-center">
        <img src="/logo.jpg" alt="Wholphin Logo" class="rounded-full mb-3 w-25 h-25 object-cover" />
        <h1 class="text-xl font-bold">WHOLPHIN</h1>
        <p class="text-xs text-gray-300">Wholesale Performance Insight</p>

        <!-- Selamat Datang, User -->
        <div class="mt-4 text-sm text-center">
            <p class="text-white font-medium">Selamat datang,</p>
            <p class="text-emerald-400">{{ auth()->user()->name ?? 'User' }}</p>
        </div>
    </div>


    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <!-- Beranda -->
        <a href="/" id="beranda-link"
            class="nav-link flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-emerald-600 transition">
            <i class="fas fa-home mr-3"></i>
            Beranda
        </a>

        <!-- Target Analytics (Dropdown) -->
        <div class="relative group">
            <button onclick="toggleDropdown('target-analytics')"
                class="dropdown-btn flex items-center justify-between w-full px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-700 transition">
                <span class="flex items-center">
                    <i class="fas fa-flag mr-3"></i>
                    Target Analytics
                </span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200" id="target-arrow"></i>
            </button>
            <div id="target-analytics" class="dropdown-content mt-1 space-y-1">
                <div class="overflow-y-auto max-h-60">
                    <a href="/target-analytics/regional-report"
                        class="nav-link block px-8 py-2 text-gray-300 hover:text-emerald-400 hover:bg-slate-700 rounded-md transition">
                        Regional Performance
                    </a>
                    <a href="/target-analytics/product-summary"
                        class="nav-link block px-8 py-2 text-gray-300 hover:text-emerald-400 hover:bg-slate-700 rounded-md transition">
                        Product Summary
                    </a>
                    <a href="/target-analytics/revenue-table"
                        class="nav-link block px-8 py-2 text-gray-300 hover:text-emerald-400 hover:bg-slate-700 rounded-md transition">
                        Target Revenue
                    </a>
                    <a href="/target-analytics/import"
                        class="nav-link block px-8 py-2 text-gray-300 hover:text-emerald-400 hover:bg-slate-700 rounded-md transition">
                        Import Data
                    </a>
                </div>
            </div>
        </div>

        <!-- Revenue Analytics (Dropdown) -->
        <div class="relative group">
            <button onclick="toggleDropdown('revenue-analytics')"
                class="dropdown-btn flex items-center justify-between w-full px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-700 transition">
                <span class="flex items-center">
                    <i class="fas fa-chart-line mr-3"></i>
                    Revenue Analytics
                </span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200" id="revenue-arrow"></i>
            </button>
            <div id="revenue-analytics" class="dropdown-content mt-1 space-y-1">
                <a href="/revenue-analytics/import"
                    class="nav-link block px-8 py-2 text-gray-300 hover:text-emerald-400 hover:bg-slate-700 rounded-md transition">
                    Import Data
                </a>
            </div>
        </div>

        <!-- NCX Status (Dropdown) -->
        <div class="relative group">
            <button onclick="toggleDropdown('ncx-status')"
                class="dropdown-btn flex items-center justify-between w-full px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-700 transition">
                <span class="flex items-center">
                    <i class="fas fa-exchange-alt mr-3"></i>
                    NCX Status
                </span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200" id="ncx-arrow"></i>
            </button>
            <div id="ncx-status" class="dropdown-content mt-1 space-y-1">
                <a href="/ncx/ncx-status"
                    class="nav-link block px-8 py-2 text-gray-300 hover:text-emerald-400 hover:bg-slate-700 rounded-md transition">
                    NCX Status
                </a>
                <a href="/ncx/import"
                    class="nav-link block px-8 py-2 text-gray-300 hover:text-emerald-400 hover:bg-slate-700 rounded-md transition">
                    Import Data
                </a>
            </div>
        </div>

        <!-- Berkas Dokumen -->
        <a href="/documents"
            class="nav-link flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-700 transition">
            <i class="fas fa-file-alt mr-3"></i>
            Berkas Dokumen
        </a>

        <!-- Tools -->
        <a href="/tools"
            class="nav-link flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-700 transition">
            <i class="fas fa-wrench mr-3"></i>
            Tools
        </a>
    </nav>

    <!-- Footer - Logout -->
    <div class="p-4 border-t border-slate-700">
        <form id="logout-form" action="/logout" method="POST" class="hidden">
            <!-- CSRF Token (jika digunakan) -->
            <!-- <input type="hidden" name="_token" value="YOUR_CSRF_TOKEN"> -->
        </form>
        <button class="w-full flex items-center justify-center p-2 rounded-lg bg-slate-700 hover:bg-red-600 transition"
            onclick="handleLogout()">
            <i class="fas fa-sign-out-alt text-gray-300"></i>
            <span class="ml-2 text-gray-300">Logout</span>
        </button>
    </div>
</aside>

<script>
    // Toggle dropdown open/close
    function toggleDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        const arrowId = dropdownId.split('-')[0] + '-arrow';
        const arrow = document.getElementById(arrowId);
        const button = dropdown.previousElementSibling;

        // Tutup semua dropdown lain
        document.querySelectorAll('.dropdown-content').forEach(d => {
            if (d !== dropdown) {
                d.classList.remove('active');
                const id = d.id.split('-')[0];
                const a = document.getElementById(id + '-arrow');
                const b = d.previousElementSibling;
                if (a) a.classList.remove('rotate-180');
                if (b) b.classList.remove('active-dropdown-button');
            }
        });

        // Toggle dropdown saat ini
        const isActive = dropdown.classList.toggle('active');
        if (arrow) arrow.classList.toggle('rotate-180', isActive);
        if (button) button.classList.toggle('active-dropdown-button', isActive);
    }

    // Saat halaman dimuat: deteksi halaman aktif dan buka dropdown jika perlu
    document.addEventListener('DOMContentLoaded', function() {
        // Normalisasi path (hilangkan trailing slash)
        const currentPath = window.location.pathname.replace(/\/$/, "") || "/";

        const links = document.querySelectorAll('a.nav-link');
        let activeLink = null;
        let dropdownToOpen = null;

        links.forEach(link => {
            const href = link.getAttribute('href');
            if (!href) return;

            const normalizedHref = href.replace(/\/$/, "") || "/";

            if (normalizedHref === currentPath) {
                // Tandai link sebagai aktif
                link.classList.add('active');

                // Cek apakah link berada di dalam dropdown
                const parentDropdown = link.closest('.dropdown-content');
                if (parentDropdown) {
                    dropdownToOpen = parentDropdown;
                    link.classList.add('active-dropdown'); // gaya khusus untuk item dropdown aktif
                } else {
                    activeLink = link;
                }
            }
        });

        // Buka dropdown jika diperlukan
        if (dropdownToOpen) {
            const dropdownId = dropdownToOpen.id;
            const parentId = dropdownId.split('-')[0];
            const arrow = document.getElementById(parentId + '-arrow');
            const button = dropdownToOpen.previousElementSibling;

            dropdownToOpen.classList.add('active');
            if (arrow) arrow.classList.add('rotate-180');
            if (button) button.classList.add('active-dropdown-button');
        }

        // Tandai menu utama (non-dropdown) sebagai aktif
        if (activeLink && !activeLink.closest('.dropdown-content')) {
            activeLink.classList.add('active');
        }
    });

    // Fungsi logout dengan konfirmasi
    function handleLogout() {
        if (confirm('Apakah Anda yakin ingin logout?')) {
            // Jika menggunakan form
            // document.getElementById('logout-form').submit();

            // Atau langsung redirect
            window.location.href = '/logout';
        }
    }
</script>
