@extends('layouts.admin')

@section('title', 'Tambah Preferensi Kriteria')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Tambah Preferensi Baru</h1>

    <form action="{{ route('admin.preferensi.store') }}" method="POST" 
          class="space-y-4 bg-white p-6 rounded shadow-md">
        @csrf

        {{-- Kriteria --}}
        <div>
            <label class="block font-semibold">Kriteria</label>
            <select name="kriteria" class="border rounded w-full p-2" required>
                <option value="">-- Pilih Kriteria --</option>
                <option value="Tujuan Penggunaan">Tujuan Penggunaan</option>
                <option value="Lokasi Penempatan">Lokasi Penempatan</option>
            </select>
        </div>

        {{-- Tujuan --}}
        <div>
            <label class="block font-semibold">Tujuan</label>
            <input type="text" name="tujuan" 
                   placeholder="Contoh: Dekoratif, Interior Fungsional, Struktural Berat" 
                   class="border rounded w-full p-2">
        </div>

        {{-- Lokasi --}}
        <div>
            <label class="block font-semibold">Lokasi</label>
            <input type="text" name="lokasi" 
                   placeholder="Contoh: Rumah Tinggal, Kantor, Area Publik" 
                   class="border rounded w-full p-2">
        </div>

        {{-- Jenis Kaca --}}
        <div>
            <label class="block font-semibold">Jenis Kaca</label>
            <input type="text" name="jenis_kaca" 
                   placeholder="Contoh: Bening, Riben, Laminated" 
                   class="border rounded w-full p-2">
        </div>

        {{-- Finishing --}}
        <div>
            <label class="block font-semibold">Finishing</label>
            <input type="text" name="finishing" 
                   placeholder="Contoh: Polos, Tempered (pisahkan dengan koma jika lebih dari satu)" 
                   class="border rounded w-full p-2">
        </div>

        {{-- Ketebalan --}}
        <div class="flex space-x-2">
            <div class="flex-1">
                <label class="block font-semibold">Ketebalan Min (mm)</label>
                <input type="number" name="ketebalan_min" step="0.1" 
                       class="border rounded w-full p-2">
            </div>
            <div class="flex-1">
                <label class="block font-semibold">Ketebalan Max (mm)</label>
                <input type="number" name="ketebalan_maks" step="0.1" 
                       class="border rounded w-full p-2">
            </div>
        </div>

        {{-- Nilai --}}
        <div>
            <label class="block font-semibold">Nilai Kecocokan (1–9)</label>
            <input type="number" name="nilai_kecocokan" min="1" max="9" 
                   class="border rounded w-full p-2" required>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('admin.preferensi.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Kembali</a>

            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                Simpan
            </button>
        </div>
    </form>
</div>
@endsection
