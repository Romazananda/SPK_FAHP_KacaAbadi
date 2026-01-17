<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\KriteriaBobot;
use App\Models\HasilPerhitungan;
use App\Models\PreferensiKriteria;
use App\Models\Subkriteria;
use App\Models\Penilaian;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
    // Menampilkan halaman dashboard admin
    $totalAlternatif = \App\Models\Alternatif::count();
    $totalKriteria = \App\Models\Kriteria::count();

    // Ambil bobot kriteria terbaru (jika ada)
    $bobotKriteria = \App\Models\KriteriaBobot::with('kriteria')->get();

    // Cegah error kalau belum ada data bobot
    if ($bobotKriteria->isEmpty()) {
        $labels = [];
        $data = [];
    } else {
        $labels = $bobotKriteria->pluck('kriteria.nama_kriteria');
        $data = $bobotKriteria->pluck('prioritas');
    }

    return view('admin.dashboard_admin', compact(
        'totalAlternatif',
        'totalKriteria',
        'labels',
        'data'
    ));
    }
    /// Menampilkan daftar kriteria
        public function kriteria()
    {
        $kriterias = Kriteria::all();
        return view('admin.kriteria.kriteria_admin', compact('kriterias'));
    }

    // Form tambah kriteria
    public function kriteriaCreate()
    {
        return view('admin.kriteria.kriteria_create');
    }

    // Simpan kriteria baru
    public function kriteriaStore(Request $request)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|max:255',
        ]);

        Kriteria::create([
            'nama_kriteria' => $request->nama_kriteria,
        ]);

        return redirect()->route('admin.kriteria')
            ->with('success', 'Kriteria berhasil ditambahkan');
    }

    // Form edit kriteria
    public function kriteriaEdit($id_kriteria)
    {
        $kriteria = Kriteria::findOrFail($id_kriteria);
        return view('admin.kriteria.kriteria_edit', compact('kriteria'));
    }

    // Update kriteria
    public function kriteriaUpdate(Request $request, $id_kriteria)
    {
        $request->validate([
            'nama_kriteria' => 'required|string|max:255',
        ]);

        $kriteria = Kriteria::findOrFail($id_kriteria);
        $kriteria->update([
            'nama_kriteria' => $request->nama_kriteria,
        ]);

        return redirect()->route('admin.kriteria')
            ->with('success', 'Kriteria berhasil diperbarui');
    }

    // Hapus kriteria
    public function kriteriaDestroy($id_kriteria)
    {
        $kriteria = Kriteria::findOrFail($id_kriteria);
        $kriteria->delete();

        return redirect()->route('admin.kriteria')
            ->with('success', 'Kriteria berhasil dihapus');
    }

    public function kriteriaKuesionerForm()
    {
        $kriterias = \App\Models\Kriteria::all();
        $bobotKriteria = \App\Models\KriteriaBobot::with('kriteria')->get();
        $hasil_cr = session('hasil_cr');
        return view('admin.kriteria.kuisoner', compact('kriterias','bobotKriteria','hasil_cr'));
    }

    // ===================== SUBKRITERIA CRUD ======================

    // Menampilkan semua subkriteria
    public function subkriteriaIndex()
    {
        $subkriteria = Subkriteria::with('kriteria')->get();
        $kriteria = Kriteria::all();

        return view('admin.subkriteria.index', compact('subkriteria', 'kriteria'));
    }

    // Simpan subkriteria baru
   public function subkriteriaStore(Request $request)
{
    $request->validate([
        'id_kriteria' => 'required|exists:kriterias,id_kriteria',
        'nama_subkriteria' => 'required|string|max:255',
        'nilai' => 'required|numeric|min:1|max:9',
        'jenis_saran' => 'nullable|string|max:50',
        'min_ketebalan_saran' => 'nullable|numeric|min:0',
        'max_ketebalan_saran' => 'nullable|numeric|min:0',
    ]);

    // Ambil semua nilai inputan 1–9 dari subkriteria kriteria yang sama
    $existing = Subkriteria::where('id_kriteria', $request->id_kriteria)->pluck('nilai')->toArray();
    
    // Karena nilai di DB sebelumnya sudah dalam bentuk 0–1, kita perlu ubah sementara ke 1–9 kira-kira.
    // Misal kita anggap nilai terbesar di DB = 9
    if (count($existing) > 0) {
        $maxOld = max($existing);
        $existing = array_map(fn($v) => ($v / $maxOld) * 9, $existing);
    }

    $existing[] = $request->nilai;
    $total = array_sum($existing);

    // Normalisasi & pembulatan
    $normalized = round($request->nilai / $total, 3);

    Subkriteria::create([
        'id_kriteria' => $request->id_kriteria,
        'nama_subkriteria' => $request->nama_subkriteria,
        'nilai' => $normalized, // yang disimpan sudah normalisasi 0–1
        'jenis_saran' => $request->jenis_saran,
        'min_ketebalan_saran' => $request->min_ketebalan_saran,
        'max_ketebalan_saran' => $request->max_ketebalan_saran,
    ]);

    return redirect()->route('admin.subkriteria')->with('success', 'Subkriteria berhasil ditambahkan!');
}

    // Form edit subkriteria
    public function subkriteriaEdit($id)
    {
        $subkriteria = Subkriteria::findOrFail($id);
        $kriteria = Kriteria::all();

        return view('admin.subkriteria.edit', compact('subkriteria', 'kriteria'));
    }

    // Update subkriteria
    public function subkriteriaUpdate(Request $request, $id)
    {
        $request->validate([
            'id_kriteria' => 'required|exists:kriterias,id_kriteria',
            'nama_subkriteria' => 'required|string|max:255',
            'nilai' => 'required|numeric|min:1|max:9',
            'jenis_saran' => 'nullable|string|max:50',
            'min_ketebalan_saran' => 'nullable|numeric|min:0',
            'max_ketebalan_saran' => 'nullable|numeric|min:0',
        ]);

        $subkriteria = Subkriteria::findOrFail($id);

        // Ambil semua nilai dari subkriteria lain di kriteria yang sama
        $existing = Subkriteria::where('id_kriteria', $request->id_kriteria)
            ->where('id_subkriteria', '!=', $id)
            ->pluck('nilai')
            ->toArray();

        // Konversi dulu nilai di DB (0–1) ke kira-kira skala 1–9 biar bisa dihitung normalisasi baru
        if (count($existing) > 0) {
            $maxOld = max($existing);
            $existing = array_map(fn($v) => ($v / $maxOld) * 9, $existing);
        }

        // Tambahkan nilai baru (1–9)
        $existing[] = $request->nilai;

        // Hitung total untuk normalisasi
        $total = array_sum($existing);

        // Normalisasi nilai baru, bulatkan ke 3 desimal
        $normalized = round($request->nilai / $total, 3);

        // Update ke database — yang disimpan hasil normalisasi
        $subkriteria->update([
            'id_kriteria' => $request->id_kriteria,
            'nama_subkriteria' => $request->nama_subkriteria,
            'nilai' => $normalized, // hasil normalisasi 0–1
            'jenis_saran' => $request->jenis_saran,
            'min_ketebalan_saran' => $request->min_ketebalan_saran,
            'max_ketebalan_saran' => $request->max_ketebalan_saran,
        ]);

        return redirect()->route('admin.subkriteria')->with('success', 'Subkriteria berhasil diperbarui dan dinormalisasi!');
    }

    // Hapus subkriteria
    public function subkriteriaDestroy($id)
    {
        $subkriteria = Subkriteria::findOrFail($id);
        $subkriteria->delete();

        return redirect()->route('admin.subkriteria')->with('success', 'Subkriteria berhasil dihapus!');
    }


    // Menampilkan subkriteria yang diajukan oleh user (pending)
    public function subkriteriaPending()
    {
        $subkriteria = \App\Models\Subkriteria::with(['kriteria', 'addedBy'])
        ->whereNotNull('added_by') // hanya subkriteria yang diajukan user
        ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
        ->orderByDesc('created_at')
        ->get();

        return view('admin.subkriteria_pending', compact('subkriteria'));
    }

    public function approveSubkriteria($id)
    {
        $sub = Subkriteria::findOrFail($id);
        $sub->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Subkriteria "' . $sub->nama_subkriteria . '" berhasil disetujui.');
    }

    public function rejectSubkriteria($id)
    {
        $sub = Subkriteria::findOrFail($id);

        // Opsi 1️⃣: hanya ubah status jadi "rejected"
        // $sub->update(['status' => 'rejected']);

        // Opsi 2️⃣: kalau kamu ingin langsung hapus dari database, pakai baris ini:
        $sub->delete();

        return back()->with('error', 'Subkriteria "' . $sub->nama_subkriteria . '" telah ditolak.');
    }


    public function kriteriaKuesionerStore(Request $request)
{
    // === 1️⃣ Ambil semua data kriteria dari DB ===
    $allKriteria = \App\Models\Kriteria::all()->keyBy('id_kriteria');
    $data = [];
    $nilaiPrioritas = [];
    $ids = [];
    $kriterias = collect();

    // === 2️⃣ Jika input berasal dari form prioritas (5 baris combo box)
    if ($request->has('prioritas')) {
        $prioritas = $request->input('prioritas');

        // Ambil urutan kriteria sesuai urutan prioritas user
        $orderedIds = [];
        foreach ($prioritas as $row) {
            if (!isset($row['kriteria']) || !isset($row['nilai'])) continue;
            $orderedIds[] = (int)$row['kriteria'];
            $nilaiPrioritas[(int)$row['kriteria']] = (float)$row['nilai'];
        }

        // Urutkan ulang kriteria sesuai prioritas
        $kriterias = collect($orderedIds)->map(fn($id) => $allKriteria[$id]);
        $ids = $orderedIds;

        // Helper ubah ke skala Saaty 1..9
        $toSaaty = function (float $x): float {
            if ($x <= 0) return 1.0;
            if ($x < 1) {
                $inv = 1 / $x;
                $rounded = (int) round($inv);
                $rounded = max(1, min(9, $rounded));
                return 1 / $rounded;
            } else {
                $rounded = (int) round($x);
                return max(1, min(9, $rounded));
            }
        };

        // Bentuk matriks perbandingan berpasangan (otomatis)
        foreach ($ids as $i => $id1) {
            foreach ($ids as $j => $id2) {
                if ($id1 == $id2) {
                    $data[$id1][$id2] = 1.0;
                } else {
                    $v1 = $nilaiPrioritas[$id1] ?? 1;
                    $v2 = $nilaiPrioritas[$id2] ?? 1;
                    $ratio = $v1 / $v2;
                    $data[$id1][$id2] = $toSaaty($ratio);
                }
            }
        }

    } else {
        // fallback jika format lama (nilai[id1][id2])
        $kriterias = \App\Models\Kriteria::all();
        $ids = $kriterias->pluck('id_kriteria')->toArray();
        $data = $request->input('nilai');
    }

    // === 3️⃣ Validasi input ===
    $n = count($ids);
    $expected = ($n * ($n - 1)) / 2;
    $filled = 0;
    foreach ($ids as $i => $id1) {
        for ($j = $i + 1; $j < $n; $j++) {
            $id2 = $ids[$j];
            if (isset($data[$id1][$id2]) && $data[$id1][$id2] !== '') $filled++;
        }
    }
    if ($filled < $expected) {
        return back()->withErrors(['msg' => 'Mohon isi semua pasangan perbandingan antar kriteria'])->withInput();
    }

    // === 4️⃣ Bangun matriks AHP (Crisp) ===
    $matrix = [];
    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            if ($i == $j) {
                $matrix[$i][$j] = 1.0;
            } elseif ($i < $j) {
                $matrix[$i][$j] = (float)$data[$ids[$i]][$ids[$j]];
            } else {
                $matrix[$i][$j] = 1.0 / (float)$data[$ids[$j]][$ids[$i]];
            }
        }
    }

    // === 5️⃣ Hitung bobot AHP & CR ===
    $weightsCrisp = $this->ahpWeightsByGeometricMean($matrix);
    $crResult = $this->hitungCR($matrix, $weightsCrisp);

    // Jika CR > 0.1 maka hentikan proses
    if ($crResult['CR'] > 0.1) {
        session(['hasil_cr' => $crResult]);
        return redirect()->route('admin.kriteria.kuisoner')
            ->withErrors(['msg' => '❌ Matriks tidak konsisten (CR = ' . $crResult['CR'] . '). Silakan revisi input.']);
    }

    // === 6️⃣ FUZZY AHP ===
    $L = $M = $U = [];
    for ($i = 0; $i < $n; $i++) {
        $L[$i] = $M[$i] = $U[$i] = array_fill(0, $n, 1.0);
    }

    for ($i = 0; $i < $n; $i++) {
        for ($j = $i + 1; $j < $n; $j++) {
            $id_i = $ids[$i];
            $id_j = $ids[$j];
            $skala = (float)$data[$id_i][$id_j];
            [$l, $m, $u] = $this->ahpToFuzzy($skala);
            $L[$i][$j] = $l; $M[$i][$j] = $m; $U[$i][$j] = $u;
            $L[$j][$i] = 1 / $u; $M[$j][$i] = 1 / $m; $U[$j][$i] = 1 / $l;
        }
    }

    // === 7️⃣ Simpan bobot fuzzy ke database ===
    \App\Models\KriteriaBobot::truncate();
    $def = [];
    for ($i = 0; $i < $n; $i++) {
        $avgL = array_sum($L[$i]) / $n;
        $avgM = array_sum($M[$i]) / $n;
        $avgU = array_sum($U[$i]) / $n;
        $defuzz = ($avgL + 4 * $avgM + $avgU) / 6.0;
        $def[$i] = $defuzz;

        \App\Models\KriteriaBobot::create([
            'kriteria_id'   => $ids[$i],
            'l'             => $avgL,
            'm'             => $avgM,
            'u'             => $avgU,
            'defuzzifikasi' => $defuzz,
            'prioritas'     => 0
        ]);
    }

    // === 8️⃣ Normalisasi defuzzifikasi ke prioritas ===
    $sumDef = array_sum($def);
    foreach ($ids as $i => $idKrit) {
        $prioritas = $sumDef > 0 ? $def[$i] / $sumDef : 0;
        \App\Models\KriteriaBobot::where('kriteria_id', $idKrit)
            ->update(['prioritas' => $prioritas]);
    }

    // === 9️⃣ Otomatis hitung ranking alternatif ===
    $this->hitungRanking();

    // === 🔟 Tampilkan hasil ke view ===
    $bobotKriteria = \App\Models\KriteriaBobot::all();

    return view('admin.kriteria.kuisoner', [
        'kriterias' => $kriterias,
        'matrix' => $matrix,
        'weightsCrisp' => $weightsCrisp,
        'hasil_cr' => $crResult,
        'bobotKriteria' => $bobotKriteria
    ])->with('success', '✅ Perhitungan Fuzzy AHP & perangkingan berhasil diperbarui! (CR = ' . $crResult['CR'] . ')');
}


    // ===================================================
    // === FUNGSI-FUNGSI BANTU AHP & FUZZY ===============
    // ===================================================

    private function ahpWeightsByGeometricMean($matrix)
    {
        $n = count($matrix);
        $geo = [];
        for ($i = 0; $i < $n; $i++) {
            $prod = 1.0;
            for ($j = 0; $j < $n; $j++) {
                $prod *= $matrix[$i][$j];
            }
            $geo[$i] = pow($prod, 1 / $n);
        }
        $sum = array_sum($geo);
        return array_map(fn($g) => $g / $sum, $geo);
    }

    private function hitungCR($matrix, $weights)
{
    $n = count($matrix);

    // === 1️⃣ Hitung jumlah kolom untuk normalisasi ===
    $colSums = [];
    for ($j = 0; $j < $n; $j++) {
        $sum = 0;
        for ($i = 0; $i < $n; $i++) {
            $sum += $matrix[$i][$j];
        }
        $colSums[$j] = $sum;
    }

    // === 2️⃣ Matriks normalisasi (Crisp) ===
    $normalizedMatrix = [];
    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            $normalizedMatrix[$i][$j] = $matrix[$i][$j] / $colSums[$j];
        }
    }

    // === 3️⃣ Bobot rata-rata (Eigen Vector) sudah ada di $weights ===

    // === 4️⃣ Hitung A × W (weighted sum) ===
    $weightedSum = [];
    for ($i = 0; $i < $n; $i++) {
        $sum = 0;
        for ($j = 0; $j < $n; $j++) {
            $sum += $matrix[$i][$j] * $weights[$j];
        }
        $weightedSum[$i] = $sum;
    }

    // === 5️⃣ Rasio Eigen (A×W)/W ===
    $ratio = [];
    for ($i = 0; $i < $n; $i++) {
        $ratio[$i] = $weights[$i] > 0 ? $weightedSum[$i] / $weights[$i] : 0;
    }

    // === 6️⃣ Hitung λmax, CI, CR ===
    $lambdaMax = array_sum($ratio) / $n;
    $CI = ($lambdaMax - $n) / ($n - 1);
    $RIValues = [1=>0.00, 2=>0.00, 3=>0.58, 4=>0.90, 5=>1.12, 6=>1.24, 7=>1.32, 8=>1.41, 9=>1.45, 10=>1.49];
    $RI = $RIValues[$n] ?? 1.49;
    $CR = $RI > 0 ? $CI / $RI : 0;

    // === 7️⃣ Return lengkap untuk ditampilkan di view ===
    return [
        'normalizedMatrix' => $normalizedMatrix,
        'weights' => $weights,
        'weightedSum' => array_map(fn($x) => round($x, 5), $weightedSum),
        'ratio' => array_map(fn($x) => round($x, 5), $ratio),
        'lambdaMax' => round($lambdaMax, 5),
        'CI' => round($CI, 5),
        'CR' => round($CR, 5),
        'status' => $CR <= 0.1 ? 'Konsisten' : 'Tidak Konsisten'
    ];
}


    private function ahpToFuzzy($skala)
    {
        switch ($skala) {
            case 1: return [1, 1, 1];
            case 2: return [1, 2, 3];
            case 3: return [2, 3, 4];
            case 4: return [3, 4, 5];
            case 5: return [4, 5, 6];
            case 6: return [5, 6, 7];
            case 7: return [6, 7, 8];
            case 8: return [7, 8, 9];
            case 9: return [8, 9, 9];
            default: return [1, 1, 1];
        }
    }

    // Data Alternatif → tampilkan semua
    public function alternatif()
    {
        $alternatif = Alternatif::all();
        return view('admin.alternatif_admin', compact('alternatif'));
    }

    public function storeAlternatif(Request $request)
    {
        // Pastikan user login
        if (!Auth::check()) {
            dd('User tidak login, session hilang!');
        }

        // Validasi tanpa finishing
        $request->validate([
            'jenis' => 'required|string',
            'ukuran' => 'required|string',
            'ketebalan' => 'required|string',
            'harga_input' => 'required|array',
        ]);

        $jenis = $request->jenis;
        $ukurans = array_map('trim', explode(',', $request->ukuran));
        $ketebalans = array_map('trim', explode(',', $request->ketebalan));
        $hargaInputs = $request->harga_input;

        $index = 0;

        foreach ($ketebalans as $ketebalan) {
            $hargaPerM2 = $hargaInputs[$index] ?? 0;

            foreach ($ukurans as $ukuran) {
                Alternatif::create([
                    'nama' => 'Alternatif ' . (Alternatif::count() + 1),
                    'jenis' => $jenis,
                    'ukuran' => $ukuran,
                    'ketebalan' => $ketebalan,
                    'harga' => $hargaPerM2, // harga per m² saja
                ]);
            }

            $index++;
        }

        // Redirect tetap ke halaman alternatif admin
        return redirect()->route('admin.alternatif')->with('success', 'Alternatif berhasil ditambahkan!');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis' => 'required|string|max:255',
            'ukuran' => 'required|string|max:255',
            'ketebalan' => 'required|numeric',
            'harga' => 'required|numeric|min:0',
        ]);

        $alternatif = Alternatif::findOrFail($id);
        $alternatif->update($request->all());

        return redirect()->route('admin.alternatif')->with('success', 'Data alternatif berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $alternatif = Alternatif::findOrFail($id);
        $alternatif->delete();

        return redirect()->route('admin.alternatif')->with('success', 'Data alternatif berhasil dihapus.');
    }


    public function logout(Request $request)
    {
        Auth::logout(); // Hapus sesi login
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Arahkan ke halaman landing page (misalnya '/')
        return redirect('/')->with('success', 'Anda telah berhasil logout.');
    }
    

    public function hitungRanking()
{
    // === 1️⃣ Ambil data bobot fuzzy terbaru ===
    $bobotKriteria = \App\Models\KriteriaBobot::with('kriteria')->get();
    $alternatifs = \App\Models\Alternatif::all();
    $matrix = [];

    // === 2️⃣ Bangun matriks nilai berdasarkan subkriteria ===
    foreach ($alternatifs as $alt) {
        foreach ($bobotKriteria as $bobot) {
            $k = $bobot->kriteria;
            $fieldName = strtolower(str_replace(' ', '_', $k->nama_kriteria));
            $altValue = $alt->$fieldName ?? null;
            $sub = null;

            switch (strtolower($k->nama_kriteria)) {
                case 'ukuran':
                    $ukuran = floatval(explode('x', $alt->ukuran)[0]);
                    if ($ukuran < 100) {
                        $sub = \App\Models\Subkriteria::where('nama_subkriteria', 'LIKE', '%Kurang dari 1 meter%')->first();
                    } elseif ($ukuran < 200) {
                        $sub = \App\Models\Subkriteria::where('nama_subkriteria', 'LIKE', '%1-2 meter%')->first();
                    } elseif ($ukuran < 300) {
                        $sub = \App\Models\Subkriteria::where('nama_subkriteria', 'LIKE', '%2-3 meter%')->first();
                    } else {
                        $sub = \App\Models\Subkriteria::where('nama_subkriteria', 'LIKE', '%Lebih dari 3 meter%')->first();
                    }
                    break;

                case 'tujuan penggunaan':
                    $sub = \App\Models\Subkriteria::where('id_kriteria', $k->id_kriteria)
                        ->where('nama_subkriteria', 'LIKE', "%{$alt->tujuan_penggunaan}%")->first();
                    break;

                case 'lokasi penempatan':
                    $sub = \App\Models\Subkriteria::where('id_kriteria', $k->id_kriteria)
                        ->where('nama_subkriteria', 'LIKE', "%{$alt->lokasi_penempatan}%")->first();
                    break;

                case 'pemotongan':
                    $sub = \App\Models\Subkriteria::where('id_kriteria', $k->id_kriteria)
                        ->where('nama_subkriteria', 'LIKE', "%{$alt->pemotongan}%")->first();
                    break;

                default:
                    $sub = \App\Models\Subkriteria::where('id_kriteria', $k->id_kriteria)
                        ->where('nama_subkriteria', 'LIKE', "%{$altValue}%")->first();
                    break;
            }

            $nilai = $sub ? $sub->nilai : 0;
            $matrix[$alt->id][$k->id_kriteria] = $nilai;
        }
    }

    // === 3️⃣ Normalisasi (Benefit & Cost) ===
    $normalisasi = [];
    foreach ($bobotKriteria as $bobot) {
        $k = $bobot->kriteria;
        $nilaiKriteria = array_map(fn($alt) => $matrix[$alt->id][$k->id_kriteria] ?? 0, $alternatifs->all());
        $maxValue = max($nilaiKriteria);
        $minValue = min($nilaiKriteria);
        $isCost = strtolower($k->nama_kriteria) == 'harga';

        foreach ($alternatifs as $alt) {
            $nilai = $matrix[$alt->id][$k->id_kriteria] ?? 0;
            $normalisasi[$alt->id][$k->id_kriteria] = $isCost
                ? ($nilai > 0 ? $minValue / $nilai : 0)
                : ($maxValue > 0 ? $nilai / $maxValue : 0);
        }
    }

    // === 4️⃣ Matriks Terbobot ===
    $terbobot = [];
    foreach ($alternatifs as $alt) {
        foreach ($bobotKriteria as $bobot) {
            $k = $bobot->kriteria;
            $terbobot[$alt->id][$k->id_kriteria] =
                ($normalisasi[$alt->id][$k->id_kriteria] ?? 0) * ($bobot->prioritas ?? 0);
        }
    }

    // === 5️⃣ Hitung Nilai Si & Ki ===
    $Si = [];
    foreach ($alternatifs as $alt) {
        $Si[$alt->id] = array_sum($terbobot[$alt->id]);
    }

    $Smax = max($Si);
    $hasil = [];

    foreach ($alternatifs as $alt) {
        $Ki = $Smax > 0 ? $Si[$alt->id] / $Smax : 0;

        $subList = [];
        foreach ($bobotKriteria as $bobot) {
            $k = $bobot->kriteria;
            $fieldName = strtolower(str_replace(' ', '_', $k->nama_kriteria));
            $subList[] = [
                'kriteria' => $k->nama_kriteria,
                'subkriteria' => $alt->$fieldName ?? '-',
            ];
        }

        $hasil[] = [
            'alternatif_id' => $alt->id,
            'nama_alternatif' => $alt->nama,
            'jenis' => $alt->jenis,
            'ukuran' => $alt->ukuran,
            'ketebalan' => $alt->ketebalan,
            'harga' => $alt->harga,
            'skor_total' => round($Ki, 6),
            'subkriteria_terpilih' => $subList,
        ];
    }

    // === 6️⃣ Urutkan & simpan ke DB ===
    usort($hasil, fn($a, $b) => $b['skor_total'] <=> $a['skor_total']);
    \App\Models\HasilPerhitungan::truncate();

    foreach ($hasil as $index => $item) {
        \App\Models\HasilPerhitungan::create([
            'alternatif_id' => $item['alternatif_id'],
            'nilai_total'   => $item['skor_total'],
            'ranking'       => $index + 1,
        ]);
    }

    // === 7️⃣ Simpan hasil ke session agar tampil otomatis ===
    session(['hasil_ranking' => $hasil]);
}


// ===============================
// 📊 TAMPILKAN HASIL PERANGKINGAN
// ===============================
public function hasilRanking()
{
    $hasil = session('hasil_ranking') ??
        \App\Models\HasilPerhitungan::orderBy('ranking')->get();

    $lastUpdated = now()->format('d M Y, H:i');
    return view('admin.hasil_ranking', compact('hasil', 'lastUpdated'))
        ->with('success', '✅ Hasil perangkingan terbaru berhasil dimuat!');
}


// ===============================
// ⚙️ GENERATE PENILAIAN OTOMATIS
// ===============================
public function generatePenilaian()
{
    $alternatifs = \App\Models\Alternatif::all();
    $kriterias = \App\Models\Kriteria::all();

    $counter = 0;

    foreach ($alternatifs as $alt) {
        $sudahAda = \App\Models\Penilaian::where('id_alternatif', $alt->id)->exists();

        if (!$sudahAda) {
            foreach ($kriterias as $krit) {
                $sub = \App\Models\Subkriteria::where('id_kriteria', $krit->id_kriteria)
                    ->inRandomOrder()
                    ->first();

                \App\Models\Penilaian::create([
                    'id_alternatif' => $alt->id,
                    'id_kriteria' => $krit->id_kriteria,
                    'id_subkriteria' => $sub->id_subkriteria,
                    'nilai' => $sub->nilai,
                ]);

                $counter++;
            }
        }
    }

    return redirect()->back()->with('success', "✅ Berhasil menambahkan $counter penilaian baru untuk alternatif!");
}

public function exportHasilAdminPDF()
{
    // Ambil hasil ranking terbaru
    $hasil = \App\Models\HasilPerhitungan::with('alternatif')
        ->orderBy('ranking', 'asc')
        ->get();

    if ($hasil->isEmpty()) {
        return redirect()->route('admin.hasil')
            ->with('error', 'Belum ada hasil perangkingan untuk dicetak.');
    }

    $tanggal = Carbon::now()->translatedFormat('d F Y');

    $pdf = Pdf::loadView('admin.laporan_pdf_admin', compact('hasil', 'tanggal'))
        ->setPaper('a4', 'landscape');

    return $pdf->stream('Laporan_Hasil_Peringkat_Admin.pdf');
}


}
