@extends('layouts.admin')

@section('title', 'Edit Preferensi Kriteria')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Edit Preferensi Kriteria</h1>

    <form action="{{ route('admin.preferensi.update', $preferensi->id) }}" 
          method="POST" class="space-y-4 bg-white p-6 shadow-md rounded-lg">
        @csrf
        @method('PUT')

        {{-- Kriteria --}}
        <div>
            <label class="block font-semibold">Kriteria</label>
            <select name="kriteria" class="border rounded w-full p-2" required>
                <option value="Tujuan Penggunaan" 
                    {{ $preferensi->kriteria == 'Tujuan Penggunaan' ? 'selected' : '' }}>Tujuan Penggunaan</option>
                <option value="Lokasi Penempatan" 
                    {{ $preferensi->kriteria == 'Lokasi Penempatan' ? 'selected' : '' }}>Lokasi Penempatan</option>
            </select>
        </div>

        {{-- Tujuan --}}
        <div>
            <label class="block font-semibold">Tujuan</label>
            <input name="tujuan" type="text" 
                   value="{{ old('tujuan', $preferensi->tujuan) }}"
                   class="border rounded w-full p-2">
        </div>

        {{-- Lokasi --}}
        <div>
            <label class="block font-semibold">Lokasi</label>
            <input name="lokasi" type="text" 
                   value="{{ old('lokasi', $preferensi->lokasi) }}"
                   class="border rounded w-full p-2">
        </div>

        {{-- Jenis Kaca --}}
        <div>
            <label class="block font-semibold">Jenis Kaca</label>
            <input name="jenis_kaca" type="text" 
                   value="{{ old('jenis_kaca', $preferensi->jenis_kaca) }}"
                   class="border rounded w-full p-2">
        </div>

        {{-- Finishing --}}
        <div>
            <label class="block font-semibold">Finishing</label>
            <input name="finishing" type="text" 
                   value="{{ old('finishing', $preferensi->finishing) }}"
                   class="border rounded w-full p-2">
        </div>

        {{-- Ketebalan --}}
        <div class="flex space-x-2">
            <div class="flex-1">
                <label class="block font-semibold">Ketebalan Min (mm)</label>
                <input name="ketebalan_min" type="number" step="0.1"
                       value="{{ old('ketebalan_min', $preferensi->ketebalan_min) }}"
                       class="border rounded w-full p-2">
            </div>
            <div class="flex-1">
                <label class="block font-semibold">Ketebalan Max (mm)</label>
                <input name="ketebalan_maks" type="number" step="0.1"
                       value="{{ old('ketebalan_maks', $preferensi->ketebalan_maks) }}"
                       class="border rounded w-full p-2">
            </div>
        </div>

        {{-- Nilai --}}
        <div>
            <label class="block font-semibold">Nilai Kecocokan (1–9)</label>
            <input name="nilai_kecocokan" type="number" min="1" max="9"
                   value="{{ old('nilai_kecocokan', $preferensi->nilai_kecocokan) }}"
                   class="border rounded w-full p-2" required>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('admin.preferensi.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Kembali</a>

            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
