@extends('layouts.app')

@section('title', 'Pemilihan Kaca / Cermin')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-semibold mb-4 text-indigo-700">Formulir Pemilihan Kaca / Cermin</h2>

    {{-- Notifikasi --}}
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @elseif(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('clients.hasil') }}">
        @csrf

        <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
            <tbody class="divide-y divide-gray-200">

                {{-- Loop semua kriteria --}}
                @foreach($kriterias as $k)
                    <tr>
                        <td class="p-3 font-semibold bg-gray-50 w-1/3">
                            {{ $k->nama_kriteria }}
                        </td>
                        <td class="p-3">
                            {{-- Khusus ukuran: input manual --}}
                            @if(strtolower($k->nama_kriteria) === 'ukuran')
                                <input type="text" 
                                    name="ukuran_input"
                                    placeholder="Contoh: 1 meter atau 250 cm"
                                    class="w-full border-gray-300 rounded-lg p-2 focus:ring-indigo-500"
                                    required>
                                <p class="text-sm text-gray-500 mt-1">
                                    Masukkan ukuran kaca (misal: 1 meter, 150 cm, atau 2.5 m)
                                </p>

                            {{-- Kriteria lainnya pakai dropdown --}}
                            @else
                                <select name="subkriteria[{{ $k->id_kriteria }}]" 
                                    class="w-full border-gray-300 rounded-lg p-2 focus:ring-indigo-500"
                                    required>
                                    <option value="">-- Pilih {{ $k->nama_kriteria }} --</option>
                                    @foreach($k->subkriteria as $sub)
                                        <option value="{{ $sub->id_subkriteria }}">{{ $sub->nama_subkriteria }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Tombol Submit --}}
        <div class="flex justify-end mt-6">
            <button type="submit"
                class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg shadow">
                Tampilkan Hasil Perankingan
            </button>
        </div>
    </form>
</div>
@endsection
