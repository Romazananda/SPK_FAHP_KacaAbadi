<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClientController;
use Barryvdh\DomPDF\Facade\Pdf;

// Landing page
Route::get('/', function () {
    return view('page');
});

// Auth pages (GET)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', function(Request $request){
    $request->validate([
        'name' => 'required|string|max:255',
        'username' => 'required|string|max:50|unique:users,username',
        'password' => 'required|string|confirmed|min:6',
    ]);

    // Tentukan role
    $role = ($request->username === 'admin' && $request->name === 'AdminName') ? 'admin' : 'user';

    User::create([
        'name' => $request->name,
        'username' => $request->username,
        'password' => Hash::make($request->password),
        'role' => $role,
    ]);

    // Kembali ke halaman login
    return redirect('/login')->with('success', 'Registration successful. Please login!');
});
Route::post('/login', function(Request $request){
    $credentials = $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // Redirect berdasarkan role
        return Auth::user()->role === 'admin' 
            ? redirect('admin/dashboard_admin') 
            : redirect('clients/dashboard_clients');
    }

    return back()->withErrors([
        'username' => 'Username atau Password anda tidak cocok.',
    ]);
});

// Logout route
Route::post('/logout', function() {
    Auth::logout();
    return redirect('/');
})->name('logout');

// Routes Admin
Route::prefix('admin')->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
    Route::get('/dashboard_admin', [AdminController::class, 'index'])->name('dashboard_admin');
    //untuk kriteria
    Route::get('/kriteria', [AdminController::class, 'kriteria'])->name('kriteria');
    Route::get('/kriteria/create', [AdminController::class, 'kriteriaCreate'])->name('kriteria.create');
    Route::post('/kriteria', [AdminController::class, 'kriteriaStore'])->name('kriteria.store');
    Route::get('/kriteria/{id}/edit', [AdminController::class, 'kriteriaEdit'])->name('kriteria.edit');
    Route::put('/kriteria/{id}', [AdminController::class, 'kriteriaUpdate'])->name('kriteria.update');
    Route::delete('/kriteria/{id}', [AdminController::class, 'kriteriaDestroy'])->name('kriteria.destroy');

    //untuk subkriteria
    Route::get('/subkriteria', [AdminController::class, 'subkriteriaIndex'])->name('subkriteria');
    Route::post('/subkriteria', [AdminController::class, 'subkriteriaStore'])->name('subkriteria.store');
    Route::get('/subkriteria/{id}/edit', [AdminController::class, 'subkriteriaEdit'])->name('subkriteria.edit');
    Route::put('/subkriteria/{id}', [AdminController::class, 'subkriteriaUpdate'])->name('subkriteria.update');
    Route::delete('/subkriteria/{id}', [AdminController::class, 'subkriteriaDestroy'])->name('subkriteria.destroy');

    //bobot kriteria fuzzy
    Route::get('/kriteria/bobot', [AdminController::class,'kriteriaKuesionerForm'])->name('kriteria.kuisoner');
    Route::post('/kriteria/bobot', [AdminController::class,'kriteriaKuesionerStore'])->name('kriteria.kuisoner.store');

    //untuk alternatif
    Route::get('/alternatif', [AdminController::class, 'alternatif'])->name('alternatif');
    Route::post('/alternatif/store', [AdminController::class, 'storeAlternatif'])->name('store_alternatif');
    Route::put('/alternatif/{id}', [AdminController::class, 'update'])->name('update_alternatif');
    Route::delete('/alternatif/{id}', [AdminController::class, 'destroy'])->name('destroy_alternatif');

    //CRUD preferensi
    Route::get('/preferensi', [AdminController::class, 'preferensiIndex'])->name('preferensi.index');
    Route::get('/preferensi/create', [AdminController::class, 'preferensiCreate'])->name('preferensi.create');
    Route::post('/preferensi/store', [AdminController::class, 'preferensiStore'])->name('preferensi.store');
    Route::get('/preferensi/{id}/edit', [AdminController::class, 'preferensiEdit'])->name('preferensi.edit');
    Route::put('/preferensi/{id}/update', [AdminController::class, 'preferensiUpdate'])->name('preferensi.update');
    Route::delete('/preferensi/{id}/delete', [AdminController::class, 'preferensiDestroy'])->name('preferensi.destroy');



    // 🔹 Cetak Laporan PDF Admin
    Route::get('/hasil-pdf', [AdminController::class, 'exportHasilAdminPDF'])->name('hasil.pdf');

    // Logout
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
    Route::post('/nilai/generate/{id}', [AdminController::class, 'generateNilaiKecocokanUntukPreferensi'])
    ->name('admin.nilai.generate.preferensi');

    // 🔹 Perangkingan
    Route::get('/ranking', [AdminController::class, 'hasilRanking'])->name('ranking');
    Route::post('/ranking/hitung', [AdminController::class, 'hitungRanking'])->name('admin.ranking.hitung');

    // 🔹 Generate Penilaian (buat tombol di view hasil_ranking)
    Route::post('/penilaian/generate', [AdminController::class, 'generatePenilaian'])->name('penilaian.generate');

    //Tambahan konfirmasi subkriteria dari user
    Route::post('/subkriteria/{id}/approve', [AdminController::class, 'approveSubkriteria'])->name('subkriteria.approve');
    Route::post('/subkriteria/{id}/reject', [AdminController::class, 'rejectSubkriteria'])->name('subkriteria.reject');
    // Data subkriteria pending dari user
    Route::get('/subkriteria/pending', [AdminController::class, 'subkriteriaPending'])
    ->name('subkriteria.pending');

});


    // Dashboard (CLIENTS)
    Route::prefix('clients')
    ->name('clients.')
    ->middleware(['auth', 'role:user'])
    ->group(function () {
        Route::get('/dashboard_clients', [\App\Http\Controllers\ClientController::class, 'dashboard'])
            ->name('dashboard');
    // === FAHP User ===
    Route::get('/fuzzy', [\App\Http\Controllers\ClientController::class, 'userFuzzyForm'])->name('fuzzy');
    Route::post('/fuzzy/store', [\App\Http\Controllers\ClientController::class, 'userKuesionerStore'])->name('fuzzy.store');
    Route::post('/fuzzy/generate-matrix', [\App\Http\Controllers\ClientController::class, 'generateMatrix'])->name('fuzzy.generateMatrix');


    // ubah method ke "showForm" dan "hasil"
    Route::get('/pemilihan', [ClientController::class, 'showForm'])->name('pemilihan');
    Route::post('/hasil-rekomendasi', [ClientController::class, 'hasil'])->name('hasil');

    // === CETAK PDF HASIL ===
    Route::get('/hasil/pdf', [\App\Http\Controllers\ClientController::class, 'exportHasilUserPDF'])->name('hasil.pdf');
    
    // ✨ halaman baru untuk tambah subkriteria
    Route::get('/subkriteria/tambah', [ClientController::class, 'showTambahSubkriteria'])->name('subkriteria.form');
    Route::post('/subkriteria/tambah', [ClientController::class, 'tambahSubkriteria'])->name('subkriteria.simpan');
    Route::post('/subkriteria/update/{id}', [ClientController::class, 'updateSubkriteria'])->name('subkriteria.update');
    Route::delete('/subkriteria/hapus/{id}', [ClientController::class, 'hapusSubkriteria'])->name('subkriteria.hapus');


});




