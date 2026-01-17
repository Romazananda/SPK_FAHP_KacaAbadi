@extends('layouts.app')

@section('title', 'Hasil Rekomendasi Kaca / Cermin')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-semibold mb-4 text-indigo-700">Hasil Perankingan Kaca / Cermin</h2>

    <p class="text-gray-600 text-sm mb-4">
        Berikut hasil perhitungan berdasarkan bobot Fuzzy AHP dan input kriteria yang kamu pilih.
    </p>

    <div class="text-right mb-4">
        <a href="{{ route('clients.hasil.pdf') }}" target="_blank"
           class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md">
           Cetak Laporan PDF
        </a>
    </div>

    {{-- 🧾 RINGKASAN PILIHAN USER --}}
    @if(session('pilihan_user'))
        @php $pilihan = session('pilihan_user'); @endphp

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Ringkasan Pilihan Anda</h3>
            <table class="min-w-full text-sm">
                <tr>
                    <td class="font-medium w-1/3">Tujuan Penggunaan</td>
                    <td>{{ $pilihan['tujuan'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="font-medium">Lokasi Penempatan</td>
                    <td>{{ $pilihan['lokasi'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="font-medium">Ukuran</td>
                    <td>{{ $pilihan['ukuran'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="font-medium">Ketebalan</td>
                    <td>{{ $pilihan['ketebalan'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="font-medium">Pemotongan</td>
                    <td>{{ $pilihan['pemotongan'] ?? '-' }}</td>
                </tr>
            </table>
        </div>
    @endif

    {{-- 📊 Tabel hasil rekomendasi --}}
    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-300 text-sm rounded-lg overflow-hidden shadow">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border text-center">Ranking</th>
                    <th class="px-4 py-2 border">Nama Produk</th>
                    <th class="px-4 py-2 border">Jenis</th>
                    <th class="px-4 py-2 border">Ukuran</th>
                    <th class="px-4 py-2 border">Ketebalan</th>
                    <th class="px-4 py-2 border text-right">Harga (Rp)</th>
                    <th class="px-4 py-2 border text-center">Skor Total</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($hasil as $index => $item)
                    <tr class="hover:bg-gray-50 {{ $index == 0 ? 'bg-green-100 font-semibold' : '' }}">
                        <td class="border px-4 py-2 text-center">{{ $index + 1 }}</td>
                        <td class="border px-4 py-2">{{ $item['nama_alternatif'] ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $item['jenis'] ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $item['ukuran'] ?? '-' }}</td>
                        <td class="border px-4 py-2 text-center">{{ $item['ketebalan'] ?? '-' }}</td>
                        <td class="border px-4 py-2 text-right font-semibold text-green-700">
                            Rp {{ $item['harga_total'] ?? '0' }}
                        </td>
                        <td class="border px-4 py-2 text-center text-blue-700 font-bold">
                            {{ number_format($item['skor_total'] ?? 0, 4) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-gray-500">
                            Tidak ada hasil rekomendasi ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Tombol kembali --}}
    <div class="mt-6 text-right">
        <a href="{{ route('clients.pemilihan') }}"
            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow">
            ← Kembali ke Form Pemilihan
        </a>
    </div>
</div>
@endsection
