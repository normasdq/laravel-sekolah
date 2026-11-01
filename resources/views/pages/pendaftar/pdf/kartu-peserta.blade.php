<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Kartu Peserta - {{ $registration->name }}</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #e8edf2;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .card {
            width: 540px;
            background: #fff;
            position: relative;
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
        }

        /* Decorative top bar */
        .top-bar {
            height: 10px;
            background: linear-gradient(90deg, #0d3b4f, #1a5f7a, #2980b9, #1a5f7a, #0d3b4f);
        }

        /* Header with school identity */
        .header {
            text-align: center;
            padding: 20px 30px 14px;
            position: relative;
        }
        .header:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 40px;
            right: 40px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #1a5f7a, transparent);
        }
        .header .school-name {
            font-size: 20px;
            font-weight: 800;
            color: #0d3b4f;
            letter-spacing: 1.5px;
            margin: 0 0 3px;
            text-transform: uppercase;
        }
        .header .card-title {
            font-size: 13px;
            color: #1a5f7a;
            font-weight: 600;
            margin: 0;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .header .year {
            font-size: 11px;
            color: #888;
            margin: 4px 0 0;
        }

        /* Main content area */
        .content {
            padding: 22px 30px 18px;
        }

        /* Participant row: photo + info */
        .participant-row {
            display: flex;
            gap: 22px;
        }

        /* Photo area */
        .photo {
            flex-shrink: 0;
            width: 120px;
            height: 150px;
            border: 3px solid #1a5f7a;
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f7fa;
            position: relative;
        }
        .photo .placeholder {
            text-align: center;
            color: #bbb;
        }
        .photo .placeholder .icon {
            font-size: 36px;
            line-height: 1;
        }
        .photo .placeholder .label {
            font-size: 9px;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .photo .border-decor {
            position: absolute;
            bottom: 6px;
            right: 6px;
            width: 20px;
            height: 20px;
            border-right: 2px solid #1a5f7a;
            border-bottom: 2px solid #1a5f7a;
            opacity: 0.3;
        }
        .photo .border-decor-tl {
            position: absolute;
            top: 6px;
            left: 6px;
            width: 20px;
            height: 20px;
            border-left: 2px solid #1a5f7a;
            border-top: 2px solid #1a5f7a;
            opacity: 0.3;
        }

        /* Info section */
        .info {
            flex: 1;
            min-width: 0;
        }
        .info table {
            width: 100%;
            border-collapse: collapse;
        }
        .info tr {
            border-bottom: 1px solid #f0f0f0;
        }
        .info tr:last-child {
            border-bottom: none;
        }
        .info td {
            padding: 5px 0;
            font-size: 12px;
            vertical-align: middle;
        }
        .info td.label {
            width: 100px;
            color: #888;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        .info td.value {
            font-weight: 700;
            color: #1a1a2e;
            font-size: 13px;
        }
        .info td.value .nisn-code {
            font-family: 'Courier New', monospace;
            letter-spacing: 1.5px;
            color: #0d3b4f;
        }
        .info td.value .reg-number {
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
            background: #f0f4f8;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        /* Status badge */
        .status-badge {
            display: inline-block;
            padding: 3px 14px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-verified { background: #dbeafe; color: #1e40af; }
        .status-lulus { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-tidak_lulus { background: #fee2e2; color: #991b1b; }

        /* Exam info box */
        .exam-box {
            margin-top: 16px;
            border: 1.5px solid #dce7ef;
            border-radius: 10px;
            overflow: hidden;
        }
        .exam-box-header {
            background: linear-gradient(135deg, #0d3b4f, #1a5f7a);
            color: #fff;
            padding: 8px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .exam-box-header .icon {
            font-size: 14px;
        }
        .exam-box-body {
            padding: 10px 16px;
            display: flex;
            gap: 16px;
            background: #fafcfd;
        }
        .exam-item {
            flex: 1;
        }
        .exam-item .ex-label {
            font-size: 8px;
            text-transform: uppercase;
            color: #999;
            letter-spacing: 0.5px;
            font-weight: 700;
            margin-bottom: 2px;
        }
        .exam-item .ex-value {
            font-size: 11px;
            font-weight: 700;
            color: #1a1a2e;
        }

        /* Separator line */
        .separator {
            border: none;
            border-top: 1px dashed #dce7ef;
            margin: 14px 0 10px;
        }

        /* Footer signature area */
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0 30px 18px;
        }
        .footer-left {
            font-size: 8px;
            color: #aaa;
        }
        .footer-right {
            text-align: center;
        }
        .footer-right .signature-line {
            width: 140px;
            border-top: 1px solid #333;
            padding-top: 4px;
            font-size: 10px;
            font-weight: 600;
            color: #333;
        }
        .footer-right .signature-label {
            font-size: 8px;
            color: #999;
            margin-top: 2px;
        }

        /* Bottom strip */
        .bottom-bar {
            height: 5px;
            background: linear-gradient(90deg, #0d3b4f, #1a5f7a, #2980b9, #1a5f7a, #0d3b4f);
        }

        /* Print-specific */
        @media print {
            body { background: #fff; }
            .card { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="top-bar"></div>

        <div class="header">
            <p class="school-name">{{ strtoupper($settings->get('school_name', 'SEKOLAH')) }}</p>
            <p class="card-title">Kartu Peserta Ujian Seleksi</p>
            <p class="year">Tahun Ajaran {{ date('Y') }} / {{ date('Y') + 1 }}</p>
        </div>

        <div class="content">
            <div class="participant-row">
                <div class="photo">
                    @if (!empty($photoDataUri))
                        <img src="{{ $photoDataUri }}" alt="Pas Foto" style="width: 120px; height: 150px; object-fit: cover; display: block;">
                    @else
                        <div class="placeholder">
                            <div class="icon">👤</div>
                            <div class="label">Pas Foto</div>
                        </div>
                        <div class="border-decor-tl"></div>
                        <div class="border-decor"></div>
                    @endif
                </div>
                <div class="info">
                    <table>
                        <tr>
                            <td class="label">Nama</td>
                            <td class="value">{{ $registration->name }}</td>
                        </tr>
                        <tr>
                            <td class="label">NISN</td>
                            <td class="value"><span class="nisn-code">{{ $registration->nisn }}</span></td>
                        </tr>
                        <tr>
                            <td class="label">No. Daftar</td>
                            <td class="value"><span class="reg-number">{{ $registration->registration_number }}</span></td>
                        </tr>
                        <tr>
                            <td class="label">Asal Sekolah</td>
                            <td class="value">{{ $registration->school_origin }}</td>
                        </tr>
                        <tr>
                            <td class="label">Jenis Kelamin</td>
                            <td class="value">{{ $registration->gender_label }}</td>
                        </tr>
                        <tr>
                            <td class="label">Status</td>
                            <td class="value">
                                <span class="status-badge status-{{ $registration->status }}">
                                    {{ $registration->status_label }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <hr class="separator">

            <div class="exam-box">
                <div class="exam-box-header">
                    <span class="icon">📋</span>
                    Informasi Jadwal Ujian
                </div>
                <div class="exam-box-body">
                    <div class="exam-item">
                        <div class="ex-label">Tanggal</div>
                        <div class="ex-value">{{ $settings->get('spmb_exam_date', 'Akan diinformasikan') }}</div>
                    </div>
                    <div class="exam-item">
                        <div class="ex-label">Waktu</div>
                        <div class="ex-value">{{ $settings->get('spmb_exam_time', 'Akan diinformasikan') }}</div>
                    </div>
                    <div class="exam-item">
                        <div class="ex-label">Lokasi</div>
                        <div class="ex-value">{{ $settings->get('spmb_exam_location', 'Akan diinformasikan') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="footer-left">
                Dicetak: {{ now()->format('d/m/Y H:i') }}<br>
                Dokumen ini sah tanpa tanda tangan
            </div>
            <div class="footer-right">
                <div class="signature-line">
                    Panitia SPMB {{ $settings->get('school_short_name', 'Sekolah') }}
                </div>
                <div class="signature-label">Verifikator &amp; Panitia Seleksi</div>
            </div>
        </div>

        <div class="bottom-bar"></div>
    </div>
</body>
</html>
