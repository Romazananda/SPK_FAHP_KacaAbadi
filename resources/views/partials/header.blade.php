<header class="bg-white shadow-md p-4 flex justify-between items-center mb-6 rounded-lg">
    <h1 class="text-2xl font-bold text-indigo-600">Kaca Abadi</h1>

    <nav class="flex gap-4 items-center">
        <a href="{{ url('/clients/dashboard_clients') }}"
            class="font-semibold {{ Request::is('clients/dashboard_clients') ? 'text-indigo-600' : 'text-gray-700 hover:text-indigo-600' }}">
            Beranda
        </a>

        <a href="{{ route('clients.fuzzy') }}"
            class="font-semibold {{ Request::is('clients/fuzzy') ? 'text-indigo-600' : 'text-gray-700 hover:text-indigo-600' }}">
            Fuzzy AHP (Prioritas Kriteria)
        </a>

        <a href="{{ url('/clients/pemilihan') }}"
            class="font-semibold {{ Request::is('clients/pemilihan') ? 'text-indigo-600' : 'text-gray-700 hover:text-indigo-600' }}">
            Pemilihan
        </a>

        <a href="{{ route('clients.subkriteria.form') }}"
            class="font-semibold {{ Request::is('clients/subkriteria/tambah') ? 'text-indigo-600' : 'text-gray-700 hover:text-indigo-600' }}">
            Tambah Subkriteria
        </a>

        {{-- <a href="{{ url('/contact') }}" class="text-gray-700 hover:text-indigo-600 font-semibold">Bantuan</a> --}}

        @if(Auth::check())
        <form action="{{ url('/logout') }}" method="POST" class="inline ml-4">
            @csrf
            <button type="submit" class="text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded-lg font-semibold">
                Logout
            </button>
        </form>
        @endif
    </nav>
</header>
