<?php

namespace App\Http\Controllers;

use App\Models\Perdin;
use App\Models\SuratTugas;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard tunggal, tanpa login, langsung terbuka saat website diakses.
     * Alpine.js mengatur perpindahan antar section (sidebar) tanpa reload.
     */
    public function index(Request $request)
    {
        $perdins = Perdin::latest()->take(20)->get();
        $suratTugas = SuratTugas::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = $request->string('q')->toString();
                $query->where('nomor', 'like', "%{$term}%")
                    ->orWhere('mak', 'like', "%{$term}%")
                    ->orWhere('file_name', 'like', "%{$term}%");
            })
            ->latest()
            ->take(50)
            ->get();

        return view('dashboard', compact('perdins', 'suratTugas'));
    }
}
