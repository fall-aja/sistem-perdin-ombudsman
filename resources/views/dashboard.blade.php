@php
    $sbmTariffsJson = json_encode(config('sbm.provinces'));
@endphp

<div id="sbm-config" data-sbm="{{ json_encode(config('sbm.provinces')) }}" class="hidden"></div>

<x-layouts.app>
    <div x-data="perdinApp()" x-init="init()" x-cloak class="min-h-screen">

        {{-- SIDEBAR --}}
        <aside class="pd-sidebar fixed inset-y-0 left-0 z-30 flex w-64 flex-col overflow-hidden border-r text-slate-200 shadow-xl shadow-slate-950/20">
            <div class="pd-sidebar-brand border-b p-5">
                <div class="flex items-center gap-3">
                    <img src="/images/ori_square.png" alt="Logo ORI" class="w-9 h-9 rounded-lg object-contain bg-white p-1">
                    <div>
                        <h1 class="font-serif font-semibold text-lg leading-tight text-white">Sistem PERDIN</h1>
                        <p class="text-xs" style="color: rgba(169,184,201,.7)">Ombudsman RI</p>
                    </div>
                </div>
            </div>

            <nav class="pd-sidebar-nav m-3 space-y-1 rounded-xl border p-2">
                <div class="pd-sidebar-eyebrow px-3 pb-2 text-xs">Menu Utama</div>
                <template x-for="section in sections" :key="section.key">
                    <button
                        @click="activeSection = section.key"
                        class="flex w-full items-center justify-between rounded-lg border border-transparent px-3 py-2 text-left text-sm transition"
                        :class="activeSection === section.key ? 'pd-nav-active' : 'pd-nav-idle text-slate-300'">
                        <span class="flex items-center gap-2 min-w-0">
                            <span class="shrink-0 [&>svg]:h-4 [&>svg]:w-4" x-html="section.icon"></span>
                            <span class="truncate" x-text="section.label"></span>
                        </span>
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 shrink-0" x-show="sectionHasErrors(section.key)" x-cloak></span>
                    </button>
                </template>
                <button @click="activeSection = 'surat_tugas'" type="button"
                    class="flex w-full items-center justify-between rounded-lg border border-transparent px-3 py-2 text-left text-sm transition"
                    :class="activeSection === 'surat_tugas' ? 'pd-nav-active' : 'pd-nav-idle text-slate-300'">
                    <span class="flex items-center gap-2 min-w-0">
                        <span class="shrink-0 [&>svg]:h-4 [&>svg]:w-4" x-html="suratTugasIcon"></span>
                        <span class="truncate">Penyimpanan MAK / Surat Tugas</span>
                    </span>
                </button>
            </nav>

            <div class="pd-sidebar-history mt-1 min-h-0 flex-1 overflow-y-auto border-t p-3">
                <p class="pd-sidebar-eyebrow px-3 mb-2">Riwayat</p>
                <div class="space-y-1">
                    @foreach ($perdins as $p)
                    <div class="pd-sidebar-history-item px-3 py-2 text-xs rounded cursor-pointer group relative transition"
                        @click="loadPerdin({{ $p->id }})">
                        <div class="font-medium truncate pr-5">{{ \Illuminate\Support\Str::limit($p->maksud_perjalanan ?? '(belum diisi)', 30) }}</div>
                        <div class="pd-sidebar-history-date">{{ $p->created_at->format('d M Y H:i') }}</div>
                        <button
                            type="button"
                            @click.stop="deletePerdin({{ $p->id }})"
                            class="absolute top-2 right-2 text-amber-200/50 hover:text-red-400 opacity-0 group-hover:opacity-100 transition"
                            title="Hapus">✕</button>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="pd-sidebar-footer border-t p-3">
                <div class="flex items-center justify-center gap-2">
                    <a href="{{ route('template-settings.index') }}" title="Pengaturan Template" aria-label="Pengaturan Template" class="flex h-10 w-10 items-center justify-center rounded-lg text-amber-300 hover:bg-white/5">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.12 2.12-.06-.06a1.7 1.7 0 0 0-1.88-.34 1.7 1.7 0 0 0-1.04 1.56V20.3h-3v-.08A1.7 1.7 0 0 0 10.66 18.66a1.7 1.7 0 0 0-1.88.34l-.06.06-2.12-2.12.06-.06A1.7 1.7 0 0 0 7 15a1.7 1.7 0 0 0-1.56-1.04H5.3v-3h.14A1.7 1.7 0 0 0 7 9.92a1.7 1.7 0 0 0-.34-1.88l-.06-.06 2.12-2.12.06.06a1.7 1.7 0 0 0 1.88.34A1.7 1.7 0 0 0 11.7 4.7v-.08h3v.08a1.7 1.7 0 0 0 1.04 1.56 1.7 1.7 0 0 0 1.88-.34l.06-.06 2.12 2.12-.06.06A1.7 1.7 0 0 0 19.4 9.92a1.7 1.7 0 0 0 1.56 1.04h.14v3h-.14A1.7 1.7 0 0 0 19.4 15Z"></path></svg>
                    </a>
                    <a href="{{ route('perdin.recycle') }}" title="Recycle Bin" aria-label="Recycle Bin" class="flex h-10 w-10 items-center justify-center rounded-lg text-slate-300 hover:bg-white/5 hover:text-red-300">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 6h18"></path><path d="M8 6V4h8v2"></path><path d="M19 6l-1 14H6L5 6"></path><path d="M10 11v5M14 11v5"></path></svg>
                    </a>
                </div>
            </div>
        </aside>

        {{-- KONTEN --}}
        <main class="ml-64 min-h-screen p-8 space-y-6">

            <div x-show="message" x-transition class="text-sm rounded-lg px-4 py-2"
                :class="messageType === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'"
                x-text="message"></div>

            @if (session('message'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm text-emerald-700">{{ session('message') }}</div>
            @endif

            <div x-show="lastDeleted" x-transition class="fixed top-4 right-4 z-50">
                <div class="flex items-center gap-3 bg-yellow-50 border border-yellow-200 text-yellow-900 px-4 py-2 rounded shadow">
                    <div class="text-sm">File sudah dihapus.</div>
                    <button @click="restorePerdin()" class="text-sm underline">Pulihkan</button>
                    <button @click="lastDeleted = null" class="text-sm text-slate-500">Tutup</button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <section x-show="activeSection === 'surat_tugas'" x-cloak class="pd-surface space-y-6 p-6">
                    <div>
                        <h2 class="font-serif text-lg font-semibold text-ink">Penyimpanan Surat Tugas</h2>
                        <p class="text-sm" style="color:#6b6455">Upload surat tugas terlebih dahulu dan cari kembali berdasarkan nomor atau MAK.</p>
                    </div>
                    <form method="POST" action="{{ route('surat-tugas.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-3 md:grid-cols-5">
                        @csrf
                        <input name="nomor" required placeholder="Nomor surat tugas" class="pd-input">
                        <input name="tanggal" type="date" class="pd-input">
                        <input name="mak" placeholder="MAK" class="pd-input">
                        <input name="file" required type="file" accept=".pdf,.doc,.docx,.xls,.xlsx" class="pd-input">
                        <button class="pd-btn pd-btn-primary justify-center">Simpan Surat Tugas</button>
                    </form>
                    <form method="GET" action="{{ route('dashboard') }}" class="flex gap-2">
                        <input name="q" value="{{ request('q') }}" placeholder="Cari nomor, MAK, atau nama file" class="pd-input">
                        <button class="pd-btn pd-btn-outline">Cari</button>
                    </form>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead><tr class="border-b" style="border-color:#e7e2d4"><th class="px-3 py-2 pd-label">Nomor</th><th class="px-3 py-2 pd-label">Tanggal</th><th class="px-3 py-2 pd-label">MAK</th><th class="px-3 py-2 pd-label">File</th><th></th></tr></thead>
                            <tbody>
                            @forelse ($suratTugas as $surat)
                                <tr class="border-b" style="border-color:#f1eee5"><td class="px-3 py-2">{{ $surat->nomor }}</td><td class="px-3 py-2">{{ optional($surat->tanggal)->format('d M Y') ?: '-' }}</td><td class="px-3 py-2">{{ $surat->mak ?: '-' }}</td><td class="px-3 py-2"><a class="text-[#0b4f8a] hover:underline" href="{{ route('surat-tugas.show', $surat) }}" target="_blank">{{ $surat->file_name }}</a></td><td class="px-3 py-2 text-right"><form method="POST" action="{{ route('surat-tugas.destroy', $surat) }}">@csrf @method('DELETE')<button class="text-[#a32638] hover:underline">Hapus</button></form></td></tr>
                            @empty
                                <tr><td colspan="5" class="px-3 py-6 text-center text-slate-400">Belum ada surat tugas tersimpan.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
                {{-- FORM --}}
                <div x-show="activeSection !== 'surat_tugas'" class="pd-surface relative overflow-hidden p-6">
                    <div class="relative z-20">
                        <x-perdin.form />
                    </div>
                    <div class="relative z-20 mt-6 pt-6 border-t" style="border-color:#e7e2d4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="text-sm" style="color:#6b6455">Tombol aksi tersedia setelah data disimpan.</div>
                            <div class="flex flex-wrap gap-2">
                                <button @click="save()" :disabled="saving" class="pd-btn pd-btn-primary">
                                    <span x-show="!saving">Simpan</span>
                                    <span x-show="saving">Menyimpan...</span>
                                </button>
                                <button @click="nextSection()" type="button" class="pd-btn pd-btn-outline">
                                    Berikutnya
                                </button>
                                <button @click="generateExcel()" :disabled="!perdinId" class="pd-btn pd-btn-accent">
                                    Generate Excel
                                </button>
                                <button @click="generatePdf()" :disabled="!perdinId" class="pd-btn pd-btn-accent">
                                    Generate PDF
                                </button>
                                <button @click="printDocument()" :disabled="!perdinId" class="pd-btn pd-btn-ghost">
                                    Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PREVIEW --}}
                <div x-show="activeSection !== 'surat_tugas'" class="pd-surface p-6">
                    <x-perdin.preview />
                </div>
            </div>
        </main>
    </div>

    <script>
        function perdinApp() {
            return {
                perdinId: null,
                saving: false,
                message: '',
                messageType: 'success',
                activeSection: 'pertanggung_jawaban',
                sections: [{
                        key: 'ppa',
                        label: 'PPA PERDIN',
                        icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="12" rx="2"></rect><circle cx="12" cy="12" r="2.5"></circle></svg>'
                    },
                    {
                        key: 'pertanggung_jawaban',
                        label: 'Pertanggung Jawaban PERDIN',
                        icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="4" width="12" height="16" rx="2"></rect><path d="M9 4V3a1 1 0 011-1h4a1 1 0 011 1v1"></path><path d="M9 13l2 2 4-4"></path></svg>'
                    },
                    {
                        key: 'kwitansi',
                        label: 'Kwit PERDIN 1',
                        icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3z"></path><path d="M9 8h6M9 12h6"></path></svg>'
                    },
                    {
                        key: 'rincian',
                        label: 'Rincian PERDIN 1',
                        icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="2"></rect><path d="M8 7h8"></path><circle cx="9" cy="12" r=".6" fill="currentColor" stroke="none"></circle><circle cx="12" cy="12" r=".6" fill="currentColor" stroke="none"></circle><circle cx="15" cy="12" r=".6" fill="currentColor" stroke="none"></circle><circle cx="9" cy="16" r=".6" fill="currentColor" stroke="none"></circle><circle cx="12" cy="16" r=".6" fill="currentColor" stroke="none"></circle><circle cx="15" cy="16" r=".6" fill="currentColor" stroke="none"></circle></svg>'
                    },
                    {
                        key: 'dpr',
                        label: 'DPR PERDIN',
                        icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="6" width="12" height="14" rx="2"></rect><path d="M4 4v13a1 1 0 001 1h1"></path><path d="M11 10h6M11 14h6M11 18h3"></path></svg>'
                    },
                    {
                        key: 'pernyataan',
                        label: 'Pernyataan',
                        icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4l10-10-4-4L4 16v4z"></path><path d="M13 6l4 4"></path></svg>'
                    },
                ],

                suratTugasIcon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="4" rx="1"></rect><path d="M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"></path><path d="M10 12h4"></path></svg>',

                form: {
                    nomor: '',
                    maksud_perjalanan: '',
                    surat_tugas_jabatan: '',
                    nomor_st: '',
                    tanggal_st: '',
                    nomor_rk: '',
                    pembebanan_anggaran: '',
                    kota_tanda_tangan: 'Jakarta',
                    tanggal_tanda_tangan: '',
                    nama_ppk: '',
                    nip_ppk: '',
                    nama_kabag_keuangan: '',
                    nip_kabag_keuangan: '',
                    nama_mengetahui: '',
                    nama_pengaju: '',
                    nip_pengaju: '',
                    nama_pengaju_ppa: '',
                    nip_pengaju_ppa: '',
                    tahun_anggaran: new Date().getFullYear().toString(),
                    nomor_bukti_kwitansi: '',
                    mak: '',
                    sudah_terima_dari: 'BENDAHARA PENGELUARAN OMBUDSMAN RI',
                    jumlah_uang_kwitansi: 0,
                    untuk_pembayaran: '',
                    nama_bendahara: '',
                    nip_bendahara: '',
                    kota_bepergian: 'Jakarta',
                    tanggal_bepergian: '',
                    kota_lunas: 'Jakarta',
                    tanggal_lunas: '',
                    lampiran_sppd_no: '',
                    nama_bepergian: '',
                    nama_mengetahui_rincian: '',
                    nip_mengetahui_rincian: '',
                    pernyataan_tidak_menggunakan_kendaraan: false,
                    pernyataan_teks: '',
                    tanggal_sppd: '',
                    dpr_nama: '',
                    dpr_nip: '',
                    dpr_jabatan: '',
                    nomor_spd: '',
                    tanggal_spd: '',
                },

                showValidation: false,

                // true kalau user pernah ngetik langsung di "Untuk Pembayaran"
                // (Kwitansi) — begitu true, field itu berhenti auto-ngikut
                // "Maksud Perjalanan Dinas" sampai form direset/dimuat ulang.
                untukPembayaranManual: false,

                travelers: [],
                sbmTariffs: JSON.parse(document.getElementById('sbm-config')?.dataset?.sbm || '[]'),
                rincianItems: [],
                dprItems: [],
                suggestions: {
                    kota: []
                },
                pegawaiResults: {},
                pegawaiOpen: {},

                async searchPegawai(fieldId, query) {
                    if (!query || query.length < 1) {
                        this.pegawaiResults[fieldId] = [];
                        this.pegawaiOpen[fieldId] = false;
                        return;
                    }
                    try {
                        const res = await fetch(`/master-data/pegawai?q=${encodeURIComponent(query)}`);
                        this.pegawaiResults[fieldId] = await res.json();
                        this.pegawaiOpen[fieldId] = this.pegawaiResults[fieldId].length > 0;
                    } catch (e) {
                        this.pegawaiResults[fieldId] = [];
                    }
                },

                emptyTraveler() {
                    return {
                        nama: '',
                        nip: '',
                        jabatan: '',
                        es: '-',
                        gol: '-',
                        dari: 'Jakarta',
                        ke: '',
                        tanggal_mulai: '',
                        tanggal_sampai: '',
                        hari_mode: 'auto',
                        hari: 1,
                        uang_harian: 0,
                        penginapan: 0,
                        represen: 0,
                        tiket: 0,
                        transportasi: 0,
                        sewa_kendaraan: 0,
                        sbm_provinsi: '',
                        sbm_jenis: '0',
                        sbm_hotel_kelas: '3',
                    };
                },

                addTraveler() {
                    this.travelers.push(this.emptyTraveler());
                },
                selectDprTraveler() {
                    const traveler = this.travelers.find(item => item.nama === this.form.dpr_nama);
                    if (!traveler) return;
                    this.form.dpr_nip = traveler.nip || '';
                    this.form.dpr_jabatan = traveler.jabatan || '';
                },
                nextSection() {
                    const currentIndex = this.sections.findIndex(section => section.key === this.activeSection);
                    if (currentIndex >= 0 && currentIndex < this.sections.length - 1) {
                        this.activeSection = this.sections[currentIndex + 1].key;
                    }
                },
                normalizeSbmText(value) {
                    return (value || '').toString().toLowerCase()
                        .replace(/d\.k\.i\./g, 'dki')
                        .replace(/d\.i\./g, 'di')
                        .replace(/[^a-z0-9]+/g, ' ').trim();
                },
                sbmTariff(t) {
                    const query = this.normalizeSbmText(t.sbm_provinsi);
                    if (!query) return null;
                    return this.sbmTariffs.find(item => {
                        const province = this.normalizeSbmText(item.name);
                        return province === query || province.includes(query) || query.includes(province);
                    }) || null;
                },
                applySbm(t) {
                    const tariff = this.sbmTariff(t);
                    if (!tariff) return;
                    t.sbm_provinsi = tariff.name;
                    t.uang_harian = tariff.daily[Number(t.sbm_jenis)];
                    t.penginapan = tariff.hotel[Number(t.sbm_hotel_kelas)];
                },
                setSbmProvinceFromDestination(t) {
                    const destination = this.normalizeSbmText(t.ke);
                    const tariff = this.sbmTariffs.find(item => destination.includes(this.normalizeSbmText(item.name)));
                    if (!tariff) return;
                    t.sbm_provinsi = tariff.name;
                    this.applySbm(t);
                },
                removeTraveler(i) {
                    this.travelers.splice(i, 1);
                },

                addRincianItem() {
                    this.rincianItems.push({
                        uraian: '',
                        keterangan_tambahan: '',
                        jumlah_satuan: 1,
                        harga_satuan: 0,
                        keterangan: ''
                    });
                },
                removeRincianItem(i) {
                    this.rincianItems.splice(i, 1);
                },

                addDprItem() {
                    this.dprItems.push({
                        uraian: '',
                        jumlah: 0
                    });
                },
                removeDprItem(i) {
                    this.dprItems.splice(i, 1);
                },

                async loadSuggestions() {
                    try {
                        const res = await fetch('/master-data/suggestions');
                        this.suggestions = await res.json();
                    } catch (e) {
                        // gagal ambil saran autocomplete bukan hal fatal, form tetap bisa diisi manual
                    }
                },

                init() {
                    this.travelers.push(this.emptyTraveler());
                    this.addRincianItem();
                    this.addDprItem();
                    this.loadSuggestions();
                },

                csrf() {
                    return document.querySelector('meta[name="csrf-token"]').content;
                },

                showMessage(text, type = 'success') {
                    this.message = text;
                    this.messageType = type;
                    setTimeout(() => this.message = '', 4000);
                },

                serverValidationErrors: {},
                fieldSectionMap: {
                    pertanggung_jawaban: [
                        'maksud_perjalanan',
                        'nama_ppk',
                        'nip_ppk',
                        'nama_kabag_keuangan',
                        'nip_kabag_keuangan',
                        'nama_mengetahui',
                        'nama_pengaju',
                        'travelers.*.nama',
                        'travelers.*.jabatan',
                        'travelers.*.hari',
                    ],
                    ppa: [
                        'maksud_perjalanan',
                        'pembebanan_anggaran',
                        'nama_ppk',
                        'nip_ppk',
                        'nama_kabag_keuangan',
                        'nip_kabag_keuangan',
                        'nama_mengetahui',
                        'nama_pengaju',
                        'travelers.*.nama',
                    ],
                    kwitansi: [
                        'mak',
                        'nomor_bukti_kwitansi',
                        'jumlah_uang_kwitansi',
                        'untuk_pembayaran',
                        'nama_bendahara',
                        'nip_bendahara',
                    ],
                    rincian: [
                        'lampiran_sppd_no',
                        'tanggal_sppd',
                        'nama_bepergian',
                        'rincian_items.*.uraian',
                    ],
                    dpr: [
                        'dpr_nama',
                        'dpr_nip',
                        'dpr_jabatan',
                        'nomor_spd',
                        'tanggal_spd',
                        'dpr_items.*.uraian',
                    ],
                    pernyataan: [
                        'pernyataan_teks',
                        'pernyataan_tidak_menggunakan_kendaraan',
                    ],
                },

                serverFieldHasError(field) {
                    if (!field || !this.serverValidationErrors) {
                        return false;
                    }

                    if (this.serverValidationErrors[field]) {
                        return true;
                    }

                    return Object.keys(this.serverValidationErrors).some(key => {
                        const escaped = key.split('.').map(part => {
                            if (part === '*') {
                                return '[^.]+'.replace(/[-\\/\\^$+?.()|[\]{}]/g, '\\$&');
                            }
                            return part.replace(/[-\\/\\^$+?.()|[\]{}]/g, '\\$&');
                        }).join('\\.');
                        return new RegExp('^' + escaped + '$').test(field);
                    });
                },

                fieldValidationClass(field) {
                    return this.serverFieldHasError(field) ? 'border-red-400' : '';
                },

                fieldValidationMessage(field) {
                    if (!this.serverValidationErrors) {
                        return '';
                    }

                    if (this.serverValidationErrors[field]?.[0]) {
                        return this.serverValidationErrors[field][0];
                    }

                    const matchingKey = Object.keys(this.serverValidationErrors).find(key => {
                        const escaped = key.split('.').map(part => {
                            if (part === '*') {
                                return '[^.]+'.replace(/[-\\/\\^$+?.()|[\]{}]/g, '\\$&');
                            }
                            return part.replace(/[-\\/\\^$+?.()|[\]{}]/g, '\\$&');
                        }).join('\\.');

                        return new RegExp('^' + escaped + '$').test(field);
                    });

                    return matchingKey ? this.serverValidationErrors[matchingKey][0] : '';
                },

                sectionHasErrors(key) {
                    if (this.serverValidationErrors && this.serverHasErrorsForSection(key)) {
                        return true;
                    }

                    // Basic client-side checks to highlight incomplete sections
                    if (key === 'pertanggung_jawaban' || key === 'ppa') {
                        if (!this.form.maksud_perjalanan || !this.travelers.length) return true;
                        if (this.travelers.some(t => !t.nama || t.nama.trim() === '')) return true;
                        return false;
                    }

                    if (key === 'rincian') {
                        return !this.rincianItems.some(r => r.uraian && r.uraian.trim() !== '');
                    }

                    if (key === 'kwitansi') {
                        return !this.form.mak || this.form.mak.trim() === '';
                    }

                    if (key === 'dpr') {
                        return !this.form.dpr_nama || this.form.dpr_nama.trim() === '';
                    }

                    return false;
                },

                serverHasErrorsForSection(key) {
                    const fields = this.fieldSectionMap[key] || [];
                    return fields.some(field => this.serverFieldHasError(field));
                },

                lastDeleted: null,


                validateAll() {
                    this.showValidation = true;
                    // If any section reports errors, form is invalid
                    return !this.sections.some(s => this.sectionHasErrors(s.key));
                },

                async save() {
                    if (!this.validateAll()) {
                        this.showMessage('Form belum lengkap. Periksa tanda merah.', 'error');
                        return;
                    }

                    this.saving = true;
                    try {
                        const res = await fetch('{{ route("perdin.store") }}', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrf(),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                ...this.form,
                                id: this.perdinId,
                                untuk_pembayaran: this.form.untuk_pembayaran || this.form.maksud_perjalanan,
                                travelers: this.travelers,
                                rincian_items: this.rincianItems
                                    .filter(r => r.uraian && r.uraian.trim() !== '')
                                    .map(r => ({
                                        ...r,
                                        jumlah_satuan: r.jumlah_satuan == null ? 0 : r.jumlah_satuan,
                                        harga_satuan: r.harga_satuan == null ? 0 : r.harga_satuan,
                                    })),
                                dpr_items: this.dprItems.filter(d => d.uraian && d.uraian.trim() !== ''),
                            }),
                        });

                        const data = await res.json();

                        if (!res.ok) {
                            this.serverValidationErrors = data.errors || {};
                            this.showValidation = true;
                            const firstError = data.errors ? Object.values(data.errors)[0][0] : null;
                            this.showMessage(firstError || data.message || 'Gagal menyimpan data.', 'error');
                            return;
                        }

                        this.serverValidationErrors = {};
                        this.perdinId = data.perdin.id;
                        this.applyPerdinData(data.perdin);
                        this.showMessage('Data berhasil disimpan.');
                    } catch (e) {
                        this.showMessage('Terjadi kesalahan jaringan.', 'error');
                    } finally {
                        this.saving = false;
                    }
                },

                async loadPerdin(id) {
                    const res = await fetch(`/perdin/${id}/preview`);
                    const data = await res.json();

                    if (!res.ok) {
                        this.showMessage(data.message || 'Gagal memuat data.', 'error');
                        return;
                    }

                    this.applyPerdinData(data);
                },

                applyPerdinData(data) {
                    this.perdinId = data.id;
                    Object.keys(this.form).forEach(key => {
                        if (Object.prototype.hasOwnProperty.call(data, key)) {
                            this.form[key] = data[key] ?? (key === 'pernyataan_tidak_menggunakan_kendaraan' ? false : '');
                        }
                    });
                    this.travelers = data.travelers?.length ? data.travelers : [this.emptyTraveler()];
                    this.travelers.forEach(traveler => {
                        traveler.sbm_provinsi = traveler.sbm_provinsi || '';
                        traveler.sbm_jenis = traveler.sbm_jenis ?? '0';
                        traveler.sbm_hotel_kelas = traveler.sbm_hotel_kelas ?? '3';
                    });
                    this.rincianItems = data.rincian_items ?? [];
                    this.dprItems = data.dpr_items ?? [];
                    this.form.nama_bepergian = data.nama_bepergian || this.travelers[0]?.nama || '';
                    this.form.untuk_pembayaran = data.untuk_pembayaran || data.maksud_perjalanan || '';
                    // Kalau isi "Untuk Pembayaran" yang tersimpan beda dari
                    // "Maksud Perjalanan Dinas", berarti dulu pernah diedit
                    // manual — jangan auto-timpa lagi. Kalau sama persis,
                    // anggap masih auto-sync (default).
                    this.untukPembayaranManual = !!data.untuk_pembayaran
                        && data.untuk_pembayaran !== data.maksud_perjalanan;
                    // Auto-fill DPR dari Nama Pengaju jika DPR belum diisi
                    this.form.dpr_nama = data.dpr_nama || data.nama_pengaju || '';
                    this.form.dpr_nip = data.dpr_nip || data.nip_pengaju || '';
                    this.form.dpr_jabatan = data.dpr_jabatan || '';
                    this.selectDprTraveler();
                },

                async deletePerdin(id) {
                    if (!confirm('Hapus riwayat ini?')) {
                        return;
                    }
                    try {
                        const res = await fetch(`/perdin/${id}`, {
                            method: 'DELETE',
                            credentials: 'same-origin',
                            headers: {
                                'X-CSRF-TOKEN': this.csrf(),
                                'Accept': 'application/json',
                            },
                        });
                        if (!res.ok) {
                            this.showMessage('Gagal menghapus data.', 'error');
                            return;
                        }

                        const data = await res.json();

                        // Set lastDeleted so user can restore
                        this.lastDeleted = data.id || id;

                        // If the deleted doc was open, clear it
                        if (this.perdinId === id) {
                            this.perdinId = null;
                        }

                        this.showMessage('Riwayat dihapus. Anda dapat memulihkan file.');
                    } catch (e) {
                        this.showMessage('Terjadi kesalahan jaringan.', 'error');
                    }
                },

                async generateExcel() {
                    if (!this.perdinId) return;
                    window.location.href = `/perdin/${this.perdinId}/excel`;
                },

                async restorePerdin() {
                    if (!this.lastDeleted) return;
                    try {
                        const res = await fetch(`/perdin/${this.lastDeleted}/restore`, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'X-CSRF-TOKEN': this.csrf(),
                                'Accept': 'application/json',
                            },
                        });
                        if (!res.ok) {
                            this.showMessage('Gagal memulihkan data.', 'error');
                            return;
                        }
                        const data = await res.json();
                        this.showMessage('Data berhasil dipulihkan.');
                        this.lastDeleted = null;
                        // reload to refresh history list
                        window.location.reload();
                    } catch (e) {
                        this.showMessage('Terjadi kesalahan jaringan.', 'error');
                    }
                },

                async generatePdf() {
                    if (!this.perdinId) return;
                    window.open(`/perdin/${this.perdinId}/pdf`, '_blank');
                },

                printDocument() {
                    if (!this.perdinId) return;
                    const win = window.open(`/perdin/${this.perdinId}/pdf`, '_blank');
                    win.addEventListener('load', () => win.print());
                },

                formatRupiah(v) {
                    const n = Number(v || 0);
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(n);
                },

                calculateTravelerDays(t) {
                    if (t.tanggal_mulai && t.tanggal_sampai) {
                        const start = new Date(t.tanggal_mulai);
                        const end = new Date(t.tanggal_sampai);

                        if (!Number.isNaN(start.getTime()) && !Number.isNaN(end.getTime())) {
                            const diff = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;
                            return diff > 0 ? diff : 1;
                        }
                    }

                    return Number(t.hari || 1);
                },
                syncTravelerDays(t) {
                    if (t.hari_mode !== 'auto' || !t.tanggal_mulai || !t.tanggal_sampai) {
                        return;
                    }

                    t.hari = this.calculateTravelerDays(t);
                },

                rincianNotes() {
                    return this.rincianItems
                        .filter(r => r.keterangan && r.keterangan.toString().trim() !== '')
                        .map(r => `${r.uraian}${r.uraian ? ': ' : ''}${r.keterangan}`);
                },

                travelerTotal(t) {
                    const hari = Math.max(this.calculateTravelerDays(t), 1);
                    const malam = Math.max(hari - 1, 0);
                    const uangHarianTotal = Number(t.uang_harian || 0) * hari;
                    const penginapanTotal = Number(t.penginapan || 0) * malam;
                    return uangHarianTotal + penginapanTotal + Number(t.represen || 0) +
                        Number(t.tiket || 0) + Number(t.transportasi || 0) + Number(t.sewa_kendaraan || 0);
                },
                kwitansiAmount() {
                    const amount = Number(this.form.jumlah_uang_kwitansi || 0);
                    return amount || this.travelers.reduce((total, traveler) => total + this.travelerTotal(traveler), 0);
                },
            }
        }
    </script>
</x-layouts.app>