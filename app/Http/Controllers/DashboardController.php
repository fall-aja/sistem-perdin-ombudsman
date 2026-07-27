<?php

namespace App\Http\Controllers;

use App\Models\Perdin;

class DashboardController extends Controller
{
    /**
     * Dashboard tunggal, tanpa login, langsung terbuka saat website diakses.
     * Alpine.js mengatur perpindahan antar section (sidebar) tanpa reload.
     */
    public function index()
    {
        $perdins = Perdin::latest()->take(20)->get();

        return view('dashboard', compact('perdins'));
    }
}
