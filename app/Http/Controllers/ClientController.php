<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Kriteria;
use App\Models\Subkriteria;
use App\Models\Alternatif;
use App\Models\HasilRanking;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


class ClientController extends Controller
{

    public function dashboard()
    {
        // Hitung total data untuk card summary
        $totalAlternatif = \App\Models\Alternatif::count();
        $totalKriteria = \App\Models\Kriteria::count();
        $totalSubkriteria = \App\Models\Subkriteria::where('status', 'approved')->count();

        // Ambil 5 besar hasil ranking terbaru dari tabel hasil_perhitungan
        $hasil = \App\Models\HasilPerhitungan::with('alternatif')
            ->orderBy('ranking', 'asc')
            ->take(5)
            ->get();

        // Siapkan data untuk chart (label = nama alternatif, data = skor)
        $labels = $hasil->pluck('alternatif.nama');
        $data = $hasil->pluck('nilai_total');

        // Kirim ke view
        return view('clients.dashboard_clients', compact(
            'totalAlternatif',
            'totalKriteria',
            'totalSubkriteria',
            'hasil',
            'labels',
            'data'
        ));
    }

    // === TAMPILKAN FORM UNTUK USER ===
    public function showForm()
    {
        $kriterias = Kriteria::with('subkriteria')->get();
        return view('clients.pemilihan', compact('kriterias'));
    }

   // === HITUNG HASIL REKOMENDASI (FINAL FIX) ===
public function hasil(Request $request)
{
    // Ambil input
    $subPilihan = $request->input('subkriteria', []);
    $ukuranInput = $request->input('ukuran_input');

    if (empty($subPilihan) && !$ukuranInput) {
        return back()->with('error', 'Harap isi semua kriteria sebelum melanjutkan!');
    }

    $kriterias = Kriteria::with('subkriteria')->get();
    $bobotUser = session('bobot_user');
    // dd($bobotUser);

    if (!$bobotUser || $bobotUser->isEmpty()) {
        return redirect()->route('clients.fuzzy')
            ->with('error', 'Silakan isi bobot Fuzzy AHP terlebih dahulu.');
    }

    // Simpan pilihan user untuk laporan
    $pilihanUser = [
        'tujuan'     => '-',
        'ukuran'     => $ukuranInput ?: '-',
        'ketebalan'  => '-',
        'lokasi'     => '-',
        'pemotongan' => '-',
        'jenis_disarankan' => '-',
        'rentang_ketebalan_disarankan' => '-',
    ];

    // Normalisasi bobot
    $bobotKriteria = [];
    foreach ($bobotUser as $b) {
        $id = $b->kriteria->id_kriteria;
        $bobotKriteria[$id] = $b->prioritas;
    }
    $sumBobot = array_sum($bobotKriteria);
    foreach ($bobotKriteria as $id => $val) {
        $bobotKriteria[$id] = $sumBobot > 0 ? $val / $sumBobot : 0;
    }

    // bantu cari id kriteria berdasarkan nama
    $findIdByName = fn($name) => optional($kriterias->first(fn($k) => strtolower($k->nama_kriteria) === strtolower($name)))->id_kriteria;

    $idHarga = $findIdByName('harga');
    $idUkuran = $findIdByName('ukuran');

    // === MULAI FILTER ===
    $query = Alternatif::query();

    // ✅ 1️⃣ Ambil subkriteria yang dipilih user
    $tujuanUser = !empty($subPilihan[3]) ? Subkriteria::find($subPilihan[3]) : null;
    $lokasiUser = !empty($subPilihan[6]) ? Subkriteria::find($subPilihan[6]) : null;
    $ketebalanUser = !empty($subPilihan[5]) ? Subkriteria::find($subPilihan[5]) : null;
    $pemotonganUser = !empty($subPilihan[7]) ? Subkriteria::find($subPilihan[7]) : null;

    if ($tujuanUser) $pilihanUser['tujuan'] = $tujuanUser->nama_subkriteria;
    if ($lokasiUser) $pilihanUser['lokasi'] = $lokasiUser->nama_subkriteria;
    if ($pemotonganUser) $pilihanUser['pemotongan'] = $pemotonganUser->nama_subkriteria;

    // ✅ 2️⃣ Tentukan jenis kaca & ketebalan ideal dari DB (bukan hardcode)
    $jenisDisarankan = null;
    $minKetebalanSaran = null;
    $maxKetebalanSaran = null;

    // Lokasi lebih dominan dari tujuan
    if ($lokasiUser && $lokasiUser->jenis_saran) {
        $jenisDisarankan = $lokasiUser->jenis_saran;
        $minKetebalanSaran = $lokasiUser->min_ketebalan_saran;
        $maxKetebalanSaran = $lokasiUser->max_ketebalan_saran;
    } elseif ($tujuanUser && $tujuanUser->jenis_saran) {
        $jenisDisarankan = $tujuanUser->jenis_saran;
        $minKetebalanSaran = $tujuanUser->min_ketebalan_saran;
        $maxKetebalanSaran = $tujuanUser->max_ketebalan_saran;
    }

    if ($jenisDisarankan) {
        $query->where('jenis', 'LIKE', "%{$jenisDisarankan}%");
    }

    $pilihanUser['jenis_disarankan'] = $jenisDisarankan ?? '-';
    $pilihanUser['rentang_ketebalan_disarankan'] = ($minKetebalanSaran && $maxKetebalanSaran)
        ? "{$minKetebalanSaran} – {$maxKetebalanSaran} mm"
        : '-';

    // ✅ 3️⃣ Filter ketebalan (hanya jika user pilih spesifik)
    if ($ketebalanUser && preg_match('/\d+/', $ketebalanUser->nama_subkriteria, $m)) {
        $targetKetebalan = (float)$m[0];
        $pilihanUser['ketebalan'] = $targetKetebalan . ' mm';
        $query->where('ketebalan', '=', $targetKetebalan);
    }

    // Ambil kandidat awal
    $alternatifs = $query->get();

    // ✅ 4️⃣ Filter ukuran manual
    if ($ukuranInput) {
        $pilihanUser['ukuran'] = $ukuranInput;
        preg_match('/\d+(\.\d+)?/', $ukuranInput, $match);
        $angkaMeter = isset($match[0]) ? (float)$match[0] : 0;
        $angkaCm = $angkaMeter * 100;

        $alternatifs = $alternatifs->filter(function($alt) use ($angkaCm) {
            if (!$alt->ukuran) return false;
            if (preg_match('/(\d+\.?\d*)x(\d+\.?\d*)/', $alt->ukuran, $m)) {
                $rata2 = ((float)$m[1] + (float)$m[2]) / 2;
                if ($angkaCm <= 120) return $rata2 <= 170;
                elseif ($angkaCm <= 220) return $rata2 > 170 && $rata2 <= 230;
                else return $rata2 > 230;
            }
            return false;
        })->values();
    }

    if ($alternatifs->isEmpty()) {
        return back()->with('error', 'Tidak ada alternatif yang cocok dengan filter yang dipilih.');
    }

    // === NORMALISASI HARGA ===
    $hargaTotals = $alternatifs->map(function($a){
        $hargaPerM = (float)preg_replace('/[^0-9.]/', '', $a->harga);
        $p = preg_split('/x/i', $a->ukuran ?? '');
        $v = array_map(fn($v)=>(float)preg_replace('/[^0-9.]/','',$v),$p);
        $luas = (count($v)==2)?(($v[0]/100)*($v[1]/100)):1;
        return $hargaPerM*$luas;
    });

    $minHarga = $hargaTotals->min();
    $maxHarga = $hargaTotals->max();

    // === HITUNG SKOR TOTAL ===
    $hasil = [];

    // Ambil semua kriteria (sekali)
    $kriteriasAll = Kriteria::all();

    // Hitung harga_total untuk tiap alternatif kandidat (harga per m * luas)
    $hargaTotals = $alternatifs->map(function($a){
        $hargaPerM = (float) preg_replace('/[^0-9.]/', '', $a->harga);
        $p = preg_split('/x/i', $a->ukuran ?? '');
        $v = array_map(fn($v)=>(float)preg_replace('/[^0-9.]/','',$v), $p);
        $luas = (count($v) == 2) ? (($v[0] / 100.0) * ($v[1] / 100.0)) : 1.0;
        return $hargaPerM * $luas;
    })->values();

    $minHarga = $hargaTotals->min() ?? 0;
    $maxHarga = $hargaTotals->max() ?? 0;

    // Kita perlu juga referensi harga_total per alternatif saat buat hasil akhir.
    // Buat map id -> harga_total agar mudah saat tie-break atau menampilkan
    $hargaMap = [];
    foreach ($alternatifs as $idx => $alt) {
        $hargaPerM = (float) preg_replace('/[^0-9.]/', '', $alt->harga);
        $p = preg_split('/x/i', $alt->ukuran ?? '');
        $v = array_map(fn($v)=>(float)preg_replace('/[^0-9.]/','',$v), $p);
        $luas = (count($v) == 2) ? (($v[0] / 100.0) * ($v[1] / 100.0)) : 1.0;
        $hargaTotal = $hargaPerM * $luas;
        $hargaMap[$alt->id] = $hargaTotal;
    }

    // Loop kandidat: pakai calculateScore deterministik yang sensitif harga + ukuran
    foreach ($alternatifs as $alt) {

        // panggil calculateScore dengan min/max harga & pilihan user
        $skor = $this->calculateScore($alt, $bobotKriteria, $kriteriasAll, $pilihanUser, $minHarga, $maxHarga);

        // format harga dan harga_total (untuk tampilan)
        $hargaPerM = (float) preg_replace('/[^0-9.]/', '', $alt->harga);
        $p = preg_split('/x/i', $alt->ukuran ?? '');
        $v = array_map(fn($v)=>(float)preg_replace('/[^0-9.]/','',$v), $p);
        $luas = (count($v) == 2) ? (($v[0] / 100.0) * ($v[1] / 100.0)) : 1.0;
        $hargaTotal = $hargaPerM * $luas;

        $hasil[] = [
            'id' => $alt->id,
            'nama_alternatif' => $alt->nama,
            'jenis' => $alt->jenis,
            'ukuran' => $alt->ukuran,
            'ketebalan' => $alt->ketebalan,
            'harga' => 'Rp ' . number_format($hargaPerM, 0, ',', '.'),
            'harga_total' => number_format($hargaTotal, 0, ',', '.'),
            'skor_total' => round($skor, 6),
        ];
    }

    // Urutkan utama berdasarkan skor desc; jika sama, tie-breaker: harga_total asc (lebih murah lebih baik)
    usort($hasil, function($a, $b) {
        if (abs($b['skor_total'] - $a['skor_total']) < 0.000001) {
            // ambil numeric harga_total (hilangkan titik/koma)
            $ha = (float) str_replace(',', '', str_replace('.', '', $a['harga_total']));
            $hb = (float) str_replace(',', '', str_replace('.', '', $b['harga_total']));
            return $ha <=> $hb;
        }
        return $b['skor_total'] <=> $a['skor_total'];
    });

    // Batasi top 10 (sama seperti sebelumnya)
    $hasil = array_slice($hasil, 0, 10);

    // Simpan session & tampilkan view
    session(['hasil_user' => $hasil, 'pilihan_user' => $pilihanUser]);

    return view('clients.hasil', compact('hasil', 'ukuranInput'))
        ->with('success', 'Hasil perhitungan berhasil dibuat berdasarkan bobot dan kriteria Anda.');
        }






        // === TAMPILKAN HALAMAN KELOLA SUBKRITERIA (TAMBAH + DAFTAR + EDIT) ===
        public function showTambahSubkriteria(Request $request)
        {
            $kriterias = Kriteria::all();
            $subkriterias = Subkriteria::with('kriteria')
                ->where('added_by', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get();

            $editData = null;
            if ($request->has('edit')) {
                $editData = Subkriteria::where('added_by', Auth::id())
                    ->where('id_subkriteria', $request->edit)
                    ->first();
            }

            return view('clients.tambah_subkriteria', compact('kriterias', 'subkriterias', 'editData'));
        }


    // === TAMBAH SUBKRITERIA BARU ===
    public function tambahSubkriteria(Request $request)
    {
        $request->validate([
            'id_kriteria' => 'required|exists:kriterias,id_kriteria',
            'nama_subkriteria' => 'required|string|max:255',
            'nilai' => 'required|numeric|min:0|max:9',
            'jenis_saran' => 'nullable|string|max:255',
            'min_ketebalan_saran' => 'nullable|numeric|min:0',
            'max_ketebalan_saran' => 'nullable|numeric|min:0',
        ]);

        $status = Auth::user()->role === 'admin' ? 'approved' : 'pending';
        $approved_at = Auth::user()->role === 'admin' ? now() : null;

        Subkriteria::create([
            'id_kriteria' => $request->id_kriteria,
            'nama_subkriteria' => $request->nama_subkriteria,
            'nilai' => $request->nilai,
            'jenis_saran' => $request->jenis_saran,
            'min_ketebalan_saran' => $request->min_ketebalan_saran,
            'max_ketebalan_saran' => $request->max_ketebalan_saran,
            'status' => $status,
            'approved_at' => $approved_at,
            'added_by' => Auth::id(),
        ]);

        return back()->with('success', 'Subkriteria berhasil ditambahkan. Menunggu konfirmasi admin.');
    }

    // === UPDATE SUBKRITERIA ===
    public function updateSubkriteria(Request $request, $id)
    {
        $request->validate([
            'id_kriteria' => 'required|exists:kriterias,id_kriteria',
            'nama_subkriteria' => 'required|string|max:255',
            'nilai' => 'required|numeric|min:0|max:9',
            'jenis_saran' => 'nullable|string|max:255',
            'min_ketebalan_saran' => 'nullable|numeric|min:0',
            'max_ketebalan_saran' => 'nullable|numeric|min:0',
        ]);

        $sub = Subkriteria::findOrFail($id);

        if ($sub->added_by !== Auth::id()) {
            abort(403, 'Tidak diizinkan mengedit data ini.');
        }

        $sub->update([
            'id_kriteria' => $request->id_kriteria,
            'nama_subkriteria' => $request->nama_subkriteria,
            'nilai' => $request->nilai,
            'jenis_saran' => $request->jenis_saran,
            'min_ketebalan_saran' => $request->min_ketebalan_saran,
            'max_ketebalan_saran' => $request->max_ketebalan_saran,
            'status' => 'pending', // reset ke pending kalau diubah
            'approved_at' => null,
        ]);

        return redirect()->route('clients.subkriteria.form')->with('success', 'Subkriteria diperbarui dan menunggu konfirmasi admin.');
    }

    // === HAPUS SUBKRITERIA ===
    public function hapusSubkriteria($id)
    {
        $sub = Subkriteria::findOrFail($id);

        if ($sub->added_by !== Auth::id()) {
            abort(403, 'Anda tidak diizinkan menghapus data ini.');
        }

        $sub->delete();

        return back()->with('success', 'Subkriteria berhasil dihapus.');
    }

    public function generateMatrix(Request $request)
    {
        $ids = $request->input('ids', []);
        $kriterias = \App\Models\Kriteria::whereIn('id_kriteria', $ids)->get()->keyBy('id_kriteria');

        // Validasi minimal dua kriteria
        if (count($ids) < 2) {
            return response()->json(['html' => '<p class="text-red-600">Minimal pilih 2 kriteria.</p>']);
        }

        // Mulai buat tabel HTML-nya
        $html = '<table class="min-w-full border border-gray-300 table-auto rounded-lg shadow">';
        $html .= '<thead class="bg-gray-100"><tr><th class="px-4 py-2 border text-center">Kriteria</th>';

        // Header kolom
        foreach ($ids as $id2) {
            $html .= '<th class="px-4 py-2 border text-center">'.$kriterias[$id2]->nama_kriteria.'</th>';
        }
        $html .= '</tr></thead><tbody>';

        // Baris isi tabel
        foreach ($ids as $i => $id1) {
            $html .= '<tr>';
            $html .= '<td class="px-4 py-2 border font-medium bg-gray-50">'.$kriterias[$id1]->nama_kriteria.'</td>';

            foreach ($ids as $j => $id2) {
                if ($i == $j) {
                    // diagonal = 1
                    $html .= '<td class="px-4 py-2 border text-center bg-gray-100">1</td>';
                } elseif ($i < $j) {
                    // input manual
                    $html .= '<td class="px-4 py-2 border text-center">
                                <input type="text"
                                    inputmode="decimal"
                                    name="matrix['.$i.']['.$j.']"
                                    class="matrix-input border rounded px-2 py-1 w-24 text-center focus:ring-2 focus:ring-blue-400 no-spinner"
                                    data-row="'.$i.'" data-col="'.$j.'"
                                    required>
                            </td>';
                } else {
                    // kolom bawah otomatis kebalikan
                    $html .= '<td class="px-4 py-2 border text-center">
                        <input type="number" readonly
                            name="matrix['.$i.']['.$j.']"
                            class="matrix-input border rounded px-2 py-1 w-24 text-center bg-gray-50"
                            data-row="'.$i.'" data-col="'.$j.'"
                            value="1">
                    </td>';
                }
            }

            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        return response()->json(['html' => $html]);
    }



    public function userFuzzyForm(Request $request)
{
    // Jangan hapus session setiap kali masuk halaman.
    // Hapus hanya kalau user benar-benar baru pertama kali (tidak ada data sama sekali).
    if (!$request->session()->has('matrix_user') 
        && !$request->session()->has('hasil_cr_user') 
        && !$request->session()->has('bobot_user')) {
        session()->forget(['matrix_user', 'hasil_cr_user', 'bobot_user', 'selected_ids_user', 'step_user']);
    }

    $kriterias = \App\Models\Kriteria::all();

    // Ambil hasil lama dari session (kalau ada)
    $bobotKriteria = session('bobot_user', collect());
    $matrix = session('matrix_user', []);
    $hasil_cr = session('hasil_cr_user', []);
    $selectedIds = session('selected_ids_user', []);
    $step = session('step_user', 1); // 1 = prioritas, 2 = matriks, 3 = hasil

    return view('clients.fuzzy_user', compact(
        'kriterias',
        'bobotKriteria',
        'matrix',
        'hasil_cr',
        'selectedIds',
        'step'
    ));
}




public function userKuesionerStore(Request $request)
{
    $matrixInput = $request->input('matrix', []);
    $prioritasInput = $request->input('prioritas', []); // dari form prioritas
    $isAjax = $request->expectsJson();

    if (empty($matrixInput) || count($matrixInput) < 2) {
        if ($isAjax) {
            return response('<p class="text-red-600">Minimal isi dua kriteria!</p>', 400);
        }
        return back()->withErrors(['msg' => 'Minimal isi dua kriteria!'])->withInput();
    }

    // === Ambil kriteria sesuai urutan prioritas ===
    $allKriteria = Kriteria::all()->keyBy('id_kriteria');
    $orderedIds = array_values(array_filter($prioritasInput));
    if (empty($orderedIds)) {
        $orderedIds = $allKriteria->keys()->toArray();
    }

    $kriteriaOrdered = collect($orderedIds)
        ->map(fn($id) => $allKriteria[$id] ?? null)
        ->filter()
        ->values();

    $n = count($matrixInput);

    // === Matriks Simetris ===
    $matrix = [];
    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            if ($i == $j) $matrix[$i][$j] = 1.0;
            elseif (isset($matrixInput[$i][$j]) && is_numeric($matrixInput[$i][$j]))
                $matrix[$i][$j] = (float)$matrixInput[$i][$j];
            elseif (isset($matrixInput[$j][$i]) && is_numeric($matrixInput[$j][$i]))
                $matrix[$i][$j] = 1 / (float)$matrixInput[$j][$i];
            else
                $matrix[$i][$j] = 1.0;
        }
    }

    // === AHP Normalisasi dan Konsistensi ===
    $weights = $this->ahpWeightsByGeometricMean($matrix);
    $hasil_cr = $this->hitungCR($matrix, $weights);
    // CEK KONSISTENSI AHP
    if ($hasil_cr['CR'] > 0.1) {

        // Jika permintaan (user menekan tombol hitung dengan modal)
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Matriks tidak konsisten. Silakan perbaiki nilai perbandingan Anda (CR harus ≤ 0.1).'
            ], 400);
        }

        // Jika bukan (fallback ke halaman)
        return back()->with('error', 'Matriks tidak konsisten. Silakan perbaiki nilai perbandingan Anda (CR harus ≤ 0.1).');
    }

    // === FUZZY AHP ===
    $L = $M = $U = [];
    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            [$l, $m, $u] = $this->ahpToFuzzy($matrix[$i][$j]);
            $L[$i][$j] = $l;
            $M[$i][$j] = $m;
            $U[$i][$j] = $u;
        }
    }

    $def = [];
    for ($i = 0; $i < $n; $i++) {
        $avgL = array_sum($L[$i]) / $n;
        $avgM = array_sum($M[$i]) / $n;
        $avgU = array_sum($U[$i]) / $n;
        $defuzz = ($avgL + 4 * $avgM + $avgU) / 6.0;
        $def[$i] = $defuzz;
    }

    $sumDef = array_sum($def);
    $bobotKriteria = collect();
    foreach (range(0, $n - 1) as $i) {
        $kriteria = $kriteriaOrdered[$i] ?? null;
        if (!$kriteria) continue;

        $bobotKriteria->push((object)[
            'kriteria' => $kriteria,
            'l' => round(array_sum($L[$i]) / $n, 2),
            'm' => round(array_sum($M[$i]) / $n, 2),
            'u' => round(array_sum($U[$i]) / $n, 2),
            'defuzzifikasi' => round($def[$i], 4),
            'prioritas' => $sumDef > 0 ? round($def[$i] / $sumDef, 4) : 0,
        ]);
    }

    // ✅ Simpan ke session (selalu update hasil terbaru)
    session([
        'matrix_user' => $matrix,
        'hasil_cr_user' => $hasil_cr,
        'bobot_user' => $bobotKriteria,
        'selected_ids_user' => $orderedIds,
        'step_user' => 3
    ]);
    session()->save();

    // === KIRIM SEMUA HASIL VIA AJAX ===
    if ($isAjax) {
        $html = '';

        // --- Matriks Normalisasi ---
        $html .= '<div class="mt-10">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Matriks Normalisasi (Crisp)</h2>
            <table class="min-w-full border border-gray-300 table-auto rounded-lg shadow">
                <thead class="bg-gray-100"><tr><th class="px-4 py-2 border text-center">Kriteria</th>';
        foreach ($kriteriaOrdered as $k) {
            $html .= '<th class="px-4 py-2 border text-center">'.$k->nama_kriteria.'</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($hasil_cr['normalizedMatrix'] as $i => $row) {
            $html .= '<tr><td class="px-4 py-2 border font-medium bg-gray-50">'
                .$kriteriaOrdered[$i]->nama_kriteria.'</td>';
            foreach ($row as $val) {
                $html .= '<td class="px-4 py-2 border text-center">'.number_format($val, 4).'</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table></div>';

        // --- Bobot Awal ---
        $html .= '<div class="mt-10"><h2 class="text-2xl font-semibold mb-4 text-gray-800">Bobot Awal (Eigen Vector)</h2>
        <table class="min-w-full border border-gray-300 table-auto rounded-lg shadow">
        <thead class="bg-gray-100"><tr><th class="px-4 py-2 border text-center">Kriteria</th><th class="px-4 py-2 border text-center">Bobot (W)</th></tr></thead><tbody>';
        foreach ($hasil_cr['weights'] as $i => $w) {
            $html .= '<tr><td class="px-4 py-2 border">'.$kriteriaOrdered[$i]->nama_kriteria.'</td>
                      <td class="px-4 py-2 border text-center">'.number_format($w, 4).'</td></tr>';
        }
        $html .= '</tbody></table></div>';

        // --- Konsistensi ---
        $html .= '<div class="mt-10"><h2 class="text-2xl font-semibold mb-4 text-gray-800">Hasil Konsistensi (AHP)</h2>
        <table class="min-w-full border border-gray-300 table-auto rounded-lg shadow">
        <thead class="bg-gray-100"><tr>
        <th class="px-4 py-2 border text-center">Kriteria</th>
        <th class="px-4 py-2 border text-center">Jumlah (A×W)</th>
        <th class="px-4 py-2 border text-center">Rasio Eigen ((A×W)/W)</th></tr></thead><tbody>';
        foreach ($hasil_cr['ratio'] as $i => $r) {
            $html .= '<tr><td class="px-4 py-2 border">'.$kriteriaOrdered[$i]->nama_kriteria.'</td>
                      <td class="px-4 py-2 border text-center">'.number_format($hasil_cr['weightedSum'][$i], 4).'</td>
                      <td class="px-4 py-2 border text-center">'.number_format($r, 4).'</td></tr>';
        }
        $html .= '<tr class="bg-blue-50 font-semibold">
                    <td class="px-4 py-2 border text-right">Rata-rata λ<sub>max</sub></td>
                    <td colspan="2" class="px-4 py-2 border text-center">'.number_format($hasil_cr['lambdaMax'],5).'</td>
                  </tr>
                  <tr><td class="px-4 py-2 border text-right">CI (Consistency Index)</td>
                    <td colspan="2" class="px-4 py-2 border text-center">'.number_format($hasil_cr['CI'],5).'</td></tr>
                  <tr><td class="px-4 py-2 border text-right">CR (Consistency Ratio)</td>
                    <td colspan="2" class="px-4 py-2 border text-center">'.number_format($hasil_cr['CR'],5).' → 
                    <span class="font-bold '.($hasil_cr['status']=='Konsisten'?'text-green-600':'text-red-600').'">'.$hasil_cr['status'].'</span></td></tr>
                  </tbody></table></div>';

        // --- FUZZY AHP ---
        $html .= '<div class="mt-10"><h2 class="text-2xl font-semibold mb-4 text-gray-800">Hasil Bobot Fuzzy (Defuzzifikasi & Prioritas)</h2>
        <table class="min-w-full border border-gray-300 table-auto rounded-lg shadow">
        <thead class="bg-gray-100"><tr>
        <th class="px-4 py-2 border text-center">Kriteria</th>
        <th class="px-4 py-2 border text-center">L</th>
        <th class="px-4 py-2 border text-center">M</th>
        <th class="px-4 py-2 border text-center">U</th>
        <th class="px-4 py-2 border text-center">Defuzzifikasi</th>
        <th class="px-4 py-2 border text-center">Prioritas</th>
        </tr></thead><tbody>';
        foreach ($bobotKriteria->sortByDesc('prioritas') as $b) {
            $html .= '<tr>
                <td class="border px-4 py-2">'.$b->kriteria->nama_kriteria.'</td>
                <td class="border px-4 py-2 text-center">'.number_format($b->l, 2).'</td>
                <td class="border px-4 py-2 text-center">'.number_format($b->m, 2).'</td>
                <td class="border px-4 py-2 text-center">'.number_format($b->u, 2).'</td>
                <td class="border px-4 py-2 text-center">'.number_format($b->defuzzifikasi, 4).'</td>
                <td class="border px-4 py-2 text-center font-semibold">'.number_format($b->prioritas, 4).'</td>
            </tr>';
        }
        $html .= '</tbody></table></div>';

        return response($html);
    }

    return redirect()->route('clients.fuzzy')->with('success', '✅ Perhitungan berhasil dilakukan.');
}

    private function ahpToFuzzy($x)
{
    $x = floatval($x);

    // Jika bilangan lebih besar atau sama dengan 1 → langsung pakai skala fuzzy
    if ($x >= 1) {
        switch (round($x)) {
            case 1: return [1, 1, 1];
            case 2: return [1, 2, 3];
            case 3: return [2, 3, 4];
            case 4: return [3, 4, 5];
            case 5: return [4, 5, 6];
            case 6: return [5, 6, 7];
            case 7: return [6, 7, 8];
            case 8: return [7, 8, 9];
            case 9: return [9, 9, 9];
        }
    }

    // Jika bilangan < 1 → fuzzy inverse
    $reciprocal = 1 / $x;
    [$l, $m, $u] = $this->ahpToFuzzy($reciprocal);

    return [
        1 / $u,
        1 / $m,
        1 / $l
    ];
}

// === HELPER: parse harga jadi number
private function parseHargaToNumber($harga)
{
    return (float) preg_replace('/[^0-9.]/', '', $harga);
}

// === HELPER: rata-rata ukuran (cm) dari string "122x152.5"
private function avgSizeCm(Alternatif $alt): float
{
    if (! $alt->ukuran) return 0;
    if (preg_match('/(\d+(\.\d+)?)\s*[xX]\s*(\d+(\.\d+)?)/', $alt->ukuran, $m)) {
        $a = (float) $m[1];
        $b = (float) $m[3];
        return ($a + $b) / 2.0; // dalam cm
    }
    return 0;
}

// === HELPER: fungsi terpusat menghitung skor deterministik untuk satu alternatif
private function calculateScore(Alternatif $alt, array $bobotKriteria, $kriterias, $pilihanUser = [], $minHarga = 0, $maxHarga = 0)
{
    $total = 0.0;

    // ambil ukuran target dari pilihan user (mis. "1 meter")
    $targetUkuranCm = 0;
    if (!empty($pilihanUser['ukuran'])) {
        if (preg_match('/\d+(\.\d+)?/', $pilihanUser['ukuran'], $m)) {
            $targetUkuranCm = (float)$m[0] * 100.0; // meter -> cm
        }
    }

    // rata2 ukuran alternatif (cm)
    $rata2Alt = 0;
    if ($alt->ukuran && preg_match('/(\d+(\.\d+)?)\s*[xX]\s*(\d+(\.\d+)?)/', $alt->ukuran, $mm)) {
        $rata2Alt = ((float)$mm[1] + (float)$mm[3]) / 2.0;
    }

    foreach ($kriterias as $k) {
        $bobot = $bobotKriteria[$k->id_kriteria] ?? 0;
        $raw = $this->nilaiAlternatifPerKriteria($alt, $k); // dapat 0..1 atau 1..9 atau raw harga

        $nilai = 0.0;
        $namaLow = strtolower($k->nama_kriteria);

        if ($namaLow === 'harga') {
            // raw = angka harga_total (pastikan caller mengirimkan min/max berdasarkan harga_total)
            $priceRaw = (float)$raw;
            if ($maxHarga > $minHarga) {
                $nilai = 1 - (($priceRaw - $minHarga) / ($maxHarga - $minHarga)); // lebih murah = lebih baik
            } else {
                $nilai = 1.0;
            }
            $nilai = max(0, min(1, $nilai));
        } elseif ($namaLow === 'ukuran') {
            // jika user menyertakan ukuran target, gunakan similarity Gaussian; kalau tidak, fallback ke nilai bucket (raw)
            if ($targetUkuranCm > 0 && $rata2Alt > 0) {
                $range = 50.0; // bandwidth (cm) — sesuaikan ke kebutuhan
                $diff = ($rata2Alt - $targetUkuranCm);
                $nilai = exp(-pow($diff / $range, 2)); // 0..1
            } else {
                $nilai = $raw > 1 ? ($raw / 9.0) : $raw;
            }
            $nilai = max(0, min(1, $nilai));
        } else {
            // kriteria diskrit: konversi 1..9 -> 0..1 jika perlu
            $nilai = $raw > 1 ? ($raw / 9.0) : $raw;
            $nilai = max(0, min(1, $nilai));
        }

        $total += $bobot * $nilai;
    }

    // penalti ketebalan jika tidak sesuai rekomendasi rentang
    if (!empty($pilihanUser['rentang_ketebalan_disarankan']) && $alt->ketebalan) {
        if (preg_match('/(\d+)\s*–\s*(\d+)/', $pilihanUser['rentang_ketebalan_disarankan'], $mm)) {
            $min = (float)$mm[1];
            $max = (float)$mm[2];
            $ket = (float)$alt->ketebalan;
            if ($min && $max && ($ket < $min || $ket > $max)) {
                $total *= 0.85; // turunkan 15%
            }
        }
    }

    return round($total, 6);
}



    // === Fungsi bantu (copy dari admin)
    private function ahpWeightsByGeometricMean($matrix)
    {
        $n = count($matrix);
        $geo = [];
        for ($i = 0; $i < $n; $i++) {
            $prod = array_product($matrix[$i]);
            $geo[$i] = pow($prod, 1 / $n);
        }
        $sum = array_sum($geo);
        return array_map(fn($g) => $g / $sum, $geo);
    }

    private function hitungCR($matrix, $weights)
    {
        $n = count($matrix);

        // Hitung total tiap kolom
        $colSum = [];
        for ($j = 0; $j < $n; $j++) {
            $sum = 0;
            for ($i = 0; $i < $n; $i++) {
                $sum += $matrix[$i][$j];
            }
            $colSum[$j] = $sum;
        }

        // Normalisasi
        $normalizedMatrix = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $normalizedMatrix[$i][$j] = $matrix[$i][$j] / $colSum[$j];
            }
        }

        // Weighted sum
        $weightedSum = [];
        for ($i = 0; $i < $n; $i++) {
            $sum = 0;
            for ($j = 0; $j < $n; $j++) {
                $sum += $matrix[$i][$j] * $weights[$j];
            }
            $weightedSum[$i] = $sum;
        }

        $ratio = [];
        for ($i = 0; $i < $n; $i++) {
            $ratio[$i] = $weights[$i] > 0 ? $weightedSum[$i] / $weights[$i] : 0;
        }

        $lambdaMax = array_sum($ratio) / $n;
        $CI = ($lambdaMax - $n) / ($n - 1);
        $RI = [1=>0,2=>0,3=>0.58,4=>0.9,5=>1.12][$n] ?? 1.12;
        $CR = $RI ? $CI / $RI : 0;

        return [
            'normalizedMatrix' => $normalizedMatrix,
            'weightedSum' => $weightedSum,
            'ratio' => $ratio,
            'weights' => $weights, 
            'lambdaMax' => round($lambdaMax, 5),
            'CI' => round($CI, 5),
            'CR' => round($CR, 5),
            'status' => $CR <= 0.1 ? 'Konsisten' : 'Tidak Konsisten'
        ];
    }

    // === PEMETAAN NILAI ALTERNATIF SESUAI SUBKRITERIA ===
    private function nilaiAlternatifPerKriteria(Alternatif $alt, Kriteria $k): float
{
    $nama = strtolower($k->nama_kriteria);

    // fungsi bantu untuk ambil nilai dari subkriteria
    $ambilNilai = function (int $idKriteria, string $namaLike): float {
        $sub = Subkriteria::where('id_kriteria', $idKriteria)
            ->where('nama_subkriteria', 'LIKE', "%{$namaLike}%")
            ->first();
        return $sub ? (float)$sub->nilai : 0.0;
    };

    switch ($nama) {
        case 'ukuran':
            $avg = $this->avgSizeCm($alt); // cm
            if ($avg <= 0) return 0.0;
            if ($avg < 100) return $ambilNilai($k->id_kriteria, 'Kurang dari 1 meter');
            elseif ($avg < 200) return $ambilNilai($k->id_kriteria, '1-2 meter');
            elseif ($avg < 300) return $ambilNilai($k->id_kriteria, '2-3 meter');
            else return $ambilNilai($k->id_kriteria, 'Lebih dari 3 meter');

        case 'ketebalan':
            return $ambilNilai($k->id_kriteria, $alt->ketebalan . ' mm');

        case 'tujuan penggunaan':
            return $ambilNilai($k->id_kriteria, $alt->tujuan_penggunaan ?? '');

        case 'lokasi penempatan':
            return $ambilNilai($k->id_kriteria, $alt->lokasi_penempatan ?? '');

        case 'pemotongan':
            return $ambilNilai($k->id_kriteria, $alt->pemotongan ?? '');

        case 'harga':
            // harga dinormalisasi bukan di sini; kembalikan harga raw (untuk dihitung kemudian)
            return $this->parseHargaToNumber($alt->harga);

        default:
            return 0.0;
    }
}


    // === HASIL PEMILIHAN / PERANKINGAN ===
    public function hasilPemilihan(Request $request)
    {
         @file_put_contents(storage_path('logs/raw_debug.txt'),
        "MASUK hasilPemilihan()\n",
        FILE_APPEND
    );
        // 1️⃣ Ambil bobot fuzzy dari session user
        $bobotSession = session('bobot_user');

        if (!$bobotSession || $bobotSession->isEmpty()) {
            return redirect()->route('clients.fuzzy')
                ->with('error', 'Silakan tentukan bobot kriteria terlebih dahulu sebelum melihat hasil peringkat.');
        }

        // ✅ Konversi bobot ke array id_kriteria => nilai prioritas
        $bobotKriteria = [];
        foreach ($bobotSession as $item) {
            $kriteria = $item->kriteria ?? null;
            if ($kriteria) {
                $bobotKriteria[$kriteria->id_kriteria] = $item->prioritas;
            }
        }

        // Normalisasi bobot supaya total = 1
        $total = array_sum($bobotKriteria);
        foreach ($bobotKriteria as $id => $val) {
            $bobotKriteria[$id] = $total > 0 ? $val / $total : 0;
        }

        // 2️⃣ Ambil semua data alternatif dan kriteria
        $alternatifs = \App\Models\Alternatif::all();
        $kriterias = \App\Models\Kriteria::all();

        $hasil = [];

        // 3️⃣ Loop alternatif dan hitung nilai total per alternatif
        $hasil = [];

        // Ambil semua kriteria (sekali)
        $kriteriasAll = Kriteria::all();

        // Hitung min/max harga_total semua alternatif untuk normalisasi harga
        $hargaSemua = \App\Models\Alternatif::all()->map(function($a){
            $hargaPerM = (float) preg_replace('/[^0-9.]/', '', $a->harga);
            $p = preg_split('/x/i', $a->ukuran ?? '');
            $v = array_map(fn($v)=>(float)preg_replace('/[^0-9.]/','',$v),$p);
            $luas = (count($v)==2)?(($v[0]/100)*($v[1]/100)):1;
            return $hargaPerM * $luas;
        })->toArray();

        $maxHarga = !empty($hargaSemua) ? max($hargaSemua) : 0;
        $minHarga = !empty($hargaSemua) ? min($hargaSemua) : 0;

        // Loop alternatif: gunakan calculateScore yang deterministik
        foreach ($alternatifs as $alt) {
            $skor = $this->calculateScore($alt, $bobotKriteria, $kriteriasAll, [], $minHarga, $maxHarga);

            $hasil[] = [
                'nama_alternatif' => $alt->nama,
                'jenis' => $alt->jenis,
                'ukuran' => $alt->ukuran,
                'ketebalan' => $alt->ketebalan,
                'harga' => $alt->harga,
                'skor_total' => round($skor, 6),
            ];
        }


    // 4️⃣ Urutkan berdasarkan skor tertinggi
    usort($hasil, fn($a, $b) => $b['skor_total'] <=> $a['skor_total']);

    // 5️⃣ Tampilkan hasil ke view
    return view('clients.hasil_perankingan', compact('hasil'))
        ->with('success', 'Hasil perhitungan rekomendasi berhasil dibuat berdasarkan bobot pribadi Anda.');
}

    public function exportHasilUserPDF()
{
    $hasil = session('hasil_user');
    $pilihan = session('pilihan_user');

    if (!$hasil) {
        return redirect()->route('clients.pemilihan')
            ->with('error', 'Tidak ada data hasil untuk dicetak. Silakan lakukan perhitungan terlebih dahulu.');
    }

    $tanggal = Carbon::now()->translatedFormat('d F Y');
    $user = Auth::user();

    $pdf = Pdf::loadView('clients.laporan_pdf', compact('hasil', 'tanggal', 'user', 'pilihan'))
        ->setPaper('a4', 'portrait');

    return $pdf->stream('Laporan_Hasil_Rekomendasi_Kaca.pdf');
}



} // ← ini tutup class ClientController



