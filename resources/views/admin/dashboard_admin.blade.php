<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 mb-4">Sistem Pendukung Keputusan</h1>
<p class="text-gray-600 mb-6">Fuzzy AHP: Rekomendasi Pemilihan Kaca dan Cermin</p>

{{-- Statistik --}}
<div class="grid grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow text-center">
        <h2 class="text-xl font-semibold mb-2">Total Alternatif</h2>
        <p class="text-2xl font-bold text-blue-600">{{ $totalAlternatif ?? 0 }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow text-center">
        <h2 class="text-xl font-semibold mb-2">Total Kriteria</h2>
        <p class="text-2xl font-bold text-green-600">{{ $totalKriteria ?? 0 }}</p>
    </div>
</div>

{{-- Chart Section --}}
{{-- <div class="grid grid-cols-2 gap-6"> --}}
    {{-- Bar Chart --}}
    {{-- <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4 text-gray-700">Bobot Kriteria (Bar Chart)</h2>
        <canvas id="barChart"></canvas>
    </div> --}}

    {{-- Pie Chart --}}
    {{-- <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4 text-gray-700">Proporsi Bobot Kriteria (Pie Chart)</h2>
        <canvas id="pieChart"></canvas>
    </div>
</div> --}}

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = @json($labels);
    const dataValues = @json($data);

    // Bar Chart
    // new Chart(document.getElementById('barChart'), {
    //     type: 'bar',
    //     data: {
    //         labels: labels,
    //         datasets: [{
    //             label: 'Bobot Prioritas',
    //             data: dataValues,
    //             borderWidth: 1
    //         }]
    //     },
    //     options: {
    //         scales: {
    //             y: { beginAtZero: true }
    //         }
    //     }
    // });

    // Pie Chart
    // new Chart(document.getElementById('pieChart'), {
    //     type: 'pie',
    //     data: {
    //         labels: labels,
    //         datasets: [{
    //             data: dataValues,
    //         }]
    //     }
    // });
</script>
@endsection
