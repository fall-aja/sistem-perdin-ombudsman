<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistem PERDIN — Ombudsman RI</title>

    {{-- Tailwind via CDN untuk kemudahan setup awal.
         Untuk produksi, ganti dengan build Vite (lihat README langkah 7). --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
    {{ $slot }}
</body>
</html>
