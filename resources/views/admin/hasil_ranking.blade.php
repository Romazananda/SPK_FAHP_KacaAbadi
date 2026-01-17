@extends('layouts.admin')

@section('title', 'Hasil Perangkingan')

@section('content')
<h1 class="text-2xl font-bold mb-4">Hasil Perangkingan </h1>

<p class="text-sm text-gray-600 mb-4">
    Berikut hasil perhitungan berdasarkan bobot Fuzzy AHP dan nilai penilaian alternatif.
</p>

{{-- Tombol Generate Penilaian --}}
<form action="{{ route('admin.penilaian.generate') }}" method="POST" class="mb-4">
    @csrf
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
        Generate Penilaian untuk Alternatif Baru
    </button>
    <div class="flex justify-end mb-4">
    <a href="{{ route('admin.hasil.pdf') }}" target="_blank"
       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow">
        Cetak Laporan PDF
    </a>
</div>
</form>

{{-- <div class="flex justify-end mb-4">
    <a href="{{ route('admin.hasil.pdf') }}" target="_blank"
       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow">
        Cetak Laporan PDF
    </a>
</div> --}}


{{-- Tabel Hasil Ranking --}}
<div class="overflow-x-auto">
    <table class="min-w-full border border-gray-300 text-sm rounded-lg overflow-hidden shadow-md">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border text-center">Ranking</th>
                <th class="px-4 py-2 border">Nama Produk</th>
                <th class="px-4 py-2 border">Jenis</th>
                <th class="px-4 py-2 border">Ukuran</th>
                <th class="px-4 py-2 border">Ketebalan</th>
                <th class="px-4 py-2 border">Harga (Rp/m²)</th>
                <th class="px-4 py-2 border text-center">Skor Total</th>
                <th class="px-4 py-2 border">Subkriteria Terpilih</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($hasil as $index => $item)
                <tr class="hover:bg-gray-50 {{ $index == 0 ? 'bg-green-100 font-semibold' : '' }}">
                    <td class="border px-4 py-2 text-center">{{ $index + 1 }}</td>
                    <td class="border px-4 py-2">{{ $item['nama_alternatif'] }}</td>
                    <td class="border px-4 py-2">{{ $item['jenis'] }}</td>
                    <td class="border px-4 py-2">{{ $item['ukuran'] }}</td>
                    <td class="border px-4 py-2 text-center">{{ $item['ketebalan'] }} mm</td>
                    <td class="border px-4 py-2 text-right">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                    <td class="border px-4 py-2 text-center text-blue-700 font-bold">
                        {{ number_format($item['skor_total'], 6) }}
                    </td>
                    <td class="border px-4 py-2">
                        <ul class="list-disc list-inside text-gray-700">
                            @forelse (($item['subkriteria_terpilih'] ?? []) as $sub)

                                <li>
                                    <span class="font-medium">{{ $sub['kriteria'] }}:</span>
                                    {{ $sub['subkriteria'] ?? '-' }}
                                </li>
                            @empty
                                <li class="text-gray-400">Tidak ada data subkriteria</li>
                            @endforelse
                        </ul>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-gray-500">
                        Belum ada data hasil perangkingan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
