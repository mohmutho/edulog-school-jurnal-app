<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Mengajar</title>
    <style>
        /* Setup Halaman & Font */
        @page { margin: 30px; }
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            color: #000;
        }

        /* Header Layout */
        .top-left { font-size: 11px; margin-bottom: -15px; }
        .header { text-align: center; margin-bottom: 5px; }
        .school-name { font-size: 16px; margin-bottom: 5px; }
        .teacher-name { font-size: 38px; }

        /* Tabel Utama */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Memaksa ukuran kolom sama rata */
            border: 3px solid #000; /* Border luar lebih tebal */
        }
        th, td {
            border: 1px solid #000;
            vertical-align: top; /* Tulisan selalu mulai dari atas */
        }

        /* Pengaturan Header Kolom (Jam ke-) */
        th {
            height: 45px;
            text-align: center;
            font-weight: normal;
        }
        .col-hari { width: 9%; } /* Kolom Hari sedikit lebih besar */
        .col-jam { width: 9.1%; } /* 10 Kolom Jam dibagi rata */
        
        .jam-number { font-size: 18px; display: block; margin-top: 5px; }
        .jam-time { font-size: 8px; display: block; margin-top: 2px; }

        /* Pengaturan Baris Hari */
        .nama-hari {
            font-size: 32px;
            text-align: center;
            vertical-align: middle;
            height: 100px; /* Tinggi seragam untuk setiap baris */
        }

        /* Isi Jadwal (Kotak Jam) */
        .slot-kosong {
            height: 100px;
        }
        .slot-isi {
            height: 100px;
            padding: 4px;
            position: relative; /* Agar posisi mapel bisa di pojok */
        }
        .mapel {
            font-size: 10px;
            text-align: left;
            /* Mengambil singkatan mapel, contoh: Fisika -> Fis */
        }
        .kelas {
            font-size: 26px;
            text-align: center;
            margin-top: 15px; /* Mendorong kelas ke tengah */
        }

        /* Footer */
        .footer {
            width: 100%;
            margin-top: 5px;
            font-size: 10px;
        }
        .footer-left { float: left; }
        .footer-right { float: right; }
    </style>
</head>
<body>
    <div class="top-left">Minha Escola</div>
    <div class="header">
        <div class="school-name">SMA NEGERI 1 REMBANG</div>
        <div class="teacher-name">Teacher {{ $user->name }}</div>
    </div>

    <table>
        <tr>
            <th class="col-hari"></th>
            @foreach($periods as $pKey => $pData)
                <th class="col-jam">
                    <span class="jam-number">{{ $pKey }}</span>
                    <span class="jam-time">{{ $pData['start'] }} - {{ $pData['end'] }}</span>
                </th>
            @endforeach
        </tr>

        @foreach($hariMap as $dayKey => $dayName)
        <tr>
            <td class="nama-hari">{{ $dayName }}</td>
            
            @foreach($periods as $pKey => $pData)
                @php $cell = $grid[$dayKey][$pKey]; @endphp
                
                @if($cell === 'skip')
                    @continue 
                @elseif($cell !== null)
                    <td class="slot-isi" colspan="{{ $cell['colspan'] }}">
                        <div class="mapel">{{ $cell['schedule']->subject->abbreviation }}</div>
                        <div class="kelas">{{ $cell['schedule']->classroom->name }}</div>
                    </td>
                @else
                    <td class="slot-kosong"></td>
                @endif
            @endforeach
            
        </tr>
        @endforeach
    </table>

    <div class="footer">
        <div class="footer-left">© 2026 Tim Kurikulum Smansa Rembang</div>
        <div class="footer-right">aSc Timetables</div>
    </div>
</body>
</html>