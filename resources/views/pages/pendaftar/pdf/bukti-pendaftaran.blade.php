<!DOCTYPE html>
<html lang="id">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Bukti Pendaftaran - {{ $registration->name }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 50px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
            border-top: 8px solid #1a5f7a;
            /* Warna Aksen Biru Formal */
        }

        /* Watermark Status (Optional) */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80pt;
            color: rgba(0, 0, 0, 0.03);
            white-space: nowrap;
            z-index: 0;
            pointer-events: none;
        }

        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #1a5f7a;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header-logo {
            display: table-cell;
            vertical-align: middle;
            width: 80px;
        }

        .header-text {
            display: table-cell;
            vertical-align: middle;
            padding-left: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 18pt;
            color: #1a5f7a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header p {
            margin: 2px 0;
            font-size: 9pt;
            color: #666;
        }

        .registration-meta {
            width: 100%;
            margin-bottom: 30px;
            font-size: 10pt;
        }

        .title-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .title-section h1 {
            margin: 0;
            font-size: 16pt;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #eee;
            display: inline-block;
            padding-bottom: 5px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            z-index: 1;
            position: relative;
        }

        .info-table td {
            padding: 10px 5px;
            border-bottom: 1px solid #f1f1f1;
            vertical-align: top;
        }

        .label {
            width: 160px;
            color: #777;
            font-weight: normal;
            text-transform: uppercase;
            font-size: 9pt;
        }

        .value {
            font-weight: 600;
            color: #222;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            background-color: #e3f2fd;
            color: #1a5f7a;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10pt;
        }

        .notes-box {
            margin-top: 30px;
            padding: 20px;
            border-radius: 8px;
            background-color: #f8f9fa;
            border-left: 4px solid #ffc107;
        }

        .notes-box h4 {
            margin: 0 0 10px 0;
            color: #856404;
            font-size: 10pt;
        }

        .notes-content {
            font-size: 10pt;
            color: #555;
            font-style: italic;
        }

        .footer-grid {
            margin-top: 50px;
            width: 100%;
        }

        .qr-code {
            width: 100px;
            height: 100px;
            background-color: #eee;
            text-align: center;
            line-height: 100px;
            font-size: 8pt;
            color: #999;
            border: 1px solid #ddd;
        }

        .signature-wrapper {
            text-align: right;
        }

        .signature-space {
            height: 70px;
        }

        .footer-text {
            font-size: 9pt;
            color: #888;
            margin-top: 20px;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="watermark">PPDB {{ date('Y') }}</div>

        <!-- Header / Kop Surat -->
        <div class="header">
            <div class="header-logo">
                @if (extension_loaded('gd') && file_exists(public_path('assets/images/logo-tunas-harapan.png')))
                    <img src="{{ public_path('assets/images/logo-tunas-harapan.png') }}" alt="Logo Sekolah"
                        style="width: 80px;">
                @else
                    <div style="width: 70px; height: 70px; background-color: #1a5f7a; border-radius: 8px; text-align: center; line-height: 70px; color: #fff; font-size: 20pt; font-weight: bold;">
                        TH
                    </div>
                @endif
            </div>
            <div class="header-text">
                <h2>{{ $settings->get('school_name', 'SMP Tunas Harapan Bekasi') }}</h2>
                <p>{{ $settings->get('school_address', 'JL. RS. Mekar Sari No 71.B Bekasi Jaya-Bekasi Timur 17112 Kota Bekasi') }}</p>
                <p>Email: {{ $settings->get('school_email', 'smptupanbekasi71@gmail.com') }} | Telp: {{ $settings->get('school_phone', '081770748835') }}</p>
            </div>
        </div>

        <!-- Meta Data -->
        <table class="registration-meta">
            <tr>
                <td><strong>No. Registrasi:</strong> <span
                        style="color: #1a5f7a;">{{ $registration->registration_number }}</span>
                </td>
                {{-- realtime jam --}}
                <td style="text-align: right;"><strong>Tanggal Pendaftaran:</strong>
                    {{ $registration->created_at->format('d F Y, H:i') }}</td>
            </tr>
        </table>

        <div class="title-section">
            <h1>BUKTI PENDAFTARAN SISWA BARU</h1>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="value">{{ $registration->name }}</td>
            </tr>
            <tr>
                <td class="label">NISN</td>
                <td class="value">{{ $registration->nisn }}</td>
            </tr>
            <tr>
                <td class="label">Tempat, Tgl Lahir</td>
                <td class="value">{{ $registration->birth_place }}, {{ $registration->birth_date?->format('d F Y') }}
                </td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td class="value">{{ $registration->gender_label }}</td>
            </tr>
            <tr>
                <td class="label">Asal Sekolah</td>
                <td class="value">{{ $registration->school_origin }}</td>
            </tr>
            <tr>
                <td class="label">Nama Orang Tua</td>
                <td class="value">{{ $registration->parent_name }}</td>
            </tr>
            <tr>
                <td class="label">Alamat Lengkap</td>
                <td class="value" style="font-weight: normal; font-size: 10pt;">{{ $registration->address }}</td>
            </tr>
            <tr>
                <td class="label">Status Saat Ini</td>
                <td><span class="status-badge">{{ $registration->status_label }}</span></td>
            </tr>
        </table>

        @if ($registration->notes)
            <div class="notes-box">
                <h4><i class="alert-icon"></i> INFORMASI PENTING / CATATAN PANITIA:</h4>
                <div class="notes-content">
                    {!! nl2br(e($registration->notes)) !!}
                </div>
            </div>
        @endif

        <table class="footer-grid">
            <tr>
                {{-- <td width="30%">
                    <!-- Area untuk QR Code (Verifikasi Digital) -->
                    <div class="qr-code">
                        QR VERIFIED
                    </div>
                    <p style="font-size: 7pt; color: #999; margin-top: 5px;">Scan untuk verifikasi data</p>
                </td> --}}
                <td class="signature-wrapper">
                    <p>Bekasi, {{ date('d F Y') }}</p>
                    <p>Mengetahui,<br><strong>Panitia PPDB</strong></p>
                    <div class="signature-space"></div>
                    <p><strong>( __________________________ )</strong></p>
                    <p style="font-size: 8pt; color: #666;">NIP. ..........................................</p>
                </td>
            </tr>
        </table>

        <div class="footer-text">
            Simpan bukti pendaftaran ini sebagai syarat verifikasi ulang. Dokumen ini diterbitkan secara elektronik oleh
            Sistem Informasi Akademik {{ $settings->get('school_name') }}.
        </div>
    </div>

</body>

</html>
