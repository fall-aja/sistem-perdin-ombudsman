<x-layouts.app>
    <div x-data="perdinApp()" x-init="init()" x-cloak class="flex min-h-screen">

        {{-- SIDEBAR --}}
        <aside class="w-64 bg-slate-900 text-slate-200 flex-shrink-0 min-h-screen">
            <div class="p-5 border-b border-slate-700">
                <h1 class="font-bold text-lg leading-tight">Sistem PERDIN</h1>
                <p class="text-xs text-slate-400">Ombudsman RI</p>
                <a href="{{ route('template-settings.index') }}" class="mt-2 inline-block text-xs text-emerald-400 hover:underline">
                    ⚙ Pengaturan Template
                </a>
            </div>

            <nav class="p-3 space-y-1">
                <template x-for="section in sections" :key="section.key">
                    <button
                        @click="activeSection = section.key"
                        class="w-full text-left px-3 py-2 rounded-lg text-sm transition"
                        :class="activeSection === section.key ? 'bg-blue-600 text-white' : 'hover:bg-slate-800'"
                        x-text="section.label"></button>
                </template>
            </nav>

            <div class="p-3 mt-4 border-t border-slate-700">
                <p class="px-3 text-xs uppercase tracking-wide text-slate-500 mb-2">Riwayat</p>
                <div class="max-h-64 overflow-y-auto space-y-1">
                    @foreach ($perdins as $p)
                    <div class="px-3 py-2 text-xs rounded hover:bg-slate-800 cursor-pointer"
                        @click="loadPerdin({{ $p->id }})">
                        <div class="font-medium truncate">{{ \Illuminate\Support\Str::limit($p->maksud_perjalanan ?? '(belum diisi)', 30) }}</div>
                        <div class="text-slate-500">{{ $p->created_at->format('d M Y H:i') }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </aside>

        {{-- KONTEN --}}
        <main class="flex-1 p-8 space-y-6">

            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold" x-text="sections.find(s => s.key === activeSection)?.label"></h2>
                    <p class="text-sm text-slate-500" x-show="perdinId" x-text="'ID Dokumen: ' + perdinId"></p>
                </div>

                <div class="flex gap-2">
                    <button @click="save()" :disabled="saving"
                        class="px-4 py-2 rounded-lg bg-slate-700 text-white text-sm hover:bg-slate-800 disabled:opacity-50">
                        <span x-show="!saving">Simpan</span>
                        <span x-show="saving">Menyimpan...</span>
                    </button>
                    <button @click="generateExcel()" :disabled="!perdinId"
                        class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm hover:bg-emerald-700 disabled:opacity-40">
                        Generate Excel
                    </button>
                    <button @click="generatePdf()" :disabled="!perdinId"
                        class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm hover:bg-red-700 disabled:opacity-40">
                        Generate PDF
                    </button>
                    <button @click="printDocument()" :disabled="!perdinId"
                        class="px-4 py-2 rounded-lg bg-slate-500 text-white text-sm hover:bg-slate-600 disabled:opacity-40">
                        Print
                    </button>
                </div>
            </div>

            <div x-show="message" x-transition class="text-sm rounded-lg px-4 py-2"
                :class="messageType === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'"
                x-text="message"></div>

            <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
                {{-- FORM --}}
                <div class="xl:col-span-3 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <x-perdin.form />
                </div>

                {{-- PREVIEW --}}
                <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
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
                        key: 'pertanggung_jawaban',
                        label: 'Pertanggung Jawaban PERDIN'
                    },
                    {
                        key: 'ppa',
                        label: 'PPA PERDIN'
                    },
                    {
                        key: 'kwitansi',
                        label: 'Kwit PERDIN 1'
                    },
                    {
                        key: 'rincian',
                        label: 'Rincian PERDIN 1'
                    },
                    {
                        key: 'dpr',
                        label: 'DPR PERDIN'
                    },
                    {
                        key: 'pernyataan',
                        label: 'Pernyataan'
                    },
                ],

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
                    tahun_anggaran: new Date().getFullYear().toString(),
                    nomor_bukti_kwitansi: '',
                    mak: '',
                    sudah_terima_dari: 'BENDAHARA PENGELUARAN OMBUDSMAN RI',
                    jumlah_uang_kwitansi: 0,
                    nama_bendahara: '',
                    nip_bendahara: '',
                    lampiran_sppd_no: '',
                    tanggal_sppd: '',
                    dpr_nama: '',
                    dpr_nip: '',
                    dpr_jabatan: '',
                    nomor_spd: '',
                    tanggal_spd: '',
                },

                travelers: [],
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
                        jabatan: '',
                        es: '-',
                        gol: '-',
                        dari: 'Jakarta',
                        ke: '',
                        tanggal_mulai: '',
                        tanggal_sampai: '',
                        hari: 1,
                        uang_harian: 0,
                        penginapan: 0,
                        represen: 0,
                        tiket: 0,
                        transportasi: 0,
                        sewa_kendaraan: 0,
                    };
                },

                addTraveler() {
                    this.travelers.push(this.emptyTraveler());
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

                async save() {
                    this.saving = true;
                    try {
                        const res = await fetch('{{ route("perdin.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': this.csrf(),
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                ...this.form,
                                travelers: this.travelers,
                                rincian_items: this.rincianItems,
                                dpr_items: this.dprItems,
                            }),
                        });

                        const data = await res.json();

                        if (!res.ok) {
                            this.showMessage(data.message || 'Gagal menyimpan data.', 'error');
                            return;
                        }

                        this.perdinId = data.perdin.id;
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

                    this.perdinId = data.id;
                    Object.keys(this.form).forEach(key => {
                        if (key in data) this.form[key] = data[key] ?? '';
                    });
                    this.travelers = data.travelers?.length ? data.travelers : [this.emptyTraveler()];
                    this.rincianItems = data.rincian_items ?? [];
                    this.dprItems = data.dpr_items ?? [];
                },

                async generateExcel() {
                    if (!this.perdinId) return;
                    window.location.href = `/perdin/${this.perdinId}/excel`;
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

                travelerTotal(t) {
                    const hari = Math.max(Number(t.hari || 1), 1);
                    const malam = Math.max(hari - 1, 0);
                    const uangHarianTotal = Number(t.uang_harian || 0) * hari;
                    const penginapanTotal = Number(t.penginapan || 0) * malam;
                    return uangHarianTotal + penginapanTotal + Number(t.represen || 0) +
                        Number(t.tiket || 0) + Number(t.transportasi || 0) + Number(t.sewa_kendaraan || 0);
                },
            }
        }
    </script>
</x-layouts.app>