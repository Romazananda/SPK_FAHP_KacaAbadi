@extends('dashboard.dashboard_clients')

@section('dashboard-content')
<h2 class="text-3xl font-semibold text-gray-800 mb-6">
    Selamat Datang, {{ Auth::user()->name ?? 'Pelanggan' }} 👋
</h2>

<div class="bg-white p-8 rounded-xl shadow text-center">
    <h3 class="text-xl font-semibold text-gray-700 mb-3">Sistem Pendukung Keputusan Pemilihan Kaca</h3>
    <p class="text-gray-600 mb-6">
        Gunakan sistem ini untuk mendapatkan rekomendasi jenis kaca terbaik berdasarkan kebutuhan Anda.
    </p>
    <a href="{{ url('/dashboard/pemilihan') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold">
        Mulai Analisis
    </a>
</div>
@endsection
