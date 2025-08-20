<aside class="w-64 bg-slate-800 text-white h-screen flex flex-col sticky top-0">
    <style>
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

        .rotate-180 {
            transform: rotate(180deg);
        }
    </style>

    <!-- Logo & Header -->
    <div class="p-6 flex flex-col items-center">
        <img src="/logo.jpg" alt="Wholphin Logo" class="rounded-full mb-3 w-25 h-25 object-cover" />
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
        <div class="relative group">
            <button onclick="toggleDropdown('target-analytics')"
                class="flex items-center justify-between w-full px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-700 transition">
                <span class="flex items-center">
                    <i class="fas fa-flag mr-3"></i>
                    Target Analytics
                </span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200" id="target-arrow"></i>
            </button>
            <div id="target-analytics" class="dropdown-content mt-1 space-y-1">
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
        <div class="relative group">
            <button onclick="toggleDropdown('revenue-analytics')"
                class="flex items-center justify-between w-full px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-700 transition">
                <span class="flex items-center">
                    <i class="fas fa-chart-line mr-3"></i>
                    Revenue Analytics
                </span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200" id="revenue-arrow"></i>
            </button>
            <div id="revenue-analytics" class="dropdown-content mt-1 space-y-1">
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
                <a href="/revenue-analytics/import"
                    class="nav-link block px-8 py-2 text-gray-300 hover:text-emerald-400 hover:bg-slate-700 rounded-md transition">
                    Import Data
                </a>
            </div>
        </div>

        <!-- Sales Analytics -->
        <a href="/sales-analytics"
            class="nav-link flex items-center px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-700 transition">
            <i class="fas fa-building mr-3"></i>
            Sales Analytics
        </a>

        <!-- NCX Status (Dropdown) -->
        <div class="relative group">
            <button onclick="toggleDropdown('ncx-status')"
                class="flex items-center justify-between w-full px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-700 transition">
                <span class="flex items-center">
                    <i class="fas fa-exchange-alt mr-3"></i>
                    NCX Status
                </span>
                <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200" id="ncx-arrow"></i>
            </button>
            <div id="ncx-status" class="dropdown-content mt-1 space-y-1">
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

    <!-- Footer -->
    <div class="p-4 border-t border-slate-700">
        <button
            class="w-full flex items-center justify-center p-2 rounded-full bg-slate-700 hover:bg-slate-600 transition"
            onclick="toggleDarkMode()">
            <i class="fas fa-moon text-gray-300"></i>
        </button>
    </div>
</aside>

<script>
    function toggleDropdown(dropdownId) {
        const dropdown = document.getElementById(dropdownId);
        const arrowId = dropdownId.split('-')[0] + '-arrow';
        const arrow = document.getElementById(arrowId);

        // Tutup semua dropdown
        document.querySelectorAll('.dropdown-content').forEach(d => {
            if (d !== dropdown) {
                d.classList.remove('active');
                const parentId = d.id.split('-')[0];
                const parentArrow = document.getElementById(parentId + '-arrow');
                if (parentArrow) {
                    parentArrow.classList.remove('rotate-180');
                }
            }
        });

        // Toggle dropdown yang diklik
        dropdown.classList.toggle('active');
        arrow.classList.toggle('rotate-180');
    }

    // Auto-open active dropdown on page load
    document.addEventListener('DOMContentLoaded', function() {
        const currentPath = window.location.pathname;
        const links = document.querySelectorAll('a.nav-link');

        links.forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                const parentDropdown = link.closest('.dropdown-content');
                if (parentDropdown) {
                    parentDropdown.classList.add('active');
                    const parentId = parentDropdown.id.split('-')[0];
                    const parentArrow = document.getElementById(parentId + '-arrow');
                    if (parentArrow) {
                        parentArrow.classList.add('rotate-180');
                    }
                }
            }
        });
    });
</script>
