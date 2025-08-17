
    <!-- Container Flex Utama -->
    <div class="flex flex-wrap items-end gap-4 mb-6">

        <!-- Input Periode -->
        <div class="flex-shrink-0">
            <label for="periode" class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
            <div class="relative">
                <input type="month" id="periode" value="{{ $selectedPeriod }}"
                    class="w-40 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm bg-white cursor-pointer"
                    style="appearance: none; -webkit-appearance: none;" />
                <button type="button"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 cursor-pointer"
                    onclick="document.getElementById('periode').showPicker()">
                    <i class="fas fa-calendar"></i>
                </button>
            </div>
        </div>

        <!-- Filter Regional, Witel, Stream -->
        @foreach ([['label' => 'Regional', 'name' => 'region', 'options' => $filterOptions['regionals'], 'id' => 'dropdown-regional'], ['label' => 'Witel', 'name' => 'witel', 'options' => $filterOptions['witels'], 'id' => 'dropdown-witel'], ['label' => 'Stream', 'name' => 'stream', 'options' => $filterOptions['streams'], 'id' => 'dropdown-stream']] as $filter)
            <div class="relative inline-block text-left flex-shrink-0">
                <h1 class="block text-sm font-medium text-gray-700 mb-1">{{ $filter['label'] }}</h1>

                <button id="{{ $filter['id'] }}-button"
                    class="filter-button flex items-center justify-between w-64 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50"
                    data-dropdown-id="{{ $filter['id'] }}" data-name="{{ $filter['name'] }}">
                    Pilih Opsi
                    <svg class="dropdown-icon w-5 h-5 ml-2 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <div id="{{ $filter['id'] }}"
                    class="dropdown-menu absolute right-0 z-10 w-64 mt-2 bg-white border border-gray-200 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 hidden">
                    <div class="p-3">
                        <!-- Select All & Clear -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <input type="checkbox" class="select-all w-4 h-4 text-blue-600 border-gray-300 rounded">
                                <label class="ml-2 text-sm text-gray-700">Select All</label>
                            </div>
                            <button type="button" class="clear text-xs text-gray-500 hover:text-red-600">Clear</button>
                        </div>

                        <!-- List -->
                        <div class="max-h-48 overflow-y-auto space-y-2 mb-3">
                            @foreach ($filter['options'] as $item)
                                <label class="flex items-center p-2 rounded hover:bg-gray-100 cursor-pointer">
                                    <input type="checkbox" value="{{ $item }}"
                                        class="option-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">{{ $item }}</span>
                                </label>
                            @endforeach
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end gap-2">
                            <button type="button"
                                class="cancel px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200">Cancel</button>
                            <button type="button"
                                class="apply px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Tombol Search dan Reset -->
        <div class="flex-shrink-0 space-x-2">
            <button id="search-btn"
                class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Search
            </button>

            <button id="reset-filters"
                class="px-6 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                Reset Filter
            </button>
        </div>

    </div>

    <!-- Script: Semua JavaScript di dalam DOMContentLoaded -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.filter-button');
            const searchBtn = document.getElementById('search-btn');
            const resetBtn = document.getElementById('reset-filters');
            const periodeInput = document.getElementById('periode');

            // Simpan semua pilihan
            const selections = {
                periode: "{{ $selectedPeriod }}"
            };

            // Inisialisasi selections untuk filter
            @foreach (['region' => 'regionals', 'witel' => 'witels', 'stream' => 'streams'] as $key => $group)
                selections['{{ $key }}'] = [];
            @endforeach

            // ====== LOGIKA DROPDOWN ======
            buttons.forEach(button => {
                const menu = document.getElementById(button.getAttribute('data-dropdown-id'));
                const selectAll = menu.querySelector('.select-all');
                const clearBtn = menu.querySelector('.clear');
                const cancelBtn = menu.querySelector('.cancel');
                const applyBtn = menu.querySelector('.apply');
                const checkboxes = menu.querySelectorAll('.option-checkbox');
                const name = button.getAttribute('data-name');
                const icon = button.querySelector('.dropdown-icon');
                const defaultText = 'Pilih Opsi';

                function updateButtonText() {

                    const selected = Array.from(checkboxes)
                        .filter(cb => cb.checked)
                        .map(cb => cb.value);
                    selections[name] = selected;

                    const count = Array.from(checkboxes).filter(cb => cb.checked).length;

                    button.innerHTML = `
                        ${selected.length ? selected.join(', ') : defaultText}
                        <svg class="dropdown-icon w-5 h-5 ml-2 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    `;

                    if (count === 0) {
                        button.innerHTML = `
                            Pilih Opsi
                            <svg class="dropdown-icon w-5 h-5 ml-2 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        `;
                    } else {
                        button.innerHTML = `
                            ${count} selected
                            <svg class="dropdown-icon w-5 h-5 ml-2 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        `;
                    }
                }

                button.addEventListener('click', (e) => {
                    e.stopPropagation();
                    document.querySelectorAll('.dropdown-menu').forEach(m => m !== menu && m
                        .classList.add('hidden'));
                    menu.classList.toggle('hidden');
                    icon.style.transform = menu.classList.contains('hidden') ? 'rotate(0deg)' :
                        'rotate(180deg)';
                });

                menu.addEventListener('click', e => e.stopPropagation());

                document.addEventListener('click', () => {
                    menu.classList.add('hidden');
                    icon.style.transform = 'rotate(0deg)';
                });

                selectAll.addEventListener('change', () => {
                    checkboxes.forEach(cb => cb.checked = selectAll.checked);
                    updateButtonText();
                });

                checkboxes.forEach(cb => {
                    cb.addEventListener('change', () => {
                        const all = Array.from(checkboxes).every(cb => cb.checked);
                        const some = Array.from(checkboxes).some(cb => cb.checked);
                        selectAll.checked = all;
                        selectAll.indeterminate = some && !all;
                        updateButtonText();
                    });
                });

                clearBtn.addEventListener('click', () => {
                    checkboxes.forEach(cb => cb.checked = false);
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                    updateButtonText();
                });

                cancelBtn.addEventListener('click', () => menu.classList.add('hidden'));
                applyBtn.addEventListener('click', () => menu.classList.add('hidden'));

                updateButtonText();
            });

            // Update nilai periode saat berubah
            periodeInput.addEventListener('change', function() {
                selections.periode = this.value;
            });

            // ====== TOMBOL SEARCH ======
            searchBtn.addEventListener('click', function() {
                const params = new URLSearchParams();

                if (selections.periode) {
                    const [year, month] = selections.periode.split('-');
                    params.append('periode', `${year}${month}`);
                }

                Object.keys(selections).forEach(key => {
                    if (key !== 'periode' && selections[key].length > 0) {
                        params.append(key, selections[key].join(','));
                    }
                });

                window.location.href = '?' + params.toString();
            });

            // ====== TOMBOL RESET FILTER ======
            resetBtn.addEventListener('click', function() {
                // Reset periode
                periodeInput.value = "";
                selections.periode = "";

                // Reset semua dropdown
                buttons.forEach(button => {
                    const menu = document.getElementById(button.getAttribute('data-dropdown-id'));
                    const checkboxes = menu.querySelectorAll('.option-checkbox');
                    const selectAll = menu.querySelector('.select-all');
                    const name = button.getAttribute('data-name');

                    checkboxes.forEach(cb => cb.checked = false);
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                    selections[name] = [];

                    // Kembalikan teks tombol
                    button.innerHTML = `
                        Pilih Opsi
                        <svg class="dropdown-icon w-5 h-5 ml-2 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    `;
                });

                // Opsional: redirect ke halaman tanpa filter
                // window.location.href = window.location.pathname;
            });

            // ====== Update Judul (Opsional) ======
            if ("{{ $selectedPeriod }}" && document.getElementById('report-title')) {
                const [year, month] = "{{ $selectedPeriod }}".split('-');
                const monthNames = [
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];
                const monthName = monthNames[parseInt(month) - 1];
                document.getElementById('report-title').textContent =
                    `Report Revenue – Per Regional (Periode: ${monthName} ${year})`;
            }
        });
    </script>
