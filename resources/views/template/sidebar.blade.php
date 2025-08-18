<aside class="w-64 bg-slate-800 text-white h-screen flex flex-col sticky top-0">
    <!-- Logo & Header -->
    <div class="p-6 flex flex-col items-center">
        <img src="https://via.placeholder.com/60x60/3B007F/FFFFFF?text=WP" alt="Wholphin Logo" class="rounded-full mb-3" />
        <h1 class="text-xl font-bold">WHOLPHIN</h1>
        <p class="text-xs text-gray-300">Wholesale Performance Insight</p>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <!-- Beranda -->
        <a href="/"
            class="nav-link flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-emerald-600 transition">
            <i class="fas fa-home mr-3"></i>
            Beranda
        </a>

        <!-- Target Analytics (Dropdown) -->
        <div class="relative">
            <button onclick="toggleDropdown('target-analytics', 'target-arrow')"
                class="flex items-center justify-between w-full px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-700 transition">
                <span class="flex items-center">
                    <i class="fas fa-flag mr-3"></i>
                    Target Analytics
                </span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200" id="target-arrow"></i>
            </button>
            <div id="target-analytics"
                class="mt-1 space-y-1 opacity-0 max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
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
                    Target Revenue Table
                </a>
                <a href="/target-analytics/import"
                    class="nav-link block px-8 py-2 text-gray-300 hover:text-emerald-400 hover:bg-slate-700 rounded-md transition">
                    Import Data
                </a>
            </div>
        </div>

        <!-- Revenue Analytics (Dropdown) -->
        <div class="relative">
            <button onclick="toggleDropdown('revenue-analytics', 'revenue-arrow')"
                class="flex items-center justify-between w-full px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-700 transition">
                <span class="flex items-center">
                    <i class="fas fa-chart-line mr-3"></i>
                    Revenue Analytics
                </span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200" id="revenue-arrow"></i>
            </button>
            <div id="revenue-analytics"
                class="mt-1 space-y-1 opacity-0 max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                <a href="/revenue-analytics/revenue-data"
                    class="nav-link block px-8 py-2 text-gray-300 hover:text-emerald-400 hover:bg-slate-700 rounded-md transition">
                    Revenue Data
                </a>
                <a href="/revenue-analytics/ytd-comparison"
                    class="nav-link block px-8 py-2 text-gray-300 hover:text-emerald-400 hover:bg-slate-700 rounded-md transition">
                    YTD Comparison
                </a>
                <a href="/revenue-analytics/forecast"
                    class="nav-link block px-8 py-2 text-gray-300 hover:text-emerald-400 hover:bg-slate-700 rounded-md transition">
                    Forecast Analysis
                </a>
            </div>
        </div>

        <!-- Sales Analytics -->
        <a href="/sales-analytics"
            class="nav-link flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-700 transition">
            <i class="fas fa-building mr-3"></i>
            Sales Analytics
        </a>

        <!-- NCX Analytics (Dropdown) -->
        <div class="relative">
            <button onclick="toggleDropdown('ncx-status', 'ncx-arrow')"
                class="flex items-center justify-between w-full px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-700 transition">
                <span class="flex items-center">
                    <i class="fas fa-exchange-alt mr-3"></i>
                    NCX Status
                </span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200" id="ncx-arrow"></i>
            </button>
            <div id="ncx-status"
                class="mt-1 space-y-1 opacity-0 max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                <a href="/ncx/data"
                    class="nav-link block px-8 py-2 text-gray-300 hover:text-emerald-400 hover:bg-slate-700 rounded-md transition">
                    NCX Data
                </a>
                <a href="/ncx/performance"
                    class="nav-link block px-8 py-2 text-gray-300 hover:text-emerald-400 hover:bg-slate-700 rounded-md transition">
                    NCX Performance
                </a>
                <a href="/ncx/ncx-status"
                    class="nav-link block px-8 py-2 text-gray-300 hover:text-emerald-400 hover:bg-slate-700 rounded-md transition">
                    NCX Status
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

    <!-- Footer -->
    <div class="p-4 border-t border-slate-700">
        <button
            class="w-full flex items-center justify-center p-2 rounded-full bg-slate-700 hover:bg-slate-600 transition"
            onclick="toggleDarkMode()">
            <i class="fas fa-moon text-gray-300"></i>
        </button>
    </div>
</aside>

<!-- Script: Toggle Dropdown & Set Active Link -->
<script>
    // Toggle Dropdown
    function toggleDropdown(dropdownId, arrowId) {
        const dropdown = document.getElementById(dropdownId);
        const arrow = document.getElementById(arrowId);

        const isOpen = dropdown.classList.contains('opacity-100');

        // Tutup semua dropdown dulu (opsional: agar hanya satu terbuka)
        document.querySelectorAll('.dropdown-content').forEach(d => {
            d.classList.add('opacity-0', 'max-h-0');
            d.classList.remove('opacity-100', 'max-h-96');
        });
        document.querySelectorAll('.fa-chevron-down').forEach(a => a.classList.remove('rotate-180'));

        if (!isOpen) {
            dropdown.classList.remove('opacity-0', 'max-h-0');
            dropdown.classList.add('opacity-100', 'max-h-96');
            arrow?.classList.add('rotate-180');
        }
    }

    // Tandai link aktif berdasarkan URL
    function setActiveLink() {
        const currentPath = window.location.pathname || '/';
        const links = document.querySelectorAll('a.nav-link');

        // Reset semua
        links.forEach(link => {
            link.classList.remove('bg-emerald-500', 'text-white');
            link.classList.add('text-gray-300');
        });

        // Cari link yang cocok
        let matchedLink = null;
        links.forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPath) {
                matchedLink = link;
            }
        });

        // Tandai sebagai aktif
        if (matchedLink) {
            matchedLink.classList.remove('text-gray-300');
            matchedLink.classList.add('bg-emerald-500', 'text-white');

            // Jika berada di dropdown, buka dropdown-nya
            const parentDropdown = matchedLink.closest('.space-y-1');
            if (parentDropdown) {
                const parentId = parentDropdown.id;
                const arrow = document.querySelector(`[onclick*="${parentId}"]`);
                if (arrow) {
                    parentDropdown.classList.remove('opacity-0', 'max-h-0');
                    parentDropdown.classList.add('opacity-100', 'max-h-96');
                    const icon = arrow.querySelector('i.fa-chevron-down');
                    icon?.classList.add('rotate-180');
                }
            }
        }
    }

    // Dark Mode (Contoh sederhana)
    function toggleDarkMode() {
        alert("Dark mode toggle (implementasikan sesuai kebutuhan)");
    }

    // Jalankan saat halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', () => {
        // Beri kelas pada dropdown agar mudah dikenali
        document.querySelectorAll('nav > .relative > div').forEach(div => {
            div.classList.add('dropdown-content');
        });
        setActiveLink();
    });
</script>
