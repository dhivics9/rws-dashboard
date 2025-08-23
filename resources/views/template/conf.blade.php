<!doctype html>
<html>

<head>
    <meta charset="utf-8" />
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

        /* Breadcrumb styling */
        .breadcrumb {
            padding: 0.75rem 1rem;
            background-color: #f8fafc;
            border-radius: 0.375rem;
            margin-bottom: 1.5rem;
        }

        .breadcrumb-list {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .breadcrumb-item {
            display: flex;
            align-items: center;
        }

        .breadcrumb-link {
            color: #4a5568;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
        }

        .breadcrumb-link:hover {
            color: #2d3748;
        }

        .breadcrumb-link.current {
            color: #2d3748;
            font-weight: 500;
        }

        .breadcrumb-separator {
            color: #a0aec0;
            margin: 0 0.5rem;
        }
    </style>
</head>

<body class="bg-gray-50 h-screen flex overflow-hidden">
    @include('template.sidebar')

    <div class="flex-1 flex flex-col overflow-hidden">
        <main class="flex-1 overflow-auto p-6">
            <!-- Breadcrumbs akan muncul di sini -->
            <nav id="breadcrumb-container" class="breadcrumb"></nav>

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

    // Breadcrumb functionality
    document.addEventListener('DOMContentLoaded', function() {
        generateBreadcrumbs();
    });

    function generateBreadcrumbs() {
        const breadcrumbContainer = document.getElementById('breadcrumb-container');
        const pathArray = window.location.pathname.split('/').filter(item => item);

        // Start with home
        let breadcrumbHTML = '<ol class="breadcrumb-list"><li class="breadcrumb-item"><a href="/" class="breadcrumb-link">Home</a></li>';

        // Build intermediate paths
        let currentPath = '';
        for (let i = 0; i < pathArray.length; i++) {
            currentPath += '/' + pathArray[i];

            // Format the breadcrumb text (capitalize, replace dashes with spaces)
            let breadcrumbText = pathArray[i]
                .replace(/-/g, ' ')
                .replace(/\b\w/g, l => l.toUpperCase());

            // Check if it's the last item
            if (i === pathArray.length - 1) {
                breadcrumbHTML += `<li class="breadcrumb-separator">/</li><li class="breadcrumb-item"><span class="breadcrumb-link current">${breadcrumbText}</span></li>`;
            } else {
                breadcrumbHTML += `<li class="breadcrumb-separator">/</li><li class="breadcrumb-item"><a href="${currentPath}" class="breadcrumb-link">${breadcrumbText}</a></li>`;
            }
        }

        breadcrumbHTML += '</ol>';
        breadcrumbContainer.innerHTML = breadcrumbHTML;
    }
</script>

</html>
