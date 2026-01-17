@extends('layouts.admin')

@section('title', 'Data Alternatif')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 mb-4">Data Alternatif</h1>

{{-- Success Message --}}
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

{{-- Error Messages --}}
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
    {{-- Form Generate Alternatif --}}
    <h2 class="text-xl font-semibold mb-4">Generate Alternatif Baru</h2>
    <p class="text-sm text-gray-600 mb-4">
        Catatan: Harga input adalah base per m² (Rp/m²) berdasarkan ketebalan.
    </p>

    <form id="alternatifForm" action="{{ route('admin.store_alternatif') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="jenis" class="block text-sm font-medium text-gray-700 mb-1">Jenis Kaca:</label>
            <input type="text" id="jenis" name="jenis" value="{{ old('jenis') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>
        </div>

        <div class="mb-4">
            <label for="ukuran" class="block text-sm font-medium text-gray-700 mb-1">Ukuran:</label>
            <input type="text" id="ukuran" name="ukuran" value="{{ old('ukuran') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="91.5x198,122x152.5,122x183" required>
        </div>

        <div class="mb-4">
            <label for="ketebalan" class="block text-sm font-medium text-gray-700 mb-1">Ketebalan:</label>
            <input type="text" id="ketebalan" name="ketebalan" value="{{ old('ketebalan') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="3,5,6" required>
        </div>

        <div class="mb-4">
            <button type="button" onclick="loadStandarUkuran()"
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition mr-2">Load Ukuran Standar</button>
            <button type="button" onclick="generateHargaTable()"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition">Generate Tabel Harga</button>
            <button type="button" onclick="clearHargaTable()"
                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md ml-2 transition">Hapus Tabel</button>
        </div>

        <div id="hargaTableContainer" class="overflow-x-auto mb-4"></div>

        <button type="submit"
            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-md transition">Generate Alternatif</button>
    </form>

    {{-- Tabel Data Alternatif --}}
    <div class="mt-8 bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-2">Tabel Data Alternatif</h2>
        <p class="text-sm text-gray-600 mb-4">Daftar alternatif yang telah tersimpan.</p>

        @if($alternatif->isEmpty())
            <p class="text-gray-500 italic">Belum ada data alternatif. Silakan generate lewat form di atas.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 border text-left text-sm font-medium text-gray-900">No</th>
                            <th class="px-6 py-3 border text-left text-sm font-medium text-gray-900">Nama Produk</th>
                            <th class="px-6 py-3 border text-left text-sm font-medium text-gray-900">Jenis</th>
                            <th class="px-6 py-3 border text-left text-sm font-medium text-gray-900">Ukuran</th>
                            <th class="px-6 py-3 border text-left text-sm font-medium text-gray-900">Ketebalan</th>
                            <th class="px-6 py-3 border text-left text-sm font-medium text-gray-900">Harga</th>
                            <th class="px-6 py-3 border text-center text-sm font-medium text-gray-900">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($alternatif as $index => $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 border text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 border text-sm text-gray-900">{{ $item->nama }}</td>
                            <td class="px-6 py-4 border text-sm text-gray-900">{{ $item->jenis }}</td>
                            <td class="px-6 py-4 border text-sm text-gray-900">{{ $item->ukuran }}</td>
                            <td class="px-6 py-4 border text-sm text-gray-900">{{ $item->ketebalan }} mm</td>
                            <td class="px-6 py-4 border text-sm text-gray-900">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 border text-center">
                                {{-- Tombol Edit (inline modal form) --}}
                                <button type="button" onclick="openEditModal({{ $item->id }}, '{{ $item->nama }}', '{{ $item->jenis }}', '{{ $item->ukuran }}', '{{ $item->ketebalan }}', '{{ $item->harga }}')"
                                    class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded-md text-sm">Edit</button>

                                {{-- Tombol Delete --}}
                                <form action="{{ route('admin.destroy_alternatif', $item->id) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('Yakin ingin menghapus alternatif ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Modal Edit Alternatif --}}
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden justify-center items-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-lg shadow-lg">
        <h2 class="text-xl font-semibold mb-4">Edit Data Alternatif</h2>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nama Produk</label>
                <input type="text" id="edit_nama" name="nama" class="w-full border px-3 py-2 rounded-md" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Jenis Kaca</label>
                <input type="text" id="edit_jenis" name="jenis" class="w-full border px-3 py-2 rounded-md" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Ukuran</label>
                <input type="text" id="edit_ukuran" name="ukuran" class="w-full border px-3 py-2 rounded-md" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Ketebalan (mm)</label>
                <input type="number" id="edit_ketebalan" name="ketebalan" class="w-full border px-3 py-2 rounded-md" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Harga (Rp/m²)</label>
                <input type="number" id="edit_harga" name="harga" class="w-full border px-3 py-2 rounded-md" required>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeEditModal()" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded-md">Batal</button>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function loadStandarUkuran() {
    document.getElementById('ukuran').value = '91.5x198,122x152.5,122x183,132x152.5,101.5x203,244x183,305x213.5,132x183,152x203';
}

function generateHargaTable() {
    const jenis = document.getElementById('jenis').value.trim();
    const ukuranRaw = document.getElementById('ukuran').value.trim();
    const ukurans = ukuranRaw.split(',').map(u => u.trim()).filter(u => u);
    const ketebalan = document.getElementById('ketebalan').value.split(',').map(k => k.trim()).filter(k => k);
    const container = document.getElementById('hargaTableContainer');

    if (!jenis || ukurans.length === 0 || ketebalan.length === 0) {
        alert('Lengkapi semua input dengan variasi yang valid!');
        return;
    }

    const invalidUkurans = [];
    const ukuranValid = ukurans.every((u, index) => {
        const match = u.match(/^\d+(?:\.\d+)?x\d+(?:\.\d+)?$/);
        if (!match) {
            invalidUkurans.push(`"${u}" (posisi ${index + 1})`);
            return false;
        }
        return true;
    });

    if (!ukuranValid) {
        alert(`Format ukuran salah di: ${invalidUkurans.join(', ')}. Contoh: 91.5x198 (gunakan titik untuk desimal, koma untuk pisah ukuran).`);
        return;
    }

    container.innerHTML = '';

    let html = '<table class="min-w-full border border-gray-300 rounded-lg overflow-hidden"><thead class="bg-gray-50"><tr><th class="px-4 py-2 border text-left">Ketebalan</th><th class="px-4 py-2 border text-left">Base Harga per m²</th></tr></thead><tbody>';

    ketebalan.forEach(k => {
        html += `<tr><td class="px-4 py-2 border font-medium">${k} mm</td>
        <td class="px-4 py-2 border"><input type="number" name="harga_input[]" min="0" step="1000" placeholder="Rp/m² untuk ${k}mm"
        class="w-full px-2 py-1 border border-gray-200 rounded text-center" required></td></tr>`;
    });

    html += '</tbody></table>';
    container.innerHTML = html;
}

const alternatifForm = document.getElementById('alternatifForm');
if (alternatifForm) {
    alternatifForm.addEventListener('submit', function(e) {
        const hargaInputs = document.querySelectorAll('input[name="harga_input[]"]');
        if (hargaInputs.length === 0) {
            e.preventDefault();
            alert('Klik "Generate Tabel Harga" dulu sebelum submit!');
        }
    });
}

function clearHargaTable() {
    document.getElementById('hargaTableContainer').innerHTML = '';
}

// ===== Modal Edit =====
function openEditModal(id, nama, jenis, ukuran, ketebalan, harga) {
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editModal').classList.add('flex');
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_jenis').value = jenis;
    document.getElementById('edit_ukuran').value = ukuran;
    document.getElementById('edit_ketebalan').value = ketebalan;
    document.getElementById('edit_harga').value = harga;
    document.getElementById('editForm').action = '/admin/alternatif/' + id;
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editModal').classList.remove('flex');
}
</script>
@endsection
