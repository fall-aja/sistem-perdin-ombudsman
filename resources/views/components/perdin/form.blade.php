{{-- Layout lebih horizontal (grid lebih lebar). Field Nama/NIP/Jabatan
     pakai <x-perdin.pegawai-picker> — ketik 1 huruf, dropdown muncul dari
     master data pegawai (DUK), klik untuk isi otomatis Nama+NIP+Jabatan.
     Field Kota tetap pakai datalist dari histori data yang pernah diisi. --}}

<datalist id="kotaList">
    <template x-for="n in suggestions.kota" :key="n">
        <option :value="n"></option>
    </template>
</datalist>

<datalist id="sbmProvinceList">
    <template x-for="item in sbmTariffs" :key="item.name">
        <option :value="item.name"></option>
    </template>
</datalist>

{{-- ================= PERTANGGUNGJAWABAN & PPA (data header sama) ================= --}}
<div x-show="activeSection === 'pertanggung_jawaban' || activeSection === 'ppa'" class="space-y-4 rounded-3xl border border-slate-200 bg-slate-50/80 p-4 shadow-sm">
    <div class="flex items-center justify-between gap-3 rounded-2xl bg-slate-100 px-4 py-3">
        <div>
            <h3 class="text-sm font-semibold text-slate-800">Pertanggung Jawaban / PPA</h3>
            <p class="text-xs text-slate-500">Isi data perjalanan, penandatangan, dan peserta.</p>
        </div>
    </div>

    <div>
        <label class="text-xs font-medium text-slate-500">Nomor</label>
        <input type="text" x-model="form.nomor"
            class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
    </div>

    <div>
        <label class="text-xs font-medium text-slate-500">Maksud Perjalanan Dinas</label>
        <div class="relative">
            <textarea x-model="form.maksud_perjalanan" x-effect="if (!form.untuk_pembayaran) form.untuk_pembayaran = form.maksud_perjalanan" rows="2" placeholder="Melaksanakan perjalanan dinas ke ..."
                class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2" :class="{'border-red-400': showValidation && (!form.maksud_perjalanan || form.maksud_perjalanan.trim() === '')}"></textarea>
            <span class="absolute right-3 top-3 w-2 h-2 rounded-full bg-red-500" x-show="showValidation && (!form.maksud_perjalanan || form.maksud_perjalanan.trim() === '')" x-cloak></span>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div>
            <label for="surat-tugas-jabatan" class="text-xs font-medium text-slate-500">Surat Tugas (Jabatan)</label>
            <select id="surat-tugas-jabatan" x-model="form.surat_tugas_jabatan"
                class="mt-1 w-full rounded-lg border border-slate-300 bg-white text-sm px-3 py-2">
                <option value="">-- Pilih jabatan --</option>
                <option value="Anggota Ombudsman RI">Anggota Ombudsman RI</option>
                <option value="Wakil Ketua Ombudsman RI">Wakil Ketua Ombudsman RI</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Nomor Surat Tugas</label>
            <input type="text" x-model="form.nomor_st" placeholder="mis. 123/ORI-PPA/VII/2026"
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

    <div>
        <label class="text-xs font-medium text-slate-500">Pembebanan Anggaran</label>
        <input type="text" x-model="form.pembebanan_anggaran" placeholder="524111 (Biaya Perjalanan Dinas Biasa)"
            class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label class="text-xs font-medium text-slate-500">Kota Tanda Tangan</label>
            <input type="text" x-model="form.kota_tanda_tangan" list="kotaList"
                class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Bulan Tanda Tangan</label>
            <input type="month" x-model="form.tanggal_tanda_tangan"
                class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
    </div>

    <h4 class="font-medium text-sm pt-3 border-t border-slate-200">Daftar Peserta</h4>
    <template x-for="(t, i) in travelers" :key="i">
        <div class="mb-10 border border-slate-300 rounded-lg p-4 space-y-3 relative bg-slate-50/50 shadow-sm">
            <button type="button" @click="removeTraveler(i)" class="absolute top-3 right-3 text-red-500 text-xs hover:underline">Hapus</button>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <x-perdin.pegawai-picker
                    label="Nama"
                    nama-model="t.nama"
                    nip-model="t.nip"
                    jabatan-model="t.jabatan"
                    field-id-expr="'traveler-nama-' + i"
                    field-name="travelers.${i}.nama" />
                <template>
                    <span class="text-xs text-red-500" x-show="showValidation && (!t.nama || t.nama.trim() === '')" x-cloak>•</span>
                </template>
                <div>
                    <label class="text-xs font-medium text-slate-500">Jabatan/Peran</label>
                    <input type="text" x-model="t.jabatan" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Es</label>
                    <input type="text" x-model="t.es" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Golongan</label>
                    <input type="text" x-model="t.gol" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="text-xs font-medium text-slate-500">Dari</label>
                    <input type="text" x-model="t.dari" list="kotaList" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Ke</label>
                    <input type="text" x-model="t.ke" @change="setSbmProvinceFromDestination(t)" list="kotaList" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Tgl Mulai</label>
                    <input type="date" x-model="t.tanggal_mulai" @input="if (t.tanggal_mulai && t.tanggal_sampai && t.hari_mode === 'auto') t.hari = calculateTravelerDays(t)" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Tgl Selesai</label>
                    <input type="date" x-model="t.tanggal_sampai" @input="if (t.tanggal_mulai && t.tanggal_sampai && t.hari_mode === 'auto') t.hari = calculateTravelerDays(t)" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Hari</label>
                    <div class="mt-1 flex gap-2">
                        <select x-model="t.hari_mode" @change="if (t.hari_mode === 'auto' && t.tanggal_mulai && t.tanggal_sampai) t.hari = calculateTravelerDays(t)" class="w-28 rounded-lg border border-slate-300 text-sm px-2 py-2 bg-white">
                            <option value="auto">Auto</option>
                            <option value="manual">Manual</option>
                        </select>
                        <input type="number" x-model.number="t.hari" :disabled="t.hari_mode === 'auto'" :class="t.hari_mode === 'auto' ? 'bg-slate-100 cursor-not-allowed' : ''" class="w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                    </div>
                </div>
            </div>

            <section class="rounded-2xl border border-blue-200 bg-blue-50/70 p-4">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h5 class="text-sm font-semibold text-blue-900">Tarif SBM</h5>
                        <p class="text-xs text-blue-700">PMK 32 Tahun 2025 · Tarif otomatis masih dapat Anda sesuaikan.</p>
                    </div>
                    <span class="rounded-full border border-blue-200 bg-white px-2 py-1 text-xs font-medium text-blue-700">TA 2026</span>
                </div>
                <div class="space-y-3">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div><label class="text-sm font-medium text-slate-700">SBM Uang Harian</label><select x-model="t.sbm_provinsi" @change="applySbm(t)" class="mt-1 w-full rounded-lg border border-blue-200 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm">
                                <option value="">Pilih provinsi</option><template x-for="item in sbmTariffs" :key="item.name">
                                    <option :value="item.name" x-text="item.name"></option>
                                </template>
                            </select></div>
                        <div><label class="text-sm font-medium text-slate-700">SBM per hari</label>
                            <div class="mt-1 flex min-h-[42px] items-center rounded-lg border border-blue-200 bg-white px-3 text-sm font-semibold text-slate-800 shadow-sm" x-text="sbmTariff(t) ? formatRupiah(sbmTariff(t).daily[t.sbm_jenis]) : 'Rp. -'"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div><label class="text-sm font-medium text-slate-700">SBM Penginapan</label>
                            <div class="mt-1 flex min-h-[42px] items-center rounded-lg border border-blue-200 bg-transparent px-3 text-sm text-slate-700 shadow-sm" x-text="t.sbm_provinsi || 'Pilih provinsi pada SBM Uang Harian'"></div>
                        </div>
                        <div><label class="text-sm font-medium text-slate-700">SBM per malam</label>
                            <div class="mt-1 flex min-h-[42px] items-center rounded-lg border border-blue-200 bg-white px-3 text-sm font-semibold text-slate-800 shadow-sm" x-text="sbmTariff(t) ? formatRupiah(sbmTariff(t).hotel[t.sbm_hotel_kelas]) : 'Rp. -'"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div><label class="text-sm font-medium text-slate-700">SBM Tiket</label>
                            <div class="mt-1 flex min-h-[42px] items-center rounded-lg border border-blue-200 bg-white px-3 text-sm font-semibold text-slate-800 shadow-sm" x-text="t.sbm_provinsi || 'Pilih provinsi pada SBM Uang Harian'"></div>
                        </div>
                        <div><label class="text-sm font-medium text-slate-700">Nilai Tiket</label>
                            <div class="mt-1 flex min-h-[42px] items-center rounded-lg border border-blue-200 bg-white px-3 text-sm font-semibold text-slate-800 shadow-sm" x-text="t.tiket ? formatRupiah(Number(t.tiket || 0)) : 'Rp. -'"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div><label class="text-sm font-medium text-slate-700">Asal Kota</label>
                            <input type="text" x-model="t.dari" list="kotaList" class="mt-1 w-full rounded-lg border border-blue-200 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm">
                        </div>
                        <div><label class="text-sm font-medium text-slate-700">Tujuan (Pakai Pilihan)</label>
                            <input type="text" x-model="t.ke" @change="setSbmProvinceFromDestination(t)" list="kotaList" placeholder="Ketik untuk cari kota..." class="mt-1 w-full rounded-lg border border-blue-200 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm">
                        </div>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-1 gap-3 border-t border-blue-200 pt-3 sm:grid-cols-2">
                    <div><label class="text-xs font-medium text-slate-600">Jenis uang harian</label><select x-model="t.sbm_jenis" @change="applySbm(t)" class="mt-1 w-full rounded-lg border border-blue-200 bg-white px-3 py-2 text-sm">
                            <option value="0">Luar kota</option>
                            <option value="1">Dalam kota &gt; 8 jam</option>
                            <option value="2">Diklat</option>
                        </select></div>
                    <div><label class="text-xs font-medium text-slate-600">Golongan penginapan</label><select x-model="t.sbm_hotel_kelas" @change="applySbm(t)" class="mt-1 w-full rounded-lg border border-blue-200 bg-white px-3 py-2 text-sm">
                            <option value="3">Eselon IV / Gol. III, II, I</option>
                            <option value="2">Eselon III / Gol. IV</option>
                            <option value="1">Pejabat negara lain / Eselon II</option>
                            <option value="0">Pejabat negara / Wakil Menteri / Eselon I</option>
                        </select></div>
                </div>
                <p class="mt-3 text-xs text-slate-600">Tiket merupakan biaya riil/perkiraan dan tidak diisi otomatis dari tabel SBM.</p>
            </section>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div>
                    <label class="text-xs font-medium text-slate-500">Uang Harian (per hari)</label>
                    <input type="number" x-model.number="t.uang_harian" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                    <div class="text-xs text-slate-500 mt-1" x-text="'Total: ' + formatRupiah( (Number(t.uang_harian||0) * Math.max(Number(t.hari||1),1)) )"></div>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Penginapan (per malam)</label>
                    <input type="number" x-model.number="t.penginapan" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                    <div class="text-xs text-slate-500 mt-1" x-text="'Total: ' + formatRupiah( Number(t.penginapan||0) * Math.max(Math.max(Number(t.hari||1),1)-1,0) )"></div>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Represen</label>
                    <input type="number" x-model.number="t.represen" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                    <div class="text-xs text-slate-500 mt-1" x-text="'Total: ' + formatRupiah(Number(t.represen||0))"></div>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Tiket (opsional)</label>
                    <input type="number" x-model.number="t.tiket" min="0" placeholder="0" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Transport</label>
                    <div class="mt-1 grid grid-cols-2 gap-2">
                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-2 text-xs text-slate-600 truncate" x-text="t.dari || 'Asal -'"></div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-2 text-xs text-slate-600 truncate" x-text="t.ke || 'Tujuan -'"></div>
                    </div>
                    <input type="number" x-model.number="t.transportasi" placeholder="Harga transport" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
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

    <h4 class="font-medium text-sm pt-3 border-t border-slate-200">Penandatangan</h4>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        <x-perdin.pegawai-picker label="Nama PPK" nama-model="form.nama_ppk" nip-model="form.nip_ppk" field-id-expr="'ppk'" field-name="nama_ppk" />
        <x-perdin.pegawai-picker label="Nama Kabag Keuangan" nama-model="form.nama_kabag_keuangan" nip-model="form.nip_kabag_keuangan" field-id-expr="'kabag'" field-name="nama_kabag_keuangan" />
        <x-perdin.pegawai-picker label="Nama (Mengetahui)" nama-model="form.nama_mengetahui" field-id-expr="'mengetahui'" field-name="nama_mengetahui" />
        <x-perdin.pegawai-picker label="Nama Pengaju" nama-model="form.nama_pengaju" nip-model="form.nip_pengaju" jabatan-model="form.dpr_jabatan" field-id-expr="'pengaju'" field-name="nama_pengaju" />
    </div>

    <div x-show="activeSection === 'ppa'" x-cloak>
        <h5 class="text-xs font-semibold text-slate-500 mt-2">Catatan: Kolom yang mengajukan sesuai Jabatan</h5>
        <x-perdin.pegawai-picker label="(Sesuai dgn SBU TA 2026)" nama-model="form.nama_pengaju_ppa" nip-model="form.nip_pengaju_ppa" field-id-expr="'pengaju_ppa'" field-name="nama_pengaju_ppa" :inline="true" />
    </div>
</div>

{{-- ================= KWITANSI ================= --}}
<div x-show="activeSection === 'kwitansi'" class="space-y-4 rounded-3xl border border-slate-200 bg-slate-50/80 p-4 shadow-sm">
    <div class="flex items-center justify-between gap-3 rounded-2xl bg-slate-100 px-4 py-3">
        <div>
            <h3 class="text-sm font-semibold text-slate-800">Kwitansi PERDIN</h3>
            <p class="text-xs text-slate-500">Informasi pengeluaran dan nama bendahara.</p>
        </div>
    </div>
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
            <p class="mt-1 text-sm font-semibold text-emerald-700" x-text="'Nilai aktif: ' + formatRupiah(kwitansiAmount())"></p>
            <p class="text-xs text-slate-400 mt-1">Kosongkan atau pilih nama untuk mengambil total biaya peserta.</p>
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Untuk Pembayaran</label>
            <textarea x-model="form.untuk_pembayaran" rows="2" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2" placeholder="Untuk pembayaran ..."></textarea>
        </div>
    </div>
    <div>
        <label class="text-xs font-medium text-slate-500">Nama Bepergian</label>
        <select x-model="form.nama_bepergian" @change="const trv = travelers.find(x => x.nama === form.nama_bepergian); if (trv) { form.jumlah_uang_kwitansi = travelerTotal(trv); }" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2 bg-white">
            <option value="">-- Pilih nama peserta --</option>
            <template x-for="(t, i) in travelers" :key="i">
                <option :value="t.nama" x-text="t.nama || '(belum diisi nama)'"></option>
            </template>
        </select>
        <p class="text-xs text-slate-400 mt-1">Pilih dari daftar Peserta. Jumlah Uang otomatis terisi dari total biaya orang tsb.</p>
    </div>
    <div>
        <x-perdin.pegawai-picker label="Nama Bendahara" nama-model="form.nama_bendahara" nip-model="form.nip_bendahara" field-id-expr="'bendahara'" field-name="nama_bendahara" :inline="true" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3 border-t border-slate-200">
        <div>
            <label class="text-xs font-medium text-slate-500">Kota Bepergian</label>
            <input type="text" x-model="form.kota_bepergian" list="kotaList"
                class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Bulan Bepergian</label>
            <input type="month" x-model="form.tanggal_bepergian"
                class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label class="text-xs font-medium text-slate-500">Bulan Dibayar Lunas</label>
            <input type="month" x-model="form.tanggal_lunas"
                class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
        <p class="text-xs text-slate-400 sm:col-span-2 -mt-1">Muncul di baris "Dibayar lunas, Tgl ... ".</p>
    </div>
</div>

{{-- ================= RINCIAN ================= --}}
<div x-show="activeSection === 'rincian'" class="space-y-4 rounded-3xl border border-slate-200 bg-slate-50/80 p-4 shadow-sm">
    <div class="flex items-center justify-between gap-3 rounded-2xl bg-slate-100 px-4 py-3">
        <div>
            <h3 class="text-sm font-semibold text-slate-800">Rincian Biaya PERDIN</h3>
            <p class="text-xs text-slate-500">Rincian biaya, lampiran SPPD, dan nama yang bepergian.</p>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
            <label class="text-xs font-medium text-slate-500">Lampiran SPPD No.</label>
            <input type="text" x-model="form.lampiran_sppd_no" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Tanggal SPPD</label>
            <input type="date" x-model="form.tanggal_sppd" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">&nbsp;</label>
            <div class="text-xs text-slate-400">Nama yang Bepergian akan muncul di bawah Rincian.</div>
        </div>
    </div>

    <div>
        <x-perdin.pegawai-picker label="Nama (Mengetahui/Menyetujui)" nama-model="form.nama_mengetahui_rincian" nip-model="form.nip_mengetahui_rincian" field-id-expr="'mengetahui_rincian'" field-name="nama_mengetahui_rincian" :inline="true" />
        <p class="text-xs text-slate-400 mt-1">Muncul di bagian bawah sheet Rincian, kolom "Mengetahui/Menyetujui".</p>
    </div>

    <template x-for="(r, i) in rincianItems" :key="i">
        <div class="border border-slate-200 rounded-lg p-4 space-y-3 relative bg-slate-50/50">
            <button type="button" @click="removeRincianItem(i)" class="absolute top-3 right-3 text-red-500 text-xs hover:underline">Hapus</button>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-medium text-slate-500">Perincian Biaya</label>
                    <div class="relative">
                        <input type="text" x-model="r.uraian" placeholder="mis. Uang Harian" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2" :class="{'border-red-400': showValidation && (!r.uraian || r.uraian.trim() === '')}">
                        <span class="absolute right-3 top-3 w-2 h-2 rounded-full bg-red-500" x-show="showValidation && (!r.uraian || r.uraian.trim() === '')" x-cloak></span>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Jumlah / Hari</label>
                    <input type="number" x-model.number="r.jumlah_satuan" min="0" step="0.01" placeholder="mis. 1" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-medium text-slate-500">Keterangan Tambahan</label>
                    <input type="text" x-model="r.keterangan_tambahan" placeholder="mis. Uang Harian"
                        class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
                <div>
                    <label class="text-xs font-medium text-slate-500">Harga (Rp)</label>
                    <input type="number" x-model.number="r.harga_satuan" min="0" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                </div>
            </div>
            <div>
                <label class="text-xs font-medium text-slate-500">Keterangan</label>
                <input type="text" x-model="r.keterangan" placeholder="mis. Transport, hotel, dll." class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
            </div>
        </div>
    </template>
    <button type="button" @click="addRincianItem()" class="text-sm text-blue-600 font-medium hover:underline">+ Tambah Rincian</button>
    <div class="mt-4">
        <label class="text-xs font-medium text-slate-500">Nama yang Bepergian (untuk Rincian)</label>
        <select x-model="form.nama_bepergian" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
            <option value="">-- Pilih nama peserta --</option>
            <template x-for="(t, i) in travelers" :key="'rincian-traveler-' + i">
                <option :value="t.nama" x-text="(t.nama || '(belum diisi nama)') + (t.nip ? ' - ' + t.nip : '')"></option>
            </template>
        </select>
        <p class="mt-1 text-xs text-slate-400">Daftar diambil dari peserta Pertanggungjawaban/PPA.</p>
    </div>
</div>

{{-- ================= DPR ================= --}}
<div x-show="activeSection === 'dpr'" class="space-y-4 rounded-3xl border border-slate-200 bg-slate-50/80 p-4 shadow-sm">
    <div class="flex items-center justify-between gap-3 rounded-2xl bg-slate-100 px-4 py-3">
        <div>
            <h3 class="text-sm font-semibold text-slate-800">DPR PERDIN</h3>
            <p class="text-xs text-slate-500">Daftar pengeluaran riil untuk perjalanan dinas.</p>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
            <label class="text-xs font-medium text-slate-500">Nama (DPR)</label>
            <select x-model="form.dpr_nama" @change="selectDprTraveler()" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2 bg-white">
                <option value="">-- Pilih nama peserta --</option>
                <template x-for="(t, i) in travelers" :key="i">
                    <option :value="t.nama" x-text="t.nama || '(belum diisi nama)'"></option>
                </template>
            </select>
            <p class="text-xs text-slate-400 mt-1">Pilih dari daftar Peserta, sama kayak "Nama Bepergian" di Kwitansi.</p>
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">NIP</label>
            <input type="text" x-model="form.dpr_nip" placeholder="Terisi otomatis dari peserta" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Nomor SPD</label>
            <input type="text" x-model="form.nomor_spd" x-effect="if (!form.nomor_spd) form.nomor_spd = form.nomor_st" placeholder="Otomatis ngikut Nomor ST, bisa diedit" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-500">Tanggal SPD</label>
            <input type="date" x-model="form.tanggal_spd" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
        </div>
    </div>

    <div class="space-y-4">
        <h4 class="font-medium text-sm pt-3 border-t border-slate-200">Daftar Pengeluaran Riil</h4>
        <template x-for="(d, i) in dprItems" :key="i">
            <div class="border border-slate-200 rounded-lg p-4 space-y-3 relative bg-slate-50/50">
                <button type="button" @click="removeDprItem(i)" class="absolute top-3 right-3 text-red-500 text-xs hover:underline">Hapus</button>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs font-medium text-slate-500">Uraian</label>
                        <input type="text" x-model="d.uraian" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-slate-500">Jumlah (Rp)</label>
                        <input type="number" x-model.number="d.jumlah" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2">
                    </div>
                </div>
            </div>
        </template>
        <button type="button" @click="addDprItem()" class="text-sm text-blue-600 font-medium hover:underline">+ Tambah DPR</button>
    </div>
</div>

{{-- ================= PERNYATAAN ================= --}}
<div x-show="activeSection === 'pernyataan'" class="space-y-4 rounded-3xl border border-slate-200 bg-slate-50/80 p-4 shadow-sm">
    <div class="flex items-center justify-between gap-3 rounded-2xl bg-slate-100 px-4 py-3">
        <div>
            <h3 class="text-sm font-semibold text-slate-800">Pernyataan</h3>
            <p class="text-xs text-slate-500">Tentukan pernyataan manual atau status kendaraan dinas.</p>
        </div>
    </div>
    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
        <div class="flex items-center gap-3">
            <input id="pernyataan-kendaraan" type="checkbox" x-model="form.pernyataan_tidak_menggunakan_kendaraan" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <label for="pernyataan-kendaraan" class="text-sm font-medium text-slate-500">Peserta tidak menggunakan kendaraan dinas</label>
        </div>
        <p class="text-xs text-slate-500 mt-2">Centang jika kendaraan dinas tidak digunakan. Jika dikosongkan, pernyataan otomatis akan diambil dari maksud perjalanan.</p>
    </div>
    <div>
        <label class="text-xs font-medium text-slate-500">Pernyataan Manual</label>
        <textarea x-model="form.pernyataan_teks" rows="5" placeholder="Masukkan pernyataan manual di sini" class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2"></textarea>
        <p class="text-xs text-slate-400 mt-1">Isi jika ingin menggunakan teks pernyataan sendiri. Kosongkan untuk mengambil pernyataan otomatis.</p>
    </div>
</div>