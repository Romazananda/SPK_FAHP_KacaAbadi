@extends('layouts.app')

@section('title', 'Perhitungan Bobot Kriteria (Fuzzy AHP User)')

@section('content')
@php
    $bobotKriteria = $bobotKriteria ?? collect();
@endphp

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">
        Perhitungan Bobot Kriteria (Fuzzy AHP)
    </h1>

    {{-- ======================== --}}
    {{-- Notifikasi --}}
    {{-- ======================== --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- ======================== --}}
    {{-- FORM PRIORITAS --}}
    {{-- ======================== --}}
    <form id="formPrioritas" class="mb-10 bg-white p-6 rounded-lg shadow">
        @csrf
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">
            Form Penentuan Prioritas Kriteria
        </h2>
        <table class="min-w-full border border-gray-300 table-auto rounded-lg shadow-sm">
            <thead class="bg-gray-100 text-gray-800">
                <tr>
                    <th class="px-4 py-2 border text-center w-32">Prioritas</th>
                    <th class="px-4 py-2 border text-center">Kriteria</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= count($kriterias); $i++)
                <tr>
                    <td class="px-4 py-2 border text-center font-semibold">Prioritas {{ $i }}</td>
                    <td class="px-4 py-2 border text-center">
                        <select name="prioritas[{{ $i }}]" class="kriteria-select border rounded px-3 py-2 w-60 focus:ring-2 focus:ring-blue-400" required>
                            <option value="">-- Pilih Kriteria --</option>
                            @foreach($kriterias as $k)
                                <option value="{{ $k->id_kriteria }}">{{ $k->nama_kriteria }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="mt-6 text-right">
            <button type="button" id="btnGenerate" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md shadow">
                OK
            </button>
        </div>
    </form>

    {{-- ======================== --}}
    {{-- TEMPAT MUNCULNYA TABEL --}}
    {{-- ======================== --}}
    <div id="comparisonContainer" class="hidden mt-10 bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">
            Matriks Perbandingan Kriteria (AHP)
        </h2>
        <div id="tableWrapper"></div>
        <div class="mt-6 text-right">
            <button type="button" id="btnSubmitMatrix" 
                class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md shadow">
                OK
            </button>
        </div>
    </div>

    {{-- ======================== --}}
{{-- TEMPAT HASIL PERHITUNGAN --}}
{{-- ======================== --}}
<div id="hasilContainer" class="mt-10 {{ $step == 3 ? '' : 'hidden' }}">
    @if($bobotKriteria && $bobotKriteria->count() > 0)
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">
            Hasil Perhitungan Fuzzy AHP Sebelumnya
        </h2>


        <table class="min-w-full border border-gray-300 table-auto rounded-lg shadow">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border text-center">Kriteria</th>
                    <th class="px-4 py-2 border text-center">L</th>
                    <th class="px-4 py-2 border text-center">M</th>
                    <th class="px-4 py-2 border text-center">U</th>
                    <th class="px-4 py-2 border text-center">Defuzzifikasi</th>
                    <th class="px-4 py-2 border text-center">Prioritas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bobotKriteria->sortByDesc('prioritas') as $b)
                    <tr>
                        <td class="border px-4 py-2">{{ $b->kriteria->nama_kriteria }}</td>
                        <td class="border px-4 py-2 text-center">{{ number_format($b->l, 2) }}</td>
                        <td class="border px-4 py-2 text-center">{{ number_format($b->m, 2) }}</td>
                        <td class="border px-4 py-2 text-center">{{ number_format($b->u, 2) }}</td>
                        <td class="border px-4 py-2 text-center">{{ number_format($b->defuzzifikasi, 4) }}</td>
                        <td class="border px-4 py-2 text-center font-semibold">{{ number_format($b->prioritas, 4) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-gray-600 text-center">
            Belum ada hasil perhitungan Fuzzy AHP yang tersimpan.
        </p>
    @endif
</div>


</div>

{{-- ======================== --}}
{{-- SCRIPT --}}
{{-- ======================== --}}
<script>
const btnGenerate = document.getElementById('btnGenerate');
const container = document.getElementById('comparisonContainer');
const tableWrapper = document.getElementById('tableWrapper');
const hasilContainer = document.getElementById('hasilContainer');
let selectedIds = [];

// === 1. Generate Matriks Berdasarkan Prioritas ===
btnGenerate.addEventListener('click', async () => {
    const selects = document.querySelectorAll('.kriteria-select');
    const values = Array.from(selects).map(s => s.value).filter(v => v !== "");

    if (values.length < 2) {
        alert('⚠️ Minimal isi dua kriteria!');
        return;
    }
    if (new Set(values).size !== values.length) {
        alert('⚠️ Ada kriteria yang dipilih lebih dari satu kali!');
        return;
    }

    selectedIds = values.map(v => parseInt(v)); // simpan urutan prioritas user

    // Gunakan relative URL agar HTTPS selalu dipakai
    const res = await fetch("/clients/fuzzy/generate-matrix", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({ ids: selectedIds })
    });

    if (!res.ok) {
        console.error("Request gagal:", res.status, res.statusText);
        return alert("Terjadi kesalahan, cek console browser.");
    }

    const data = await res.json();
    tableWrapper.innerHTML = data.html;
    container.classList.remove('hidden');
    hasilContainer.classList.add('hidden'); // sembunyikan hasil lama
    attachMirrorLogic();
});

// === Mirror Input Matrix (otomatis isi kebalikannya) ===
function attachMirrorLogic() {
    document.querySelectorAll('.matrix-input').forEach(input => {
        input.addEventListener('input', function() {
            const i = this.dataset.row;
            const j = this.dataset.col;
            const val = parseFloat(this.value);
            if (isNaN(val) || val <= 0) return;
            const mirror = document.querySelector(`input[data-row="${j}"][data-col="${i}"]`);
            if (mirror) mirror.value = (1 / val).toFixed(3);
        });
    });
}

// === 2. Submit Matriks dan Tampilkan Hasil Lengkap ===
document.addEventListener('click', async (e) => {
    if (e.target && e.target.id === 'btnSubmitMatrix') {
        const inputs = document.querySelectorAll('.matrix-input');
        const matrix = {};

        inputs.forEach(input => {
            const row = input.dataset.row;
            const col = input.dataset.col;
            const val = parseFloat(input.value) || 1;
            if (!matrix[row]) matrix[row] = {};
            matrix[row][col] = val;
        });

        const res = await fetch("/clients/fuzzy/store", { // relative URL
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({
                matrix,
                prioritas: selectedIds
            })
        });

        if (!res.ok) {
            console.error("Request gagal:", res.status, res.statusText);
            return alert("Terjadi kesalahan, cek console browser.");
        }

        const html = await res.text();
        hasilContainer.innerHTML = html;
        hasilContainer.classList.remove('hidden');
        window.scrollTo({ top: hasilContainer.offsetTop, behavior: 'smooth' });
    }
});


</script>
@endsection
