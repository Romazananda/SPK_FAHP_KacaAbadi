@extends('layouts.admin')

@section('title', 'Nilai Kecocokan Alternatif – Kriteria')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-4 text-gray-800">
        Nilai Kecocokan Alternatif – Kriteria
    </h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex justify-end mb-4 space-x-2">
        <form action="{{ route('admin.nilai.generate') }}" method="POST">
            @csrf
            <button type="submit"
                class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded">
                Generate Otomatis
            </button>
        </form>

        <form action="{{ route('admin.nilai.store') }}" method="POST">
            @csrf
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Hitung Hasil Rekomendasi
            </button>
        </form>
    </div>

    <div class="overflow-x-auto bg-white shadow-md rounded-lg p-4">
        <table class="min-w-full border border-gray-300 text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="border px-3 py-2 text-center">No</th>
                    <th class="border px-3 py-2 text-left">Alternatif</th>
                    @foreach($kriterias as $k)
                        <th class="border px-3 py-2 text-center">{{ $k->nama_kriteria }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($alternatifs as $index => $alt)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-3 py-2 text-center">{{ $index + 1 }}</td>
                        <td class="border px-3 py-2">{{ $alt->nama }}</td>
                        @foreach($kriterias as $k)
                            @php
                                $nilai = $nilaiKecocokan[$alt->id][$k->id_kriteria] ?? 0;
                            @endphp
                            <td class="border px-3 py-2 text-center font-semibold">
                                {{ number_format($nilai, 2) }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
