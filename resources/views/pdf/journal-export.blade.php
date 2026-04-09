<!DOCTYPE html>
<html>
<head>
    <title>Export Jurnal Guru</title>
    <style>
        @page { margin: 20px 30px; }
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            font-size: 10px;
        }
        
        .header { text-align: center; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; font-size: 12px; }
        
        .info-table { width: 100%; margin-bottom: 10px; font-weight: bold; font-size: 11px; }
        .info-table td { padding: 2px 0; }

        /* Tabel Jurnal Utama */
        table.jurnal-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; 
        }
        table.jurnal-table th, table.jurnal-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
            word-wrap: break-word;
        }
        table.jurnal-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        /* Lebar Spesifik Kolom yang Disempurnakan */
        .col-jam { width: 9%; text-align: center; } 
        .col-kelas { width: 5%; text-align: center; font-weight: bold; }
        .col-mapel { width: 12%; font-weight: bold;}
        
        /* Kolom Absensi (Disesuaikan agar muat 3 pilar angka) */
        .col-hadir { width: 3%; text-align: center; }
        .col-absen { width: 3%; text-align: center; }
        .col-jml { width: 3%; text-align: center; }
        .col-nama { width: 14%; font-size: 9px; }
        
        .col-materi { width: 18%; }
        .col-kegiatan { width: 17%; }
        .col-catatan { width: 15%; font-style: italic; }

        /* Pemisah Hari */
        .row-hari { background-color: #e8e8e8; font-weight: bold; font-size: 11px; text-align: left; }
    </style>
</head>
<body>

    <div class="header">
        <h1>JURNAL HARIAN GURU</h1>
        <p>SMA NEGERI 1 REMBANG</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">Nama Guru</td>
            <td width="2%">:</td>
            <td width="33%">{{ $user->name }}</td>
            <td width="15%">Periode Cetak</td>
            <td width="2%">:</td>
            <td width="33%">{{ $startDate }} s/d {{ $endDate }}</td>
        </tr>
    </table>

    <table class="jurnal-table">
        <thead>
            <tr>
                <th class="col-jam" rowspan="2">Jam & Waktu</th>
                <th class="col-kelas" rowspan="2">Kelas</th>
                <th class="col-mapel" rowspan="2">Mata Pelajaran</th>
                <th colspan="4">Kehadiran Siswa</th> <th class="col-materi" rowspan="2">Materi</th>
                <th class="col-kegiatan" rowspan="2">Kegiatan</th>
                <th class="col-catatan" rowspan="2">Catatan</th>
            </tr>
            <tr>
                <th class="col-hadir">H</th>
                <th class="col-absen">TH</th>
                <th class="col-jml">Jml</th> <th class="col-nama">Nama Yang Tidak Hadir</th>
            </tr>
        </thead>
        <tbody>
            @if(count($groupedJournals) == 0)
                <tr>
                    <td colspan="10" style="text-align: center; padding: 20px;">Tidak ada riwayat jurnal pada rentang tanggal tersebut.</td>
                </tr>
            @else
                @php
                    $periods = [
                        1 => ['start' => '07:00', 'end' => '07:45'],
                        2 => ['start' => '07:45', 'end' => '08:30'],
                        3 => ['start' => '08:30', 'end' => '09:15'],
                        4 => ['start' => '09:15', 'end' => '10:00'],
                        5 => ['start' => '10:15', 'end' => '11:00'],
                        6 => ['start' => '11:00', 'end' => '11:45'],
                        7 => ['start' => '12:30', 'end' => '13:15'],
                        8 => ['start' => '13:15', 'end' => '14:00'],
                        9 => ['start' => '14:00', 'end' => '14:45'],
                        10 => ['start' => '14:45', 'end' => '15:30']
                    ];
                @endphp

                @foreach($groupedJournals as $date => $journals)
                    <tr>
                        <td colspan="10" class="row-hari">
                            {{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }}
                        </td>
                    </tr>
                    
                    @foreach($journals as $j)
                        @php
                            $startStr = substr($j->schedule->start_time, 0, 5);
                            $endStr = substr($j->schedule->end_time, 0, 5);
                            $startPeriod = null;
                            $endPeriod = null;

                            foreach ($periods as $pKey => $pData) {
                                if ($startStr >= $pData['start'] && $startStr < $pData['end']) {
                                    $startPeriod = $pKey;
                                }
                                if ($endStr > $pData['start'] && $endStr <= $pData['end']) {
                                    $endPeriod = $pKey;
                                }
                            }

                            $jamText = "Jam ke -";
                            if ($startPeriod && $endPeriod) {
                                $jamText = "Jam ke " . ($startPeriod == $endPeriod ? $startPeriod : $startPeriod . "-" . $endPeriod);
                            }

                            // --- LOGIKA MENGHITUNG ABSENSI ---
                            $totalSiswa = $j->attendances->count();
                            $hadir = $j->attendances->where('status', 'hadir')->count();
                            $tidakHadir = $totalSiswa - $hadir;
                            
                            $siswaAbsen = $j->attendances->where('status', '!=', 'hadir')->map(function($absen) {
                                return $absen->student->name . ' (' . ucfirst($absen->status) . ')';
                            })->implode(', ');
                        @endphp
                        <tr>
                            <td class="col-jam">
                                <strong>{{ $jamText }}</strong><br>
                                <span style="font-size: 8px; color: #555;">{{ $startStr }} - {{ $endStr }}</span>
                            </td>

                            <td class="col-kelas">{{ $j->schedule->classroom->name }}</td>
                            <td class="col-mapel">{{ $j->schedule->subject->name }}</td>
                            
                            <td class="col-hadir">{{ $hadir }}</td>
                            <td class="col-absen">{{ $tidakHadir }}</td>
                            <td class="col-jml"><strong>{{ $totalSiswa }}</strong></td> <td class="col-nama">{{ $tidakHadir > 0 ? $siswaAbsen : '-' }}</td>
                            
                            <td class="col-materi">{{ $j->description ?? '-' }}</td>
                            <td class="col-kegiatan">{{ $j->activity_type ?? '-' }}</td>
                            <td class="col-catatan">{{ $j->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endif
        </tbody>
    </table>

</body>
</html>