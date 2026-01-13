<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Peminjaman - {{ $loan->asset->code }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 40px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            margin-bottom: 30px;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }

        .info-row {
            display: flex;
            margin-bottom: 10px;
        }

        .info-label {
            width: 150px;
            font-weight: bold;
        }

        .info-value {
            flex: 1;
        }

        .asset-details {
            margin-top: 30px;
            border: 1px solid #eee;
            padding: 20px;
            background: #f9f9f9;
        }

        .asset-details h3 {
            margin-top: 0;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 200px;
            text-align: center;
        }

        .signature-space {
            height: 80px;
        }

        .footer {
            margin-top: 50px;
            font-size: 10px;
            text-align: center;
            color: #777;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .container {
                border: none;
                width: 100%;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()"
            style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Cetak Dokumen
        </button>
        <button onclick="window.close()"
            style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Tutup
        </button>
    </div>

    <div class="container">
        <div class="header">
            <h1>Bukti Peminjaman Aset</h1>
            <p>{{ App\Models\Setting::get('site_name', 'Sistem Manajemen Aset IMC') }}</p>
        </div>

        <div class="info-section">
            <div class="info-row">
                <div class="info-label">ID Peminjaman</div>
                <div class="info-value">#: {{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Peminjam</div>
                <div class="info-value">{{ $loan->user->name }} ({{ $loan->user->employee_id ?? '-' }})</div>
            </div>
            <div class="info-row">
                <div class="info-label">Departemen</div>
                <div class="info-value">{{ $loan->user->department ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Pinjam</div>
                <div class="info-value">{{ $loan->loan_date->translatedFormat('d F Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Estimasi Kembali</div>
                <div class="info-value">{{ $loan->due_date->translatedFormat('d F Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value" style="text-transform: capitalize;">{{ $loan->status }}</div>
            </div>
        </div>

        <div class="asset-details">
            <h3>Rincian Aset</h3>
            <div class="info-row">
                <div class="info-label">Kode Aset</div>
                <div class="info-value text-primary" style="font-weight: bold;">{{ $loan->asset->code }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nama Aset</div>
                <div class="info-value">{{ $loan->asset->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Kategori</div>
                <div class="info-value">{{ $loan->asset->category->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Asal Lokasi</div>
                <div class="info-value">{{ $loan->asset->location->name }}</div>
            </div>
            <div class="info-row" style="margin-top: 10px;">
                <div class="info-label">Tujuan Peminjaman</div>
                <div class="info-value">{{ $loan->purpose }}</div>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <p><strong>Syarat & Ketentuan:</strong></p>
            <ol>
                <li>Peminjam bertanggung jawab penuh atas keutuhan dan keamanan aset yang dipinjam.</li>
                <li>Aset harus dikembalikan paling lambat pada tanggal estimasi yang tertera.</li>
                <li>Kerusakan atau kehilangan yang disebabkan kelalaian peminjam menjadi tanggung jawab bapak/ibu ybs.
                </li>
            </ol>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <p>Peminjam,</p>
                <div class="signature-space"></div>
                <p><strong>( {{ $loan->user->name }} )</strong></p>
            </div>
            <div class="signature-box">
                <p>Menyetujui,</p>
                <div class="signature-space"></div>
                <p><strong>( {{ $loan->approver->name ?? 'Administrator' }} )</strong></p>
            </div>
        </div>

        <div class="footer">
            <p>Dicetak otomatis oleh Sistem Manajemen Aset pada {{ now()->translatedFormat('d F Y H:i:s') }}</p>
        </div>
    </div>
</body>

</html>