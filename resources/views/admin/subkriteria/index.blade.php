@extends('layouts.admin')

@section('title', 'Data Subkriteria')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 mb-4">Data Subkriteria</h1>

{{-- ✅ Success Message --}}
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- ⚠️ Error Validation --}}
@if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- 📝 Form Tambah Subkriteria --}}
<div class="bg-white p-6 rounded-lg shadow mb-6">
    <h2 class="text-xl font-semibold mb-4">Tambah Subkriteria Baru</h2>
    <form action="{{ route('admin.subkriteria.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Kriteria --}}
            <div>
                <label for="id_kriteria" class="block text-sm font-medium text-gray-700 mb-1">Pilih Kriteria:</label>
                <select id="id_kriteria" name="id_kriteria"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
                    <option value="">-- Pilih Kriteria --</option>
                    @foreach($kriteria as $item)
                        <option value="{{ $item->id_kriteria }}">{{ $item->nama_kriteria }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Nama Subkriteria --}}
            <div>
                <label for="nama_subkriteria" class="block text-sm font-medium text-gray-700 mb-1">Nama Subkriteria:</label>
                <input type="text" id="nama_subkriteria" name="nama_subkriteria" value="{{ old('nama_subkriteria') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
            </div>

            {{-- Nilai --}}
           <div>
                <label for="nilai" class="block text-sm font-medium text-gray-700 mb-1">Skala AHP (1–9)</label>
                <input type="number" id="nilai" name="nilai" value="{{ old('nilai') }}" min="1" max="9" step="1"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
            </div>

            {{-- Jenis Kaca Saran --}}
            <div>
                <label for="jenis_saran" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kaca Disarankan:</label>
                <select id="jenis_saran" name="jenis_saran"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Tidak Ada / Opsional --</option>
                    <option value="Bening">Bening</option>
                    <option value="Tempered">Tempered</option>
                    <option value="Cermin Biasa">Cermin Biasa</option>
                    <option value="Cermin Bronze">Cermin Bronze</option>
                </select>
            </div>

            {{-- Ketebalan Ideal (min / max) --}}
            <div>
                <label for="min_ketebalan_saran" class="block text-sm font-medium text-gray-700 mb-1">Ketebalan Min (mm):</label>
                <input type="number" id="min_ketebalan_saran" name="min_ketebalan_saran"
                    value="{{ old('min_ketebalan_saran') }}" min="0" step="0.1"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="max_ketebalan_saran" class="block text-sm font-medium text-gray-700 mb-1">Ketebalan Max (mm):</label>
                <input type="number" id="max_ketebalan_saran" name="max_ketebalan_saran"
                    value="{{ old('max_ketebalan_saran') }}" min="0" step="0.1"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <button type="submit"
            class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition">Tambah Subkriteria</button>
    </form>
</div>


{{-- 📋 Tabel Daftar Subkriteria --}}
<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4">Daftar Subkriteria</h2>

    @if($subkriteria->isEmpty())
        <p class="text-gray-500 italic">Belum ada data subkriteria.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 border text-left text-sm font-medium text-gray-900">No</th>
                        <th class="px-6 py-3 border text-left text-sm font-medium text-gray-900">Nama Kriteria</th>
                        <th class="px-6 py-3 border text-left text-sm font-medium text-gray-900">Nama Subkriteria</th>
                        <th class="px-6 py-3 border text-left text-sm font-medium text-gray-900">Nilai</th>
                        <th class="px-6 py-3 border text-left text-sm font-medium text-gray-900">Jenis Kaca</th>
                        <th class="px-6 py-3 border text-left text-sm font-medium text-gray-900">Ketebalan Ideal</th>
                        <th class="px-6 py-3 border text-left text-sm font-medium text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($subkriteria as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 border text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 border text-sm text-gray-900">{{ $item->kriteria->nama_kriteria ?? '-' }}</td>
                        <td class="px-6 py-4 border text-sm text-gray-900">{{ $item->nama_subkriteria }}</td>
                        <td class="px-6 py-4 border text-sm text-gray-900">{{ number_format($item->nilai * 9, 0) }}</td>
                        <td class="px-6 py-4 border text-sm text-gray-900">{{ $item->jenis_saran ?? '-' }}</td>
                        <td class="px-6 py-4 border text-sm text-gray-900">
                            @if($item->min_ketebalan_saran || $item->max_ketebalan_saran)
                                {{ $item->min_ketebalan_saran ?? '?' }} – {{ $item->max_ketebalan_saran ?? '?' }} mm
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 border text-sm text-gray-900">
                            <a href="{{ route('admin.subkriteria.edit', $item->id_subkriteria) }}"
                                class="text-blue-500 hover:text-blue-700 mr-3">Edit</a>
                            <form action="{{ route('admin.subkriteria.destroy', $item->id_subkriteria) }}"
                                method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-500 hover:text-red-700"
                                    onclick="return confirm('Hapus subkriteria ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
