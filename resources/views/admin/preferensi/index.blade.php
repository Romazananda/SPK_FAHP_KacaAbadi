@extends('layouts.admin')

@section('title', 'Daftar Preferensi Kriteria')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Daftar Preferensi Kriteria</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('admin.preferensi.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded mb-3 inline-block">+ Tambah Preferensi</a>

    <div class="overflow-x-auto bg-white shadow-md rounded-lg p-4">
        <table class="min-w-full border border-gray-300 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2">Kriteria</th>
                    <th class="border px-3 py-2">Tujuan</th>
                    <th class="border px-3 py-2">Lokasi</th>
                    <th class="border px-3 py-2">Jenis Kaca</th>
                    <th class="border px-3 py-2">Finishing</th>
                    <th class="border px-3 py-2">Ketebalan (mm)</th>
                    <th class="border px-3 py-2">Nilai</th>
                    <th class="border px-3 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($preferensis as $pref)
                <tr class="hover:bg-gray-50">
                    <td class="border px-3 py-2">{{ $pref->kriteria }}</td>
                    <td class="border px-3 py-2">{{ $pref->tujuan }}</td>
                    <td class="border px-3 py-2">{{ $pref->lokasi }}</td>
                    <td class="border px-3 py-2">{{ $pref->jenis_kaca }}</td>
                    <td class="border px-3 py-2">{{ $pref->finishing }}</td>
                    <td class="border px-3 py-2">{{ $pref->ketebalan_min }} - {{ $pref->ketebalan_maks }}</td>
                    <td class="border px-3 py-2 font-bold text-blue-700">{{ $pref->nilai_kecocokan }}</td>
                    <td class="border px-3 py-2 text-center">
                        <a href="{{ route('admin.preferensi.edit', $pref->id) }}" class="text-yellow-600">Edit</a> |
                        <form action="{{ route('admin.preferensi.destroy', $pref->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button class="text-red-600" onclick="return confirm('Hapus preferensi ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
