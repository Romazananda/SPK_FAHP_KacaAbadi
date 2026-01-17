@extends('layouts.admin')

@section('title', 'Edit Subkriteria')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 mb-4">Edit Subkriteria</h1>

{{-- ✅ Notifikasi --}}
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

<div class="bg-white p-6 rounded-lg shadow">
    <form action="{{ route('admin.subkriteria.update', $subkriteria->id_subkriteria) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            {{-- Pilih Kriteria --}}
            <div>
                <label for="id_kriteria" class="block text-sm font-medium text-gray-700 mb-1">Pilih Kriteria:</label>
                <select id="id_kriteria" name="id_kriteria"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
                    @foreach($kriteria as $item)
                        <option value="{{ $item->id_kriteria }}" 
                            {{ $item->id_kriteria == $subkriteria->id_kriteria ? 'selected' : '' }}>
                            {{ $item->nama_kriteria }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Nama Subkriteria --}}
            <div>
                <label for="nama_subkriteria" class="block text-sm font-medium text-gray-700 mb-1">Nama Subkriteria:</label>
                <input type="text" id="nama_subkriteria" name="nama_subkriteria"
                    value="{{ old('nama_subkriteria', $subkriteria->nama_subkriteria) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
            </div>

            {{-- Nilai --}}
            <div>
                <label for="nilai" class="block text-sm font-medium text-gray-700 mb-1">Nilai (0–1):</label>
                <input type="number" id="nilai" name="nilai"
                    value="{{ old('nilai', $subkriteria->nilai) }}" min="0" max="1" step="0.01"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
            </div>

            {{-- Jenis Kaca Disarankan --}}
            <div>
                <label for="jenis_saran" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kaca Disarankan:</label>
                <select id="jenis_saran" name="jenis_saran"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Tidak Ada / Opsional --</option>
                    <option value="Bening" {{ old('jenis_saran', $subkriteria->jenis_saran) == 'Bening' ? 'selected' : '' }}>Bening</option>
                    <option value="Tempered" {{ old('jenis_saran', $subkriteria->jenis_saran) == 'Tempered' ? 'selected' : '' }}>Tempered</option>
                    <option value="Cermin Biasa" {{ old('jenis_saran', $subkriteria->jenis_saran) == 'Cermin Biasa' ? 'selected' : '' }}>Cermin Biasa</option>
                    <option value="Cermin Bronze" {{ old('jenis_saran', $subkriteria->jenis_saran) == 'Cermin Bronze' ? 'selected' : '' }}>Cermin Bronze</option>
                </select>
            </div>

            {{-- Ketebalan Min --}}
            <div>
                <label for="min_ketebalan_saran" class="block text-sm font-medium text-gray-700 mb-1">Ketebalan Min (mm):</label>
                <input type="number" id="min_ketebalan_saran" name="min_ketebalan_saran"
                    value="{{ old('min_ketebalan_saran', $subkriteria->min_ketebalan_saran) }}" min="0" step="0.1"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Ketebalan Max --}}
            <div>
                <label for="max_ketebalan_saran" class="block text-sm font-medium text-gray-700 mb-1">Ketebalan Max (mm):</label>
                <input type="number" id="max_ketebalan_saran" name="max_ketebalan_saran"
                    value="{{ old('max_ketebalan_saran', $subkriteria->max_ketebalan_saran) }}" min="0" step="0.1"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="flex items-center mt-6">
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md transition mr-3">Simpan Perubahan</button>
            <a href="{{ route('admin.subkriteria') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition">Batal</a>
        </div>
    </form>
</div>
@endsection
