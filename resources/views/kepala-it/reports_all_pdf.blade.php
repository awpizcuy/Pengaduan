<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Laporan Selesai</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
        }
        .report-container {
            border: 1px solid #eee;
            padding: 15px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .report-container:not(:last-child) {
            page-break-after: always;
        }
        h1 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 18px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        h2 {
            font-size: 14px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-top: 0;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .details-table th, .details-table td {
            border: 1px solid #ddd;
            padding: 7px;
            text-align: left;
            vertical-align: top;
        }
        .details-table th {
            background-color: #f9f9f9;
            width: 30%;
        }
        .rating-box {
            border: 1px solid #f59e0b;
            background-color: #fffbeb;
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
        }
        .rating-box .stars {
            font-size: 1.2em;
            color: #f59e0b;
        }
        .image-box {
            margin-top: 15px;
            text-align: center;
        }
        .image-box img {
            max-width: 80%;
            height: auto;
            border: 1px solid #ddd;
            padding: 3px;
            border-radius: 4px;
        }
        .image-label {
            font-weight: bold;
            margin-top: 5px;
            display: block;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 12px;
            color: #fff;
            text-transform: uppercase;
        }
        .status-completed { background-color: #16a34a; } /* Green */
        .status-rated { background-color: #f59e0b; } /* Amber */
        .whitespace-pre-wrap {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>

    <h1>Rekapitulasi Laporan Selesai</h1>

    @forelse($reports as $report)
        <div class="report-container">
            <h2>Laporan #{{ $report->id }} - {{ $report->kategori }}</h2>

            <table class="details-table">
                <tr>
                    <th>Status</th>
                    <td>
                        @if($report->status == 'completed')
                            <span class="status-badge status-completed">Completed</span>
                        @elseif($report->status == 'rated')
                            <span class="status-badge status-rated">Rated</span>
                        @else
                            {{ ucfirst($report->status) }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Pelapor</th>
                    <td>{{ $report->nama_pelapor }} ({{ $report->reporter->email ?? 'N/A' }})</td>
                </tr>
                <tr>
                    <th>Tanggal Pengajuan</th>
                    <td>{{ \Carbon\Carbon::parse($report->tanggal_pengajuan)->format('d F Y, H:i') }}</td>
                </tr>
                <tr>
                    <th>Deskripsi Masalah</th>
                    <td class="whitespace-pre-wrap">{{ $report->deskripsi_pengajuan }}</td>
                </tr>
                <tr>
                    <th>Teknisi yang Ditugaskan</th>
                    <td>{{ $report->technician->name ?? 'N/A' }}</td>
                </tr>
                @if($report->status_note)
                <tr>
                    <th>Catatan dari Kepala IT</th>
                    <td class="whitespace-pre-wrap">{{ $report->status_note }}</td>
                </tr>
                @endif
            </table>

            @if($report->resolution)
                <h2 style="margin-top: 20px;">Hasil Penyelesaian</h2>
                <table class="details-table">
                    <tr>
                        <th>Tanggal Selesai</th>
                        <td>{{ $report->resolution->completion_date ? \Carbon\Carbon::parse($report->resolution->completion_date)->format('d F Y, H:i') : 'N/A' }}</td>
                    </tr>
                    @if($report->resolution->barang)
                    <tr>
                        <th>Barang yang Digunakan/Diganti</th>
                        <td>{{ $report->resolution->barang }} (Jumlah: {{ $report->resolution->qty ?? 1 }})</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Deskripsi Pekerjaan</th>
                        <td class="whitespace-pre-wrap">{{ $report->resolution->deskripsi_pekerjaan ?? 'N/A' }}</td>
                    </tr>
                </table>

                @if($report->resolution->rating)
                <div class="rating-box">
                    <strong>Rating dari Pelapor:</strong>
                    <span class="stars">
                        @for($i = 0; $i < 5; $i++)
                            {{ $i < $report->resolution->rating ? '★' : '☆' }}
                        @endfor
                    </span>
                    <span>({{ $report->resolution->rating }}/5)</span>
                    @if($report->resolution->rating_feedback)
                        <p style="margin-top: 5px; margin-bottom: 0;"><i>"{{ $report->resolution->rating_feedback }}"</i></p>
                    @endif
                </div>
                @endif

                <table style="width: 100%; margin-top: 20px; border: none;">
                    <tr style="text-align: center;">
                        @php
                            $beforePath = $report->resolution->foto_before;
                            $beforeUrl = $beforePath && Storage::disk('public')->exists($beforePath) ? Storage::disk('public')->path($beforePath) : null;
                        @endphp
                        @if($beforeUrl)
                        <td style="width: 50%;">
                            <span class="image-label">Foto Sebelum</span>
                            <div class="image-box">
                                <img src="{{ $beforeUrl }}" alt="Foto Sebelum">
                            </div>
                        </td>
                        @endif

                        @php
                            $afterPath = $report->resolution->foto_after;
                            $afterUrl = $afterPath && Storage::disk('public')->exists($afterPath) ? Storage::disk('public')->path($afterPath) : null;
                        @endphp
                        @if($afterUrl)
                        <td style="width: 50%;">
                            <span class="image-label">Foto Sesudah</span>
                            <div class="image-box">
                                <img src="{{ $afterUrl }}" alt="Foto Sesudah">
                            </div>
                        </td>
                        @endif
                    </tr>
                </table>
            @endif
        </div>
    @empty
        <p>Tidak ada laporan selesai yang memiliki rating untuk ditampilkan.</p>
    @endforelse

</body>
</html>