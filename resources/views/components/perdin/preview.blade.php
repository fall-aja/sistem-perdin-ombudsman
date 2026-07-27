{{-- Preview HANYA visual (approksimasi tampilan Excel), file resmi tetap dari workbook --}}

<div x-show="activeSection === 'pertanggung_jawaban' || activeSection === 'ppa'">
    <table class="w-full text-xs border border-slate-300">
        <tr class="border-b"><td class="p-2 font-medium bg-slate-50 w-1/3">Maksud Perjalanan</td><td class="p-2" x-text="form.maksud_perjalanan"></td></tr>
        <tr class="border-b"><td class="p-2 font-medium bg-slate-50">Surat Tugas</td><td class="p-2" x-text="form.surat_tugas_jabatan"></td></tr>
        <tr class="border-b"><td class="p-2 font-medium bg-slate-50">Nomor / Tanggal ST</td><td class="p-2" x-text="form.nomor_st + ' / ' + form.tanggal_st"></td></tr>
        <tr class="border-b"><td class="p-2 font-medium bg-slate-50">Pembebanan Anggaran</td><td class="p-2" x-text="form.pembebanan_anggaran"></td></tr>
    </table>

    <table class="w-full text-xs border border-slate-300 mt-4">
        <thead>
        <tr class="bg-slate-800 text-white">
            <th class="p-1">No</th><th class="p-1">Nama</th><th class="p-1">Jabatan</th>
            <th class="p-1">Dari</th><th class="p-1">Ke</th><th class="p-1">Hari</th><th class="p-1">Jumlah</th>
        </tr>
        </thead>
        <tbody>
        <template x-for="(t, i) in travelers" :key="i">
            <tr class="border-b odd:bg-white even:bg-slate-50">
                <td class="p-1 text-center" x-text="i + 1"></td>
                <td class="p-1" x-text="t.nama"></td>
                <td class="p-1" x-text="t.jabatan"></td>
                <td class="p-1" x-text="t.dari"></td>
                <td class="p-1" x-text="t.ke"></td>
                <td class="p-1 text-center" x-text="t.hari"></td>
                <td class="p-1 text-right" x-text="formatRupiah(travelerTotal(t))"></td>
            </tr>
        </template>
        <tr class="font-semibold bg-slate-100">
            <td colspan="6" class="p-1 text-right">Jumlah</td>
            <td class="p-1 text-right" x-text="formatRupiah(travelers.reduce((s,t)=>s+travelerTotal(t),0))"></td>
        </tr>
        </tbody>
    </table>
</div>

<div x-show="activeSection === 'kwitansi'" class="text-xs">
    <table class="w-full border border-slate-300">
        <tr class="border-b"><td class="p-2 font-medium bg-slate-50 w-1/3">Sudah terima dari</td><td class="p-2" x-text="form.sudah_terima_dari"></td></tr>
        <tr class="border-b"><td class="p-2 font-medium bg-slate-50">Jumlah uang</td><td class="p-2" x-text="formatRupiah(form.jumlah_uang_kwitansi || travelers.reduce((s,t)=>s+travelerTotal(t),0))"></td></tr>
        <tr class="border-b"><td class="p-2 font-medium bg-slate-50">Untuk pembayaran</td><td class="p-2" x-text="form.maksud_perjalanan"></td></tr>
    </table>
</div>

<div x-show="activeSection === 'rincian'" class="text-xs">
    <table class="w-full border border-slate-300">
        <thead>
        <tr class="bg-slate-800 text-white">
            <th class="p-1">No</th><th class="p-1">Perincian Biaya</th><th class="p-1">Jumlah/Hari</th><th class="p-1">Harga</th><th class="p-1">Total</th>
        </tr>
        </thead>
        <tbody>
        <template x-for="(r, i) in rincianItems" :key="i">
            <tr class="border-b odd:bg-white even:bg-slate-50">
                <td class="p-1 text-center" x-text="i + 1"></td>
                <td class="p-1" x-text="r.uraian"></td>
                <td class="p-1 text-center" x-text="r.jumlah_satuan"></td>
                <td class="p-1 text-right" x-text="formatRupiah(r.harga_satuan)"></td>
                <td class="p-1 text-right" x-text="formatRupiah((r.jumlah_satuan||0) * (r.harga_satuan||0))"></td>
            </tr>
        </template>
        <tr class="font-semibold bg-slate-100">
            <td colspan="4" class="p-1 text-right">Jumlah</td>
            <td class="p-1 text-right" x-text="formatRupiah(rincianItems.reduce((s,r)=>s+(r.jumlah_satuan||0)*(r.harga_satuan||0),0))"></td>
        </tr>
        </tbody>
    </table>
</div>

<div x-show="activeSection === 'dpr'" class="text-xs">
    <table class="w-full border border-slate-300">
        <thead>
        <tr class="bg-slate-800 text-white"><th class="p-1">No</th><th class="p-1">Uraian</th><th class="p-1">Jumlah</th></tr>
        </thead>
        <tbody>
        <template x-for="(d, i) in dprItems" :key="i">
            <tr class="border-b odd:bg-white even:bg-slate-50">
                <td class="p-1 text-center" x-text="i + 1"></td>
                <td class="p-1" x-text="d.uraian"></td>
                <td class="p-1 text-right" x-text="formatRupiah(d.jumlah)"></td>
            </tr>
        </template>
        <tr class="font-semibold bg-slate-100">
            <td colspan="2" class="p-1 text-right">Jumlah</td>
            <td class="p-1 text-right" x-text="formatRupiah(dprItems.reduce((s,d)=>s+(d.jumlah||0),0))"></td>
        </tr>
        </tbody>
    </table>
</div>

<div x-show="activeSection === 'pernyataan'" class="text-xs">
    <p class="mb-2 text-slate-600" x-text="'Kami yang bertandatangan di bawah ini, dalam ' + (form.maksud_perjalanan || '...')"></p>
    <table class="w-full border border-slate-300">
        <thead><tr class="bg-slate-800 text-white"><th class="p-1">No</th><th class="p-1">Nama</th><th class="p-1">Jabatan</th></tr></thead>
        <tbody>
        <template x-for="(t, i) in travelers" :key="i">
            <tr class="border-b odd:bg-white even:bg-slate-50">
                <td class="p-1 text-center" x-text="i + 1"></td>
                <td class="p-1" x-text="t.nama"></td>
                <td class="p-1" x-text="t.jabatan"></td>
            </tr>
        </template>
        </tbody>
    </table>
</div>
