@extends('layouts.admin')

@section('title', 'Tambah Kriteria')

@section('content')
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumb -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Tambah Kriteria</h1>
            <p class="text-gray-600 mt-1">Isi data kriteria baru untuk sistem SPK.</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <form action="{{ route('admin.kriteria.store') }}" method="POST">
                @csrf

                <!-- Nama Kriteria -->
                <div class="mb-6">
                    <label class="block mb-2 font-semibold text-gray-700">Nama Kriteria</label>
                    <input type="text" name="nama_kriteria" value="{{ old('nama_kriteria') }}"
                        class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan nama kriteria" required>
                    @error('nama_kriteria')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tombol -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.kriteria') }}"
                        class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white font-semibold">
                        Simpan
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection
