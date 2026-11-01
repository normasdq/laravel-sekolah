<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Seleksi SPK SAW - {{ $schoolName }}</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #222;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1a5f7a;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .header h2 {
            margin: 0;
            font-size: 15pt;
            color: #1a5f7a;
            text-transform: uppercase;
        }
        .header p {
            margin: 3px 0 0;
            font-size: 9pt;
            color: #555;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            margin: 14px 0 6px;
            color: #1a5f7a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            font-size: 9pt;
        }
        th {
            background-color: #f0f4f8;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge-lulus {
            color: #0f5132;
            background-color: #d1e7dd;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
        .badge-gagal {
            color: #842029;
            background-color: #f8d7da;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }
        .footer-sign {
            margin-top: 25px;
            width: 100%;
        }
        .footer-sign td {
            border: none;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $schoolName }}</h2>
        <p><strong>LAPORAN HASIL PENDUKUNG KEPUTUSAN SELEKSI SISWA BARU — METODE SAW</strong></p>
        <p>Dicetak pada: {{ $printedAt }} WIB | Ambang Batas Kelulusan (Threshold): <strong>{{ (int) round($threshold * 100) }}</strong></p>
    </div>

    <div class="section-title">A. Kriteria & Bobot Penilaian</div>
    <table>
        <thead>
            <tr>
                <th style="width: 60px">Kode</th>
                <th>Nama Kriteria</th>
                <th style="width: 90px">Atribut</th>
                <th style="width: 100px">Bobot Awal (%)</th>
                <th style="width: 120px">Bobot Normalisasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($criteria as $c)
                <tr>
                    <td class="text-center"><strong>{{ $c->code }}</strong></td>
                    <td>{{ $c->name }}</td>
                    <td class="text-center">{{ strtoupper($c->type) }}</td>
                    <td class="text-center">{{ (int) round($c->weight) }}%</td>
                    <td class="text-center">{{ (int) round(($weights[$c->id] ?? 0) * 100) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">B. Hasil Perankingan & Rekomendasi Kelulusan Calon Siswa</div>
    <table>
        <thead>
            <tr>
                <th style="width: 45px">Rank</th>
                <th style="width: 110px">No. Pendaftaran</th>
                <th>Nama Calon Siswa</th>
                <th style="width: 90px">NISN</th>
                @foreach ($criteria as $c)
                    <th>{{ $c->code }} (Mentah)</th>
                    <th>{{ $c->code }} (Normalisasi)</th>
                @endforeach
                <th style="width: 85px">Skor Akhir (V)</th>
                <th style="width: 110px">Keputusan SAW</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ranking as $row)
                <tr>
                    <td class="text-center"><strong>#{{ $row['rank'] }}</strong></td>
                    <td class="text-center">{{ $row['registration']->registration_number }}</td>
                    <td><strong>{{ $row['registration']->name }}</strong></td>
                    <td class="text-center">{{ $row['registration']->nisn }}</td>
                    @foreach ($criteria as $c)
                        <td class="text-center">{{ (int) round($row['raw_scores'][$c->id] ?? 0) }}</td>
                        <td class="text-center">{{ (int) round(($row['normalized_scores'][$c->id] ?? 0) * 100) }}</td>
                    @endforeach
                    <td class="text-center"><strong>{{ (int) round($row['preference_score'] * 100) }}</strong></td>
                    <td class="text-center">
                        @if ($row['is_passed'])
                            <span class="badge-lulus">LULUS</span>
                        @else
                            <span class="badge-gagal">TIDAK LULUS</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 6 + ($criteria->count() * 2) }}" class="text-center">Belum ada data calon siswa yang dihitung.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer-sign">
        <tr>
            <td style="width: 70%"></td>
            <td class="text-center">
                Mengetahui,<br>
                <strong>Ketua Panitia SPMB</strong>
                <br><br><br><br>
                ( ___________________________ )
            </td>
        </tr>
    </table>
</body>
</html>

