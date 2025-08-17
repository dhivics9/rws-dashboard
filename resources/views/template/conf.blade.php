<!doctype html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @vite('resources/css/app.css')
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Wholphin - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.sheetjs.com/xlsx-0.19.3/package/dist/xlsx.full.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
        .bg-dark-blue {
            background-color: #2d3748;
        }

        .bg-light-gray {
            background-color: #f7fafc;
        }

        .text-dark-blue {
            color: #2d3748;
        }

        .border-dark-blue {
            border-color: #2d3748;
        }

        .border-light-gray {
            border-color: #e2e8f0;
        }
    </style>
</head>

<body class="bg-gray-50 h-screen flex overflow-hidden">
    @include('template.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        <main class="flex-1 overflow-auto p-6">
            @yield('content')
        </main>
    </div>
</body>

<script>
    function toggleDropdown(id) {
        const dropdown = document.getElementById(id);
        const arrow = document.getElementById(id + '-arrow');

        // Toggle visibility
        if (dropdown.style.maxHeight === '0px' || dropdown.style.maxHeight === '') {
            dropdown.style.maxHeight = dropdown.scrollHeight + 'px';
            dropdown.style.opacity = '1';
            arrow.classList.remove('fa-chevron-down');
            arrow.classList.add('fa-chevron-up');
        } else {
            dropdown.style.maxHeight = '0px';
            dropdown.style.opacity = '0';
            arrow.classList.remove('fa-chevron-up');
            arrow.classList.add('fa-chevron-down');
        }
    }

    // Set initial state
    document.querySelectorAll('.dropdown').forEach(el => {
        el.style.maxHeight = '0px';
        el.style.opacity = '0';
    });
</script>

</html>
