    @extends('layouts.admin')

    @section('title', 'Kelola Subkriteria')

    @section('content')
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold text-indigo-700 mb-4">Daftar Subkriteria</h2>

        {{-- Notifikasi --}}
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
        @endif

        <table class="w-full border border-gray-300 rounded-lg overflow-hidden text-sm">
            <thead class="bg-gray-100">
                <tr class="text-center font-semibold text-gray-700">
                    <th class="border p-2">Kriteria</th>
                    <th class="border p-2">Subkriteria</th>
                    <th class="border p-2">Nilai</th>
                    <th class="border p-2">Jenis Kaca Disarankan</th>
                    <th class="border p-2">Ketebalan (mm)</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Diajukan Oleh</th>
                    <th class="border p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subkriteria as $s)
                    <tr class="text-center hover:bg-gray-50 transition">
                        <td class="border p-2">{{ $s->kriteria->nama_kriteria }}</td>
                        <td class="border p-2 font-medium">{{ $s->nama_subkriteria }}</td>
                        <td class="border p-2">{{ number_format($s->nilai * 9, 0) }}</td>
                        <td class="border p-2">{{ $s->jenis_saran ?? '-' }}</td>
                        <td class="border p-2">
                            {{ $s->min_ketebalan_saran ?? '-' }} - {{ $s->max_ketebalan_saran ?? '-' }}
                        </td>

                        {{-- === BADGE STATUS === --}}
                        <td class="border p-2">
                            @if($s->status === 'approved')
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                                    Disetujui
                                </span>
                            @elseif($s->status === 'pending')
                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold">
                                    Menunggu
                                </span>
                            @elseif($s->status === 'rejected')
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">
                                    Ditolak
                                </span>
                            @endif
                        </td>

                        {{-- Diajukan oleh siapa --}}
                        <td class="border p-2">{{ $s->addedBy?->name ?? '-' }}</td>

                        {{-- Tombol aksi --}}
                        <td class="border p-2 flex justify-center gap-2">
                            @if($s->status === 'pending')
                                <form method="POST" action="{{ route('admin.subkriteria.approve', $s->id_subkriteria) }}">
                                    @csrf
                                    <button type="submit"
                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs">
                                        Setujui
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.subkriteria.reject', $s->id_subkriteria) }}">
                                    @csrf
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">
                                        Tolak
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 text-xs italic">Tidak ada aksi</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-4 text-center text-gray-500">Belum ada data subkriteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endsection
