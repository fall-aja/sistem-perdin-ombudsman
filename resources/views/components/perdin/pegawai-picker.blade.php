{{--
    Komponen reusable: ketik 1 huruf -> dropdown muncul berisi pegawai
    yang cocok (dari tabel master `pegawai`, hasil import DUK). Klik salah
    satu untuk isi otomatis Nama + NIP sekaligus. Kotak NIP tetap terlihat
    dan bisa diedit manual juga.

    Cara pakai:
    <x-perdin.pegawai-picker
        label="Nama PPK"
        nama-model="form.nama_ppk"
        nip-model="form.nip_ppk"
        field-id-expr="'ppk'"
    />
--}}
@props(['label', 'namaModel', 'nipModel' => null, 'jabatanModel' => null, 'fieldIdExpr'])

<div>
    <div class="relative" x-data="{ get fid() { return {{ $fieldIdExpr }}; } }" @click.outside="pegawaiOpen[fid] = false">
        <label class="text-xs font-medium text-slate-500">{{ $label }}</label>
        <input
            type="text"
            autocomplete="off"
            x-model="{{ $namaModel }}"
            @input.debounce.300ms="await searchPegawai(fid, {{ $namaModel }})"
            @focus="if (pegawaiResults[fid]?.length) pegawaiOpen[fid] = true"
            class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2"
        >

        <div
            x-show="pegawaiOpen[fid] && pegawaiResults[fid]?.length"
            x-cloak
            class="absolute z-20 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-lg max-h-56 overflow-y-auto"
        >
            <template x-for="p in (pegawaiResults[fid] || [])" :key="p.nip">
                <button
                    type="button"
                    @click="{{ $namaModel }} = p.nama; @if($nipModel) {{ $nipModel }} = p.nip; @endif @if($jabatanModel) {{ $jabatanModel }} = p.jabatan; @endif pegawaiOpen[fid] = false"
                    class="w-full text-left px-3 py-2 text-sm hover:bg-emerald-50 border-b border-slate-100 last:border-b-0"
                >
                    <div class="font-medium text-slate-700" x-text="p.nama"></div>
                    <div class="text-xs text-slate-400" x-text="p.nip + (p.jabatan ? ' · ' + p.jabatan : '')"></div>
                </button>
            </template>
        </div>
    </div>

    @if($nipModel)
        <div class="mt-2">
            <label class="text-xs font-medium text-slate-500">NIP</label>
            <input
                type="text"
                x-model="{{ $nipModel }}"
                placeholder="Terisi otomatis, atau ketik manual"
                class="mt-1 w-full rounded-lg border border-slate-300 text-sm px-3 py-2"
            >
        </div>
    @endif
</div>