@extends('layouts.admin')
@section('title', 'Perhitungan Bobot Kriteria (Fuzzy AHP)')

@section('content')
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

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
            <ul class="list-disc pl-6">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ===================================================== --}}
    {{-- FORM 5 PRIORITAS - (versi lebih manusiawi sesuai sketsa dosen) --}}
    {{-- ===================================================== --}}
    <form action="{{ route('admin.kriteria.kuisoner.store') }}" method="POST" class="mb-10 bg-white p-6 rounded-lg shadow">
        @csrf
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">Form Penentuan Prioritas Kriteria</h2>

        <table class="min-w-full border border-gray-300 table-auto rounded-lg shadow-sm">
            <thead class="bg-gray-100 text-gray-800">
                <tr>
                    <th class="px-4 py-2 border text-center w-32">Prioritas</th>
                    <th class="px-4 py-2 border text-center">Kriteria</th>
                    <th class="px-4 py-2 border text-center w-32">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 5; $i++)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border text-center font-semibold">Prioritas {{ $i }}</td>
                    {{-- Combo box kriteria --}}
                    <td class="px-4 py-2 border text-center">
                        <select name="prioritas[{{ $i }}][kriteria]" 
                                class="border rounded px-3 py-2 w-60 focus:ring-2 focus:ring-blue-400" required>
                            <option value="">-- Pilih Kriteria --</option>
                            @foreach($kriterias as $k)
                                <option value="{{ $k->id_kriteria }}">{{ $k->nama_kriteria }}</option>
                            @endforeach
                        </select>
                    </td>
                    {{-- Combo box nilai --}}
                    <td class="px-4 py-2 border text-center">
                        <select name="prioritas[{{ $i }}][nilai]" 
                                class="border rounded px-3 py-2 w-24 text-center focus:ring-2 focus:ring-blue-400" required>
                            <option value="">--</option>
                            @for($n=1;$n<=9;$n++)
                                <option value="{{ $n }}">{{ $n }}</option>
                            @endfor
                        </select>
                    </td>
                </tr>
                @endfor
            </tbody>
        </table>

        <div class="mt-6 text-right">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md shadow">
                OK
            </button>
        </div>
    </form>
    {{-- ======================== --}}
    {{-- TABEL PERBANDINGAN OTOMATIS --}}
    {{-- ======================== --}}
    @if(isset($matrix))
    <div class="mt-10">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">
            Matriks Perbandingan Kriteria (AHP)
        </h2>

        <table class="min-w-full border border-gray-300 table-auto rounded-lg shadow">
            <thead class="bg-gray-100 text-gray-800">
                <tr>
                    <th class="px-4 py-2 border text-center">Kriteria</th>
                    @foreach($kriterias as $k)
                        <th class="px-4 py-2 border text-center ">{{ $k->nama_kriteria }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($kriterias as $i => $krit)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border font-medium bg-gray-50">{{ $krit->nama_kriteria }}</td>

                    @foreach($kriterias as $j => $krit2)
                        <td class="px-4 py-2 border text-center 
                            @if($i == $j)
                            @elseif($i < $j)
                            @else bg-white @endif">
                            @if($i == $j)
                                1
                            @elseif($i < $j)
                                {{ rtrim(rtrim(number_format($matrix[$i][$j], 3, '.', ''), '0'), '.') }}
                            @else
                                {{ rtrim(rtrim(number_format(1 / $matrix[$j][$i], 3, '.', ''), '0'), '.') }}
                            @endif
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif


    {{-- ===================================================== --}}
    {{-- HASIL PERHITUNGAN (dari kode lamamu tetap dipertahankan) --}}
    {{-- ===================================================== --}}
    @if(isset($hasil_cr['normalizedMatrix']))
    <div class="mt-10">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">
            Matriks Normalisasi (Crisp)
        </h2>
        <table class="min-w-full border border-gray-300 table-auto rounded-lg shadow">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border text-center">Kriteria</th>
                    @foreach($kriterias as $k)
                        <th class="px-4 py-2 border text-center">{{ $k->nama_kriteria }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($hasil_cr['normalizedMatrix'] as $i => $row)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border font-medium bg-gray-50">
                        {{ $kriterias[$i]->nama_kriteria }}
                    </td>
                    @foreach($row as $val)
                        <td class="px-4 py-2 border text-center">
                            {{ number_format($val, 4) }}
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>

        <h2 class="text-2xl font-semibold mb-4 text-gray-800 mt-8">
            Bobot Awal (Eigen Vector)
        </h2>
        <table class="min-w-full border border-gray-300 table-auto rounded-lg shadow">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border text-center">Kriteria</th>
                    <th class="px-4 py-2 border text-center">Bobot (W)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hasil_cr['weights'] as $i => $w)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border text-left font-medium">
                        {{ $kriterias[$i]->nama_kriteria }}
                    </td>
                    <td class="px-4 py-2 border text-center">
                        {{ number_format($w, 4) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Hasil Konsistensi & Bobot Fuzzy tetap sama seperti sebelumnya --}}
    {{-- (kode bagian bawah tidak diubah) --}}

    @if(isset($hasil_cr))
    <div class="mt-10">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">
            Hasil Konsistensi (AHP)
        </h2>
        <table class="min-w-full border border-gray-300 table-auto rounded-lg shadow">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border text-center">Kriteria</th>
                    <th class="px-4 py-2 border text-center">Jumlah (A×W)</th>
                    <th class="px-4 py-2 border text-center">Rasio Eigen ((A×W)/W)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kriterias as $index => $krit)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border text-left font-medium">
                        {{ $krit->nama_kriteria }}
                    </td>
                    <td class="px-4 py-2 border text-center">
                        {{ $hasil_cr['weightedSum'][$index] ?? '-' }}
                    </td>
                    <td class="px-4 py-2 border text-center">
                        {{ $hasil_cr['ratio'][$index] ?? '-' }}
                    </td>
                </tr>
                @endforeach
                <tr class="bg-blue-50 font-semibold">
                    <td class="px-4 py-2 border text-right">Rata-rata λ<sub>max</sub></td>
                    <td colspan="2" class="px-4 py-2 border text-center">
                        {{ $hasil_cr['lambdaMax'] }}
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-2 border text-right">CI (Consistency Index)</td>
                    <td colspan="2" class="px-4 py-2 border text-center">
                        {{ $hasil_cr['CI'] }}
                    </td>
                </tr>
                <tr>
                    <td class="px-4 py-2 border text-right">CR (Consistency Ratio)</td>
                    <td colspan="2" class="px-4 py-2 border text-center">
                        {{ $hasil_cr['CR'] }} →
                        <span class="font-bold {{ $hasil_cr['status'] == 'Konsisten' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $hasil_cr['status'] }}
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="mt-3 text-sm text-gray-600">
            <strong>Catatan:</strong> Matriks dianggap <strong>konsisten</strong> jika <code>CR ≤ 0.1</code>.
        </p>
    </div>
    @endif

    @if($bobotKriteria->count() > 0)
    <div class="mt-10">
        <h2 class="text-2xl font-semibold mb-4 text-gray-800">
            Hasil Bobot Fuzzy (Defuzzifikasi & Prioritas)
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
                @foreach($bobotKriteria->sortByDesc('prioritas') as $bobot)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 border text-left font-medium">
                        {{ $bobot->kriteria->nama_kriteria }}
                    </td>
                    <td class="px-4 py-2 border text-center">{{ number_format($bobot->l, 2) }}</td>
                    <td class="px-4 py-2 border text-center">{{ number_format($bobot->m, 2) }}</td>
                    <td class="px-4 py-2 border text-center">{{ number_format($bobot->u, 2) }}</td>
                    <td class="px-4 py-2 border text-center">{{ number_format($bobot->defuzzifikasi, 4) }}</td>
                    <td class="px-4 py-2 border text-center font-semibold">{{ number_format($bobot->prioritas, 4) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
