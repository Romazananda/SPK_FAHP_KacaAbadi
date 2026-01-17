<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Perangkingan - Admin</title>
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

    <h3 style="text-align:center; text-decoration: underline;">Laporan Hasil Perangkingan (Admin)</h3>
    <p style="text-align:center;">Tanggal: {{ $tanggal }}</p>

    <table>
        <thead>
            <tr>
                <th>Ranking</th>
                <th>Nama Produk</th>
                <th>Jenis</th>
                <th>Ukuran</th>
                <th>Ketebalan</th>
                <th>Harga (Rp/m²)</th>
                <th>Skor Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($hasil as $i => $h)
            <tr>
                <td>{{ $h->ranking }}</td>
                <td>{{ $h->alternatif->nama ?? '-' }}</td>
                <td>{{ $h->alternatif->jenis ?? '-' }}</td>
                <td>{{ $h->alternatif->ukuran ?? '-' }}</td>
                <td>{{ $h->alternatif->ketebalan ?? '-' }}</td>
                <td>Rp {{ number_format($h->alternatif->harga ?? 0, 0, ',', '.') }}</td>
                <td>{{ number_format($h->nilai_total ?? 0, 6) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="ttd">
        <p>Malang, {{ $tanggal }}</p>
        <p><strong>Pimpinan Toko</strong></p>
    </div>
</body>
</html>
