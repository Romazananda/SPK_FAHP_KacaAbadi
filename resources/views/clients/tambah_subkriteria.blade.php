@extends('layouts.app')

@section('title', 'Kelola Subkriteria')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-semibold mb-4 text-indigo-700">Kelola Subkriteria</h2>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    {{-- === FORM TAMBAH / EDIT === --}}
    <form method="POST" action="{{ isset($editData) ? route('clients.subkriteria.update', $editData->id_subkriteria) : route('clients.subkriteria.simpan') }}">
        @csrf
        @if(isset($editData))
            @method('POST')
        @endif

        {{-- Pilih Kriteria --}}
        <div class="mb-4">
            <label class="font-semibold text-sm">Pilih Kriteria</label>
            <select name="id_kriteria" class="w-full border rounded-lg p-2" required>
                <option value="">-- Pilih Kriteria --</option>
                @foreach($kriterias as $k)
                    <option value="{{ $k->id_kriteria }}" {{ isset($editData) && $editData->id_kriteria == $k->id_kriteria ? 'selected' : '' }}>
                        {{ $k->nama_kriteria }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Nama Subkriteria --}}
        <div class="mb-4">
            <label class="font-semibold text-sm">Nama Subkriteria</label>
            <input type="text" name="nama_subkriteria"
                   class="w-full border rounded-lg p-2"
                   placeholder="Masukkan nama subkriteria"
                   value="{{ $editData->nama_subkriteria ?? '' }}" required>
        </div>

        {{-- Nilai --}}
        <div class="mb-4">
            <label class="font-semibold text-sm">Tingkat Kepentingan</label>
            <select name="nilai" class="w-full border rounded-lg p-2" required>
                <option value="">-- Pilih Tingkat Kepentingan --</option>
                <option value="0.1">Sangat Rendah</option>
                <option value="0.3">Rendah</option>
                <option value="0.5">Sedang</option>
                <option value="0.7">Tinggi</option>
                <option value="0.9">Sangat Tinggi</option>
            </select>
        </div>
        {{-- Jenis Kaca --}}
        <div class="mb-4">
            <label class="font-semibold text-sm">Jenis Kaca yang Disarankan</label>
            <input type="text" name="jenis_saran"
                   class="w-full border rounded-lg p-2"
                   value="{{ $editData->jenis_saran ?? '' }}"
                   placeholder="Contoh: Tempered, Bening, Riben">
        </div>

        {{-- Ketebalan --}}
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="font-semibold text-sm">Ketebalan Minimum (mm)</label>
                <input type="number" step="0.1" name="min_ketebalan_saran"
                       class="w-full border rounded-lg p-2"
                       value="{{ $editData->min_ketebalan_saran ?? '' }}">
            </div>
            <div>
                <label class="font-semibold text-sm">Ketebalan Maksimum (mm)</label>
                <input type="number" step="0.1" name="max_ketebalan_saran"
                       class="w-full border rounded-lg p-2"
                       value="{{ $editData->max_ketebalan_saran ?? '' }}">
            </div>
        </div>

        <div class="flex justify-end mt-4">
            @if(isset($editData))
                <a href="{{ route('clients.subkriteria.form') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg shadow mr-2">
                    Batal Edit
                </a>
            @endif
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg shadow">
                {{ isset($editData) ? 'Perbarui Subkriteria' : 'Simpan Subkriteria' }}
            </button>
        </div>
    </form>
</div>

{{-- === DAFTAR SUBKRITERIA === --}}
<div class="bg-white p-6 rounded-lg shadow-md mt-8">
    <h3 class="text-lg font-semibold mb-4 text-gray-800">Daftar Subkriteria</h3>
    <table class="min-w-full border border-gray-300 rounded-lg text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-4 py-2 text-center">No</th>
                <th class="border px-4 py-2">Kriteria</th>
                <th class="border px-4 py-2">Subkriteria</th>
                <th class="border px-4 py-2">Jenis Saran</th>
                <th class="border px-4 py-2 text-center">Status</th>
                <th class="border px-4 py-2 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subkriterias as $i => $s)
                <tr class="hover:bg-gray-50">
                    <td class="border px-4 py-2 text-center">{{ $i + 1 }}</td>
                    <td class="border px-4 py-2">{{ $s->kriteria->nama_kriteria ?? '-' }}</td>
                    <td class="border px-4 py-2">{{ $s->nama_subkriteria }}</td>
                    <td class="border px-4 py-2">{{ $s->jenis_saran ?? '-' }}</td>

                    {{-- STATUS --}}
                    <td class="border px-4 py-2 text-center">
                        @if($s->status == 'approved')
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">Disetujui</span>
                        @elseif($s->status == 'pending')
                            <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-semibold">Menunggu</span>
                        @elseif($s->status == 'rejected')
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-semibold">Ditolak</span>
                        @endif
                    </td>

                    {{-- AKSI --}}
                    <td class="border px-4 py-2 text-center">
                        @if($s->status == 'pending')
                            <a href="{{ route('clients.subkriteria.form', ['edit' => $s->id_subkriteria]) }}" class="text-blue-600 hover:underline">Edit</a> |
                            <form action="{{ route('clients.subkriteria.hapus', $s->id_subkriteria) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin hapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        @else
                            <span class="text-gray-400 italic">Terkunci</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-gray-500">Belum ada subkriteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
