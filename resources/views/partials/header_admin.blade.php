<header class="bg-white shadow-md p-4 flex justify-between items-center mb-6 rounded-lg">
    <h1 class="text-2xl font-bold text-indigo-700">Dashboard Admin</h1>
    <nav class="flex gap-4 items-center">
        <a href="{{ route('admin.dashboard_admin') }}" class="text-gray-700 hover:text-indigo-600 font-semibold {{ request()->routeIs('admin.dashboard_admin') ? 'underline' : '' }}">Dashboard</a>
        <a href="{{ route('admin.alternatif_admin') }}" class="text-gray-700 hover:text-indigo-600 font-semibold {{ request()->routeIs('admin.alternatif_admin') ? 'underline' : '' }}">Data Alternatif</a>
        <a href="{{ route('admin.pemilihan') }}" class="text-gray-700 hover:text-indigo-600 font-semibold {{ request()->routeIs('admin.pemilihan') ? 'underline' : '' }}">Pemilihan</a>
        <a href="{{ route('admin.hasil') }}" class="text-gray-700 hover:text-indigo-600 font-semibold {{ request()->routeIs('admin.hasil') ? 'underline' : '' }}">Hasil</a>

        <span class="text-gray-800 font-semibold ml-4">
            👋 Selamat datang, {{ Auth::user()->name }}
        </span>

        <form action="{{ route('logout') }}" method="POST" class="inline ml-4">
            @csrf
            <button type="submit" class="text-white bg-red-500 hover:bg-red-600 px-3 py-1 rounded-lg font-semibold">
                Logout
            </button>
        </form>
    </nav>
</header>
