@extends('layouts.admin')

@section('title', 'Data Kriteria')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Data Kriteria</h1>

    <!-- Tombol tambah kriteria -->
    <div class="mb-4">
        <a href="{{ route('admin.kriteria.create') }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded">
           + Tambah Kriteria
        </a>
    </div>

    <!-- Table Kriteria -->
    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full table-auto border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border">No</th>
                    <th class="px-4 py-2 border">Nama Kriteria</th>
                    <th class="px-4 py-2 border">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kriterias as $index => $kriteria)
                <tr class="text-center border-t">
                    <td class="px-4 py-2 border">{{ $index + 1 }}</td>
                    <td class="px-4 py-2 border">{{ $kriteria->nama_kriteria }}</td>
                    <td class="px-4 py-2 border">
                        <!-- Tombol Edit -->
                        <a href="{{ route('admin.kriteria.edit', $kriteria->id_kriteria) }}" 
                           class="text-yellow-500 hover:text-yellow-700 mr-2">Edit</a>

                        <!-- Tombol Hapus -->
                        <form action="{{ route('admin.kriteria.destroy', $kriteria->id_kriteria) }}" 
                              method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="text-red-500 hover:text-red-700"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus kriteria ini?')">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-2 border text-center text-gray-500">
                        Tidak ada data kriteria
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
