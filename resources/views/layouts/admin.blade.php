<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin')</title>
    @vite(['resources/css/app.css','resources/js/app.js'])

    <script>
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            const arrow = document.getElementById(id + '-arrow');
            dropdown.classList.toggle('hidden');
            arrow.classList.toggle('rotate-180');
        }
    </script>
</head>
<body class="flex min-h-screen bg-gray-100">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-800 text-white flex flex-col">
        <div class="p-6 text-center border-b border-gray-700">
            <h1 class="text-2xl font-bold">Dashboard ADMIN</h1>
        </div>

        <nav class="flex-1 p-4 space-y-2">
            <a href="{{ route('admin.dashboard_admin') }}" class="block py-2.5 px-4 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.dashboard_admin') ? 'bg-gray-700' : '' }}">
                Dashboard
            </a>

            <a href="{{ route('admin.alternatif') }}" class="block py-2.5 px-4 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.alternatif') ? 'bg-gray-700' : '' }}">
                Data Alternatif
            </a>

            {{-- Dropdown: Olah Data Kriteria --}}
            <button 
                class="w-full text-left py-2.5 px-4 rounded-lg hover:bg-gray-700 flex justify-between items-center"
                onclick="toggleDropdown('dropdown-kriteria', 'dropdown-kriteria-arrow')"
            >
                Olah Data Kriteria
                <svg id="dropdown-kriteria-arrow" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="dropdown-kriteria" class="hidden pl-4 mt-1 space-y-1">
                <a href="{{ route('admin.kriteria') }}" class="block py-2.5 px-4 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.kriteria') ? 'bg-gray-700' : '' }}">
                    Data Kriteria
                </a>
                <a href="{{ route('admin.subkriteria') }}" class="block py-2.5 px-4 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.subkriteria*') ? 'bg-gray-700 text-white' : '' }}">
                    Data Subkriteria
                </a>
                <a href="{{ route('admin.subkriteria.pending') }}" class="block py-2.5 px-4 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.subkriteria.pending*') ? 'bg-gray-700 text-white' : '' }}">
                    Data Subkriteria Dari User
                </a>
            </div>


            {{-- Dropdown: Olah Perhitungan dan Perankingan --}}
            {{-- <button 
                class="w-full text-left py-2.5 px-4 rounded-lg hover:bg-gray-700 flex justify-between items-center"
                onclick="toggleDropdown('dropdown-fahp', 'dropdown-fahp-arrow')"
            >
                Perangkingan F-AHP
                <svg id="dropdown-fahp-arrow" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div id="dropdown-fahp" class="hidden pl-4 mt-1 space-y-1">
                <a href="{{ route('admin.kriteria.kuisoner') }}" class="block py-2.5 px-4 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.kriteria.kuisoner') ? 'bg-gray-700' : '' }}">
                    Pembobotan Kriteria
                </a>
                <a href="{{ route('admin.ranking') }}" class="block py-2.5 px-4 rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.ranking') ? 'bg-gray-700 text-white' : '' }}">
                    Hasil Perangkingan
                </a>
            </div> --}}

            <div class="mt-4 border-t border-gray-700 pt-2">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left block py-2.5 px-4 rounded-lg hover:bg-gray-700">
                        Logout
                    </button>
                </form>
            </div>
        </nav>

        <div class="p-4 border-t border-gray-700 text-center">
            👋 {{ Auth::user()->name }}
        </div>
    </aside>

    {{-- Konten Utama --}}
    <main class="flex-1 p-8">
        @yield('content')
    </main>

</body>
</html>
