{{-- Layout lebih horizontal (grid lebih lebar). Field Nama/NIP/Jabatan
     pakai <x-perdin.pegawai-picker> — ketik 1 huruf, dropdown muncul dari
     master data pegawai (DUK), klik untuk isi otomatis Nama+NIP+Jabatan.
     Field Kota tetap pakai datalist dari histori data yang pernah diisi. --}}

<datalist id="kotaList">
    <template x-for="n in suggestions.kota" :key="n">
        <option :value="n"></option>
    </template>
</datalist>

{{-- ================= PERTANGGUNGJAWABAN & PPA (data header sama) ================= --}}
<div x-show="activeSection === 'pertanggung_jawaban' || activeSection === 'ppa'" class="space-y-4">

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="text-xs font-medium text-slate-500">Nomor Surat</label>
            <input type="text" x-model="form.nomor" placeholder="mis. 123/ORI-PPA/VII/2026"
                class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Pembebanan Anggaran</label>
            <input type="text" x-model="form.pembebanan_anggaran" placeholder="524111 (Biaya Perjalanan Dinas Biasa)"
                class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
    </div>

    <div>
        <label class="text-xs font-medium text-slate-500">Maksud Perjalanan Dinas</label>
        <textarea x-model="form.maksud_perjalanan" rows="2" placeholder="Melaksanakan perjalanan dinas ke ..."
            class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2"></textarea>
    </div>

    <div class="grid grid-cols-4 gap-3">
        <div>
            <label class="text-xs font-medium text-slate-500">Surat Tugas (Jabatan)</label>
            <input type="text" x-model="form.surat_tugas_jabatan" placeholder="Anggota Ombudsman RI"
                class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Nomor Surat Tugas</label>
            <input type="text" x-model="form.nomor_st"
                class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Tanggal Surat Tugas</label>
            <input type="date" x-model="form.tanggal_st"
                class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Nomor Rencana Kerja</label>
            <input type="text" x-model="form.nomor_rk"
                class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="text-xs font-medium text-slate-500">Kota Tanda Tangan</label>
            <input type="text" x-model="form.kota_tanda_tangan" list="kotaList"
                class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Tanggal Tanda Tangan</label>
            <input type="date" x-model="form.tanggal_tanda_tangan"
                class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
    </div>

    <h4 class="font-medium text-sm pt-3 border-t border-slate-200">Penandatangan</h4>
    <div class="grid grid-cols-3 gap-3">
        <x-perdin.pegawai-picker label="Nama PPK" nama-model="form.nama_ppk" nip-model="form.nip_ppk" field-id-expr="'ppk'" />
        <x-perdin.pegawai-picker label="Nama Kabag Keuangan" nama-model="form.nama_kabag_keuangan" nip-model="form.nip_kabag_keuangan" field-id-expr="'kabag'" />
        <x-perdin.pegawai-picker label="Nama (Mengetahui)" nama-model="form.nama_mengetahui" field-id-expr="'mengetahui'" />
        <x-perdin.pegawai-picker label="Nama Pengaju" nama-model="form.nama_pengaju" nip-model="form.nip_pengaju" field-id-expr="'pengaju'" />
    </div>

    <h4 class="font-medium text-sm pt-3 border-t border-slate-200">Daftar Peserta</h4>
    <template x-for="(t, i) in travelers" :key="i">
        <div class="border border-slate-200 rounded-lg p-4 space-y-3 relative bg-slate-50/50">
            <button type="button" @click="removeTraveler(i)" class="absolute top-3 right-3 text-red-500 text-xs hover:underline">Hapus</button>

            <div class="grid grid-cols-4 gap-3">
                <x-perdin.pegawai-picker
                    label="Nama"
                    nama-model="t.nama"
                    jabatan-model="t.jabatan"
                    field-id-expr="'traveler-nama-' + i" />
                <div>
                    <label class="text-xs font-medium text-slate-500">Jabatan/Peran</label>
                    <input type="text" x-model="t.jabatan" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Eselon</label>
                    <input type="text" x-model="t.es" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Golongan</label>
                    <input type="text" x-model="t.gol" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
            </div>
            <div class="grid grid-cols-5 gap-3">
                <div>
                    <label class="text-xs font-medium text-slate-500">Dari</label>
                    <input type="text" x-model="t.dari" list="kotaList" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Ke</label>
                    <input type="text" x-model="t.ke" list="kotaList" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Tgl Mulai</label>
                    <input type="date" x-model="t.tanggal_mulai" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Tgl Selesai</label>
                    <input type="date" x-model="t.tanggal_sampai" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Hari</label>
                    <input type="number" x-model.number="t.hari" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-6 gap-3">
                <div>
                    <label class="text-xs font-medium text-slate-500">Uang Harian</label>
                    <input type="number" x-model.number="t.uang_harian" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Penginapan</label>
                    <input type="number" x-model.number="t.penginapan" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Represen</label>
                    <input type="number" x-model.number="t.represen" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Tiket</label>
                    <input type="number" x-model.number="t.tiket" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Transport</label>
                    <input type="number" x-model.number="t.transportasi" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Sewa Kendaraan</label>
                    <input type="number" x-model.number="t.sewa_kendaraan" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
            </div>

            <div class="text-xs text-slate-500 text-right font-medium" x-text="'Jumlah: ' + formatRupiah(travelerTotal(t))"></div>
        </div>
    </template>
    <button type="button" @click="addTraveler()" class="text-sm text-blue-600 font-medium hover:underline">+ Tambah Peserta</button>
</div>

{{-- ================= KWITANSI ================= --}}
<div x-show="activeSection === 'kwitansi'" class="space-y-4">
    <div class="grid grid-cols-3 gap-3">
        <div>
            <label class="text-xs font-medium text-slate-500">Tahun Anggaran</label>
            <input type="text" x-model="form.tahun_anggaran" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Nomor Bukti</label>
            <input type="text" x-model="form.nomor_bukti_kwitansi" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">M A K</label>
            <input type="text" x-model="form.mak" placeholder="mis. 5618.QAA.001.057.A"
                class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
    </div>
    <div>
        <label class="text-xs font-medium text-slate-500">Sudah Terima Dari</label>
        <input type="text" x-model="form.sudah_terima_dari" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="text-xs font-medium text-slate-500">Jumlah Uang (Rp)</label>
            <input type="number" x-model.number="form.jumlah_uang_kwitansi" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
            <p class="text-xs text-slate-400 mt-1">Kosongkan untuk otomatis dari total biaya peserta.</p>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <x-perdin.pegawai-picker label="Nama Bendahara" nama-model="form.nama_bendahara" nip-model="form.nip_bendahara" field-id-expr="'bendahara'" />
    </div>
</div>

{{-- ================= RINCIAN ================= --}}
<div x-show="activeSection === 'rincian'" class="space-y-4">
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="text-xs font-medium text-slate-500">Lampiran SPPD No.</label>
            <input type="text" x-model="form.lampiran_sppd_no" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Tanggal SPPD</label>
            <input type="date" x-model="form.tanggal_sppd" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
        <template x-for="(r, i) in rincianItems" :key="i">
        <div class="border border-slate-200 rounded-lg p-4 space-y-3 relative bg-slate-50/50">
            <button type="button" @click="removeRincianItem(i)" class="absolute top-3 right-3 text-red-500 text-xs hover:underline">Hapus</button>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-medium text-slate-500">Uraian</label>
                    <input type="text" x-model="r.uraian" placeholder="mis. Uang Harian" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Keterangan Tambahan</label>
                    <input type="text" x-model="r.keterangan_tambahan" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="text-xs font-medium text-slate-500">Jumlah/Hari</label>
                    <input type="number" x-model.number="r.jumlah_satuan" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Harga Satuan</label>
                    <input type="number" x-model.number="r.harga_satuan" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Keterangan</label>
                    <input type="text" x-model="r.keterangan" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
            </div>
        </div>
    </template>
    <button type="button" @click="addRincianItem()" class="text-sm text-blue-600 font-medium hover:underline">+ Tambah Rincian</button>
</div>

{{-- ================= DPR ================= --}}
<div x-show="activeSection === 'dpr'" class="space-y-4">
    <div class="grid grid-cols-3 gap-3">
        <x-perdin.pegawai-picker label="Nama" nama-model="form.dpr_nama" nip-model="form.dpr_nip" jabatan-model="form.dpr_jabatan" field-id-expr="'dpr'" />
        <div>
            <label class="text-xs font-medium text-slate-500">Jabatan</label>
            <input type="text" x-model="form.dpr_jabatan" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Nomor SPD</label>
            <input type="text" x-model="form.nomor_spd" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
    </div>
    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="text-xs font-medium text-slate-500">Tanggal SPD</label>
            <input type="date" x-model="form.tanggal_spd" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
    </div>

    <template x-for="(d, i) in dprItems" :key="i">
        <div class="border border-slate-200 rounded-lg p-4 flex gap-3 items-end bg-slate-50/50">
            <div class="flex-1">
                <label class="text-xs font-medium text-slate-500">Uraian</label>
                <input type="text" x-model="d.uraian" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
            </div>
            <div class="w-36">
                <label class="text-xs font-medium text-slate-500">Jumlah</label>
                <input type="number" x-model.number="d.jumlah" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
            </div>
            <button type="button" @click="removeDprItem(i)" class="text-red-500 text-xs hover:underline pb-2.5">Hapus</button>
        </div>
    </template>
    <button type="button" @click="addDprItem()" class="text-sm text-blue-600 font-medium hover:underline">+ Tambah Pengeluaran</button>
</div>

{{-- ================= PERNYATAAN ================= --}}
<div x-show="activeSection === 'pernyataan'" class="space-y-4">
    <p class="text-sm text-slate-500">
        Surat pernyataan tidak menggunakan kendaraan dinas otomatis diisi dari
        <strong>Maksud Perjalanan Dinas</strong> dan <strong>Daftar Peserta</strong> pada tab
        "Pertanggung Jawaban PERDIN" / "PPA PERDIN". Tidak ada input tambahan di sini.
    </p>
</div>