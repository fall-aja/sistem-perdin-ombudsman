@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Recycle Bin - Perdin</h1>

    <p class="text-sm text-slate-600 mb-4">Daftar dokumen yang sudah dihapus. Anda dapat memulihkan atau menghapus permanen.</p>

    <table class="w-full border-collapse border border-slate-200 text-sm">
        <thead class="bg-slate-100">
            <tr>
                <th class="p-2 border">ID</th>
                <th class="p-2 border">Maksud Perjalanan</th>
                <th class="p-2 border">Diajukan Oleh</th>
                <th class="p-2 border">Dihapus Pada</th>
                <th class="p-2 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($deleted as $p)
                <tr class="even:bg-slate-50">
                    <td class="p-2 border">{{ $p->id }}</td>
                    <td class="p-2 border">{{ \\Illuminate\Support\Str::limit($p->maksud_perjalanan ?? '(kosong)', 80) }}</td>
                    <td class="p-2 border">{{ $p->nama_pengaju }}</td>
                    <td class="p-2 border">{{ optional($p->deleted_at)->format('d M Y H:i') }}</td>
                    <td class="p-2 border">
                        <form method="POST" action="{{ route('perdin.restore', $p->id) }}" style="display:inline">
                            @csrf
                            <button class="px-2 py-1 bg-emerald-600 text-white rounded text-xs">Pulihkan</button>
                        </form>

                        <form method="POST" action="{{ route('perdin.force-delete', $p->id) }}" style="display:inline" onsubmit="return confirm('Hapus permanen?')">
                            @csrf
                            <button class="px-2 py-1 bg-red-600 text-white rounded text-xs ml-2">Hapus Permanen</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-4 text-center text-slate-500">Tidak ada file terhapus.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
