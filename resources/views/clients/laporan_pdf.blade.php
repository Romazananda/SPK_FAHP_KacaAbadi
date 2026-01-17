<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Rekomendasi Kaca Abadi</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            margin: 30px;
            color: #222;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header img {
            width: 70px;
            height: 70px;
            float: left;
            margin-right: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #888;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 12px;
        }
        .ttd {
            margin-top: 60px;
            text-align: right;
            font-size: 12px;
        }
        .ttd p {
            margin-bottom: 60px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Toko Kaca Abadi</h1>
        <p>Jl. Bendungan Sutami No. 12, Malang | Telp: (0341) 123-456</p>
    </div>

    <h3 style="text-align:center; text-decoration: underline;">Laporan Hasil Pemilihan Kaca / Cermin</h3>
    <p style="text-align:center;">Tanggal: {{ $tanggal }}</p>

    {{-- Jika ingin menampilkan pilihan user --}}
    @php
        $pilihan = session('pilihan_user', []);
    @endphp

    <div style="margin-bottom: 15px; border: 1px solid #ccc; padding: 10px;">
        <strong>Ringkasan Input dan Analisis Sistem:</strong>
        <table style="width:100%; border-collapse: collapse; font-size:12px; margin-top:5px;">
            <tr>
                <td style="width:35%; border:1px solid #ddd; padding:5px;">Tujuan Penggunaan</td>
                <td style="border:1px solid #ddd; padding:5px;">{{ $pilihan['tujuan'] ?? '-' }}</td>
            </tr>
            <tr>
                <td style="border:1px solid #ddd; padding:5px;">Lokasi Penempatan</td>
                <td style="border:1px solid #ddd; padding:5px;">{{ $pilihan['lokasi'] ?? '-' }}</td>
            </tr>
            <tr>
                <td style="border:1px solid #ddd; padding:5px;">Ukuran Input</td>
                <td style="border:1px solid #ddd; padding:5px;">{{ $pilihan['ukuran'] ?? '-' }}</td>
            </tr>
            <tr>
                <td style="border:1px solid #ddd; padding:5px;">Ketebalan Dipilih</td>
                <td style="border:1px solid #ddd; padding:5px;">{{ $pilihan['ketebalan'] ?? '-' }}</td>
            </tr>
            <tr>
                <td style="border:1px solid #ddd; padding:5px;">Pemotongan</td>
                <td style="border:1px solid #ddd; padding:5px;">{{ $pilihan['pemotongan'] ?? '-' }}</td>
            </tr>
        </table>
    </div>


    <table>
        <thead>
            <tr>
                <th>Ranking</th>
                <th>Nama Produk</th>
                <th>Jenis</th>
                <th>Ukuran</th>
                <th>Ketebalan (mm)</th>
                <th>Total Harga (Rp)</th>
                <th>Skor Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hasil as $i => $h)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $h['nama_alternatif'] }}</td>
                <td>{{ $h['jenis'] }}</td>
                <td>{{ $h['ukuran'] }}</td>
                <td>{{ $h['ketebalan'] }}</td>
                <td>Rp {{ number_format(str_replace('.', '', $h['harga_total']) ?? 0, 0, ',', '.') }}</td>
                <td>{{ number_format($h['skor_total'], 6) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="ttd">
        <p>Malang, {{ $tanggal }}</p>
        <p><strong>Pimpinan Toko</strong></p>
        <p><u><strong></strong></u></p>
    </div>
</body>
</html>
