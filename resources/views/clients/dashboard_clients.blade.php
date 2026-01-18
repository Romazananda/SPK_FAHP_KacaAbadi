@extends('layouts.app')

@section('title', 'Dashboard SPK Fuzzy AHP')

@section('content')
<div class="min-h-screen p-6 bg-gray-100">

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
        <div class="card bg-green-500 text-white p-6 rounded-xl shadow">
            <h2 class="text-xl font-semibold mb-2">Total Alternatif</h2>
            <p class="text-2xl font-bold text-white">{{ $totalAlternatif ?? 0 }}</p>
        </div>
        <div class="card bg-yellow-500 text-white p-6 rounded-xl shadow">
            <p class="text-xl font-semibold mb-2">Total Kriteria</p>
            <p class="text-2xl font-bold text-white">{{ $totalKriteria ?? 0 }}</p>
        </div>
        <div class="card bg-red-500 text-white p-6 rounded-xl shadow">
            <p class="text-xl font-semibold mb-2">Total Subkriteria</p>
            <p class="text-2xl font-bold text-white">{{ $totalSubkriteria ?? 0 }}</p>
        </div>
    </div>
<div class="mt-8 flex flex-col items-center text-center">
    <h2 class="text-2xl font-semibold text-gray-700 mb-2">Selamat Datang di Sistem SPK Fuzzy AHP</h2>
    <p class="text-gray-500 mb-4">Kelola data dan lihat hasil analisis keputusan Anda di sini</p>

    <img src="{{ asset('images/kaca2.jpg') }}" 
     alt="Dashboard Illustration"
     class="w-full max-w-4xl rounded-xl shadow-md">
</div>
</div>
@endsection

@section('scripts')
<script>
    const ctx = document.getElementById('chartAlternatif')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Skor Alternatif',
                    data: {!! json_encode($data) !!},
                    backgroundColor: 'rgba(59,130,246,0.7)',
                    borderColor: 'rgba(59,130,246,1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, max: 1 } }
            }
        });
    }
</script>
@endsection
