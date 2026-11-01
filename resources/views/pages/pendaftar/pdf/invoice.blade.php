<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Invoice - {{ $payment->transaction_id }}</title>
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

        .invoice {
            width: 640px;
            background: #fff;
            position: relative;
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
        }

        .top-bar {
            height: 8px;
            background: linear-gradient(90deg, #0d3b4f, #1a5f7a, #2980b9, #1a5f7a, #0d3b4f);
        }

        .header {
            text-align: center;
            padding: 24px 36px 16px;
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
        .header .invoice-title {
            font-size: 16px;
            color: #1a5f7a;
            font-weight: 700;
            margin: 4px 0 0;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .content {
            padding: 20px 36px 18px;
        }

        .status-banner {
            text-align: center;
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .status-paid {
            background: #d1fae5;
            color: #065f46;
            border: 1.5px solid #a7f3d0;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
            border: 1.5px solid #fde68a;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #1a5f7a;
            margin: 18px 0 8px;
            padding-bottom: 4px;
            border-bottom: 2px solid #e8edf2;
        }

        table.info-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.info-table td {
            padding: 5px 0;
            font-size: 12px;
            vertical-align: top;
        }
        table.info-table td.label {
            width: 130px;
            color: #888;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        table.info-table td.value {
            font-weight: 600;
            color: #1a1a2e;
            font-size: 13px;
        }
        table.info-table td.value .mono {
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
            color: #0d3b4f;
        }

        .amount-box {
            margin: 14px 0;
            padding: 14px 20px;
            background: #f0f7ff;
            border: 1.5px solid #dbeafe;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .amount-box .amount-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
            font-weight: 700;
        }
        .amount-box .amount-value {
            font-size: 24px;
            font-weight: 800;
            color: #0d3b4f;
        }

        .detail-grid {
            display: flex;
            gap: 12px;
            margin: 10px 0;
        }
        .detail-grid .item {
            flex: 1;
            padding: 10px 12px;
            background: #f9fafb;
            border-radius: 6px;
            border: 1px solid #e8edf2;
        }
        .detail-grid .item .d-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #999;
            font-weight: 700;
            margin-bottom: 2px;
        }
        .detail-grid .item .d-value {
            font-size: 11px;
            font-weight: 700;
            color: #1a1a2e;
        }

        .separator {
            border: none;
            border-top: 1px dashed #dce7ef;
            margin: 14px 0;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0 36px 20px;
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

        .bottom-bar {
            height: 5px;
            background: linear-gradient(90deg, #0d3b4f, #1a5f7a, #2980b9, #1a5f7a, #0d3b4f);
        }

        @media print {
            body { background: #fff; }
            .invoice { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="invoice">
        <div class="top-bar"></div>

        <div class="header">
            <p class="school-name">{{ strtoupper($settings->get('school_name', 'SEKOLAH')) }}</p>
            <p class="invoice-title">Invoice Pembayaran</p>
        </div>

        <div class="content">
            <div class="status-banner status-{{ $payment->status === 'success' ? 'paid' : 'pending' }}">
                {{ $payment->status === 'success' ? 'LUNAS' : 'MENUNGGU PEMBAYARAN' }}
            </div>

            <div class="section-title">Informasi Transaksi</div>
            <table class="info-table">
                <tr>
                    <td class="label">No. Invoice</td>
                    <td class="value"><span class="mono">{{ $payment->transaction_id }}</span></td>
                </tr>
                <tr>
                    <td class="label">Tanggal</td>
                    <td class="value">{{ $payment->created_at->isoFormat('D MMMM Y, HH:mm') }} WIB</td>
                </tr>
                <tr>
                    <td class="label">Status</td>
                    <td class="value">{{ $payment->status_label }}</td>
                </tr>
                @if ($payment->paid_at)
                <tr>
                    <td class="label">Dibayar Pada</td>
                    <td class="value">{{ $payment->paid_at->isoFormat('D MMMM Y, HH:mm') }} WIB</td>
                </tr>
                @endif
                @if ($payment->method)
                <tr>
                    <td class="label">Metode</td>
                    <td class="value">{{ $payment->method }}</td>
                </tr>
                @endif
            </table>

            <div class="amount-box">
                <div>
                    <div class="amount-label">Total Pembayaran</div>
                    <div style="font-size:10px;color:#888;margin-top:2px;">Biaya Daftar Ulang Siswa Baru (SPMB)</div>
                </div>
                <div class="amount-value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
            </div>

            <div class="section-title">Data Pendaftar</div>
            <table class="info-table">
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td class="value">{{ $registration->name }}</td>
                </tr>
                <tr>
                    <td class="label">NISN</td>
                    <td class="value"><span class="mono">{{ $registration->nisn }}</span></td>
                </tr>
                <tr>
                    <td class="label">No. Pendaftaran</td>
                    <td class="value"><span class="mono">{{ $registration->registration_number }}</span></td>
                </tr>
                <tr>
                    <td class="label">Asal Sekolah</td>
                    <td class="value">{{ $registration->school_origin }}</td>
                </tr>
            </table>

            @if ($payment->raw_response && isset($payment->raw_response['transaction_id']))
            <div class="section-title">Referensi Midtrans</div>
            <div class="detail-grid">
                <div class="item">
                    <div class="d-label">Transaction ID</div>
                    <div class="d-value" style="font-family:'Courier New',monospace;font-size:10px;">{{ $payment->raw_response['transaction_id'] ?? '-' }}</div>
                </div>
                <div class="item">
                    <div class="d-label">Status</div>
                    <div class="d-value">{{ $payment->raw_response['transaction_status'] ?? '-' }}</div>
                </div>
                <div class="item">
                    <div class="d-label">Payment Type</div>
                    <div class="d-value">{{ $payment->raw_response['payment_type'] ?? '-' }}</div>
                </div>
            </div>
            @endif

            <hr class="separator">

            <div style="text-align:center;font-size:10px;color:#999;line-height:1.6;">
                Invoice ini sah dan diproses melalui sistem pembayaran online.<br>
                Terima kasih telah menyelesaikan pembayaran biaya daftar ulang siswa baru.
            </div>
        </div>

        <div class="footer">
            <div class="footer-left">
                Dicetak: {{ now()->format('d/m/Y H:i') }}<br>
                Invoice sah tanpa tanda tangan
            </div>
            <div class="footer-right">
                <div class="signature-line">
                    Bendahara SPMB {{ $settings->get('school_short_name', 'Sekolah') }}
                </div>
                <div class="signature-label">Administrasi Keuangan</div>
            </div>
        </div>

        <div class="bottom-bar"></div>
    </div>
</body>
</html>
