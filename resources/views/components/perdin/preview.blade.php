{{-- Preview HANYA visual (approksimasi tampilan Excel), file resmi tetap dari workbook --}}

<div x-show="activeSection === 'pertanggung_jawaban'">
    <table class="pd-doc-table">
        <tr class="border-b"><td class="pd-doc-label">Maksud Perjalanan</td><td class="p-2" x-text="form.maksud_perjalanan"></td></tr>
        <tr class="border-b"><td class="pd-doc-label">Surat Tugas</td><td class="p-2" x-text="form.surat_tugas_jabatan"></td></tr>
        <tr class="border-b"><td class="pd-doc-label">Nomor / Tanggal ST</td><td class="p-2" x-text="form.nomor_st + ' / ' + form.tanggal_st"></td></tr>
        <tr class="border-b"><td class="pd-doc-label">Pembebanan Anggaran</td><td class="p-2" x-text="form.pembebanan_anggaran"></td></tr>
    </table>

    <table class="pd-doc-table mt-4">
        <thead>
        <tr>
            <th class="p-1">No</th><th class="p-1">Nama</th><th class="p-1">Jabatan</th>
            <th class="p-1">Dari</th><th class="p-1">Ke</th><th class="p-1">Hari</th><th class="p-1">Jumlah</th>
        </tr>
        </thead>
        <tbody>
        <template x-for="(t, i) in travelers" :key="i">
            <tr class="border-b">
                <td class="p-1 text-center" x-text="i + 1"></td>
                <td class="p-1" x-text="t.nama"></td>
                <td class="p-1" x-text="t.jabatan"></td>
                <td class="p-1" x-text="t.dari"></td>
                <td class="p-1" x-text="t.ke"></td>
                <td class="p-1 text-center" x-text="t.hari"></td>
                <td class="p-1 text-right" x-text="formatRupiah(travelerTotal(t))"></td>
            </tr>
        </template>
        <tr class="pd-doc-total">
            <td colspan="6" class="p-1 text-right">Jumlah</td>
            <td class="p-1 text-right" x-text="formatRupiah(travelers.reduce((s,t)=>s+travelerTotal(t),0))"></td>
        </tr>
        </tbody>
    </table>
</div>

<div x-show="activeSection === 'ppa'" class="p-4 bg-white rounded-lg">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-sm font-semibold">Permintaan Pembebanan Anggaran (PPA)</h3>
            <div class="text-xs">@ <span x-text="form.maksud_perjalanan"></span></div>
        </div>
        <div>
            <button type="button" onclick="window.print()" class="pd-btn pd-btn-outline">Cetak Tampilan</button>
        </div>
    </div>

    <table class="pd-doc-table">
        <thead>
            <tr>
                <th class="p-1">No</th>
                <th class="p-1">Nama</th>
                <th class="p-1">Jabatan</th>
                <th class="p-1">Dari</th>
                <th class="p-1">Ke</th>
                <th class="p-1">Tgl Mulai</th>
                <th class="p-1">Tgl Selesai</th>
                <th class="p-1">Hari</th>
                <th class="p-1">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="(t, i) in travelers" :key="i">
                <tr class="border-b">
                    <td class="p-1 text-center" x-text="i + 1"></td>
                    <td class="p-1" x-text="t.nama"></td>
                    <td class="p-1" x-text="t.jabatan"></td>
                    <td class="p-1" x-text="t.dari"></td>
                    <td class="p-1" x-text="t.ke"></td>
                    <td class="p-1" x-text="t.tanggal_mulai"></td>
                    <td class="p-1" x-text="t.tanggal_sampai"></td>
                    <td class="p-1 text-center" x-text="t.hari"></td>
                    <td class="p-1 text-right" x-text="formatRupiah(travelerTotal(t))"></td>
                </tr>
            </template>
            <tr class="pd-doc-total">
                <td colspan="8" class="p-1 text-right">Jumlah</td>
                <td class="p-1 text-right" x-text="formatRupiah(travelers.reduce((s,t)=>s+travelerTotal(t),0))"></td>
            </tr>
        </tbody>
    </table>

    <div class="grid grid-cols-4 gap-4 mt-6 text-xs">
        <div class="text-center">
            <div class="font-semibold">Diajukan oleh</div>
            <div class="mt-12" style="height:60px"></div>
            <div x-text="form.nama_pengaju"></div>
            <div x-text="form.nip_pengaju"></div>
        </div>
        <div class="text-center">
            <div class="font-semibold">Mengetahui</div>
            <div class="mt-12" style="height:60px"></div>
            <div x-text="form.nama_mengetahui"></div>
        </div>
        <div class="text-center">
            <div class="font-semibold">PPK</div>
            <div class="mt-12" style="height:60px"></div>
            <div x-text="form.nama_ppk"></div>
            <div x-text="form.nip_ppk"></div>
        </div>
        <div class="text-center">
            <div class="font-semibold">Bendahara</div>
            <div class="mt-12" style="height:60px"></div>
            <div x-text="form.nama_bendahara"></div>
            <div x-text="form.nip_bendahara"></div>
        </div>
    </div>
</div>

<div x-show="activeSection === 'kwitansi'" class="text-xs">
    <table class="pd-doc-table mb-3">
        <tr class="border-b"><td class="pd-doc-label">Tahun Anggaran</td><td class="p-2" x-text="form.tahun_anggaran"></td></tr>
        <tr class="border-b"><td class="pd-doc-label">Nomor Bukti</td><td class="p-2" x-text="form.nomor_bukti_kwitansi"></td></tr>
        <tr class="border-b"><td class="pd-doc-label">M A K</td><td class="p-2" x-text="form.mak"></td></tr>
        <tr class="border-b"><td class="pd-doc-label">Sudah terima dari</td><td class="p-2" x-text="form.sudah_terima_dari"></td></tr>
        <tr class="border-b"><td class="pd-doc-label">Jumlah uang</td><td class="p-2" x-text="formatRupiah(form.jumlah_uang_kwitansi || travelers.reduce((s,t)=>s+travelerTotal(t),0))"></td></tr>
        <tr class="border-b"><td class="pd-doc-label">Terbilang</td><td class="p-2" x-text="''"></td></tr>
        <tr class="border-b"><td class="pd-doc-label">Untuk pembayaran</td><td class="p-2" x-text="form.untuk_pembayaran || form.maksud_perjalanan"></td></tr>
        <tr class="border-b"><td class="pd-doc-label">Nama Bendahara</td><td class="p-2" x-text="form.nama_bendahara"></td></tr>
        <tr><td class="pd-doc-label">NIP Bendahara</td><td class="p-2" x-text="form.nip_bendahara"></td></tr>
    </table>
</div>

<div x-show="activeSection === 'rincian'" class="text-xs">
    <table class="pd-doc-table mb-3">
        <tr class="border-b"><td class="pd-doc-label">Lampiran SPPD No.</td><td class="p-2" x-text="form.lampiran_sppd_no"></td></tr>
        <tr class="border-b"><td class="pd-doc-label">Tanggal SPPD</td><td class="p-2" x-text="form.tanggal_sppd"></td></tr>
        <tr><td class="pd-doc-label">Nama yang Bepergian</td><td class="p-2" x-text="form.nama_bepergian || form.nama_pengaju || travelers[0]?.nama"></td></tr>
    </table>
    <table class="pd-doc-table">
        <thead>
        <tr>
            <th class="p-1">No</th><th class="p-1">Perincian Biaya</th><th class="p-1">Jumlah/Hari</th><th class="p-1">Harga</th>
        </tr>
        </thead>
        <tbody>
        <template x-for="(r, i) in rincianItems" :key="i">
            <tr class="border-b">
                <td class="p-1 text-center" x-text="i + 1"></td>
                <td class="p-1">
                    <div x-text="r.uraian"></div>
                    <template x-if="r.keterangan">
                        <div class="text-[11px] text-[#6b6455] mt-1">- <span x-text="r.keterangan"></span></div>
                    </template>
                </td>
                <td class="p-1 text-center" x-text="r.jumlah_satuan ? r.jumlah_satuan + ' Hari' : ''"></td>
                <td class="p-1 text-right" x-text="formatRupiah(r.harga_satuan)"></td>
            </tr>
        </template>
        <tr class="pd-doc-total">
            <td colspan="3" class="p-1 text-right">Jumlah</td>
            <td class="p-1 text-right" x-text="formatRupiah(rincianItems.reduce((s,r)=>s+(r.harga_satuan||0),0))"></td>
        </tr>
        </tbody>
    </table>

    <div class="mt-4 text-xs text-[#1b2332]">
        <div class="font-semibold mb-2">Keterangan:</div>
        <template x-if="rincianNotes().length">
            <template x-for="(note, index) in rincianNotes()" :key="index">
                <div class="mb-1" x-text="note"></div>
            </template>
        </template>
        <template x-if="!rincianNotes().length">
            <div class="text-[#6b6455]">Tidak ada keterangan tambahan.</div>
        </template>
    </div>
</div>

<div x-show="activeSection === 'dpr'" class="text-xs">
    <table class="pd-doc-table mb-3">
        <tr class="border-b"><td class="pd-doc-label">Nama</td><td class="p-2" x-text="form.dpr_nama"></td></tr>
        <tr class="border-b"><td class="pd-doc-label">NIP</td><td class="p-2" x-text="form.dpr_nip"></td></tr>
        <tr class="border-b"><td class="pd-doc-label">Jabatan</td><td class="p-2" x-text="form.dpr_jabatan"></td></tr>
        <tr class="border-b"><td class="pd-doc-label">Nomor SPD</td><td class="p-2" x-text="form.nomor_spd"></td></tr>
        <tr><td class="pd-doc-label">Tanggal SPD</td><td class="p-2" x-text="form.tanggal_spd"></td></tr>
    </table>
    <table class="pd-doc-table">
        <thead>
        <tr><th class="p-1">No</th><th class="p-1">Uraian</th><th class="p-1">Jumlah</th></tr>
        </thead>
        <tbody>
        <template x-for="(d, i) in dprItems" :key="i">
            <tr class="border-b">
                <td class="p-1 text-center" x-text="i + 1"></td>
                <td class="p-1" x-text="d.uraian"></td>
                <td class="p-1 text-right" x-text="formatRupiah(d.jumlah)"></td>
            </tr>
        </template>
        <tr class="pd-doc-total">
            <td colspan="2" class="p-1 text-right">Jumlah</td>
            <td class="p-1 text-right" x-text="formatRupiah(dprItems.reduce((s,d)=>s+(d.jumlah||0),0))"></td>
        </tr>
        </tbody>
    </table>
</div>

<div x-show="activeSection === 'pernyataan'" class="space-y-4 text-xs">
    <div class="pd-item-card">
        <div class="font-semibold text-[#1b2332] mb-2">Preview Pernyataan</div>
        <template x-if="form.pernyataan_teks">
            <p class="whitespace-pre-line" x-text="form.pernyataan_teks"></p>
        </template>
        <template x-if="!form.pernyataan_teks && form.pernyataan_tidak_menggunakan_kendaraan">
            <p>Peserta tidak menggunakan kendaraan dinas.</p>
        </template>
        <template x-if="!form.pernyataan_teks && !form.pernyataan_tidak_menggunakan_kendaraan">
            <p x-text="form.maksud_perjalanan ? 'Dalam ' + form.maksud_perjalanan : 'Dalam ...'"></p>
        </template>
    </div>
    <table class="pd-doc-table">
        <thead><tr><th class="p-1">No</th><th class="p-1">Nama</th><th class="p-1">Jabatan</th></tr></thead>
        <tbody>
        <template x-for="(t, i) in travelers" :key="i">
            <tr class="border-b">
                <td class="p-1 text-center" x-text="i + 1"></td>
                <td class="p-1" x-text="t.nama"></td>
                <td class="p-1" x-text="t.jabatan"></td>
            </tr>
        </template>
        </tbody>
    </table>
</div>