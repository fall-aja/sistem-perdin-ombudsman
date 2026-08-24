<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PPA - {{ $perdin->nomor ?? '' }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; color:#111; }
        .header { display:flex; align-items:center; justify-content:space-between; }
        .logo { width:70px }
        .title { text-align:center }
        table { width:100%; border-collapse:collapse; margin-top:12px }
        th, td { border:1px solid #333; padding:6px; font-size:11px }
        th { background:#222; color:#fff; }
        .no-border td { border: none }
        .small { font-size:10px }
        .sign { margin-top:30px; text-align:center }
    </style>
</head>
<body>
    <div class="header">
        <div><img src="{{ public_path('images/ori_square.png') }}" class="logo" alt="logo"></div>
        <div class="title">
            <div style="font-weight:700">PERMOHONAN PEMBEBANAN ANGGARAN</div>
            <div class="small">Permintaan dan pembebanan anggaran biaya perjalanan dinas</div>
        </div>
        <div style="width:70px"></div>
    </div>

    <table class="small no-border">
        <tr>
            <td>Nomor</td><td>{{ $perdin->nomor }}</td>
            <td>Tanggal ST</td><td>{{ optional($perdin->tanggal_st)->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <td>Maksud Perjalanan</td><td colspan="3">{{ $perdin->maksud_perjalanan }}</td>
        </tr>
        <tr>
            <td>Pembebanan Anggaran</td><td colspan="3">{{ $perdin->pembebanan_anggaran }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>No</th><th>Nama</th><th>Jabatan</th><th>Dari</th><th>Ke</th><th>Tgl Mulai</th><th>Tgl Selesai</th><th>Hari</th><th>Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($perdin->travelers as $i => $t)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $t->nama }}</td>
                    <td>{{ $t->jabatan }}</td>
                    <td>{{ $t->dari }}</td>
                    <td>{{ $t->ke }}</td>
                    <td>{{ optional($t->tanggal_mulai)->format('d-m-Y') }}</td>
                    <td>{{ optional($t->tanggal_sampai)->format('d-m-Y') }}</td>
                    <td class="text-right">{{ $t->hari }}</td>
                    <td class="text-right">{{ number_format($t->jumlah,0,',','.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="8" style="text-align:right;font-weight:700">Jumlah</td>
                <td class="text-right font-bold">{{ number_format($perdin->total_biaya,0,',','.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="grid" style="display:flex; gap:20px; margin-top:30px">
        <div style="flex:1; text-align:center">
            <div class="small">Diajukan oleh</div>
            <div class="sign">&nbsp;</div>
            <div style="font-weight:700">{{ $perdin->nama_pengaju }}</div>
            <div class="small">{{ $perdin->nip_pengaju }}</div>
        </div>

        <div style="flex:1; text-align:center">
            <div class="small">Mengetahui</div>
            <div class="sign">&nbsp;</div>
            <div style="font-weight:700">{{ $perdin->nama_mengetahui }}</div>
        </div>

        <div style="flex:1; text-align:center">
            <div class="small">PPK</div>
            <div class="sign">&nbsp;</div>
            <div style="font-weight:700">{{ $perdin->nama_ppk }}</div>
            <div class="small">{{ $perdin->nip_ppk }}</div>
        </div>

        <div style="flex:1; text-align:center">
            <div class="small">Bendahara</div>
            <div class="sign">&nbsp;</div>
            <div style="font-weight:700">{{ $perdin->nama_bendahara }}</div>
            <div class="small">{{ $perdin->nip_bendahara }}</div>
        </div>
    </div>

</body>
</html>
