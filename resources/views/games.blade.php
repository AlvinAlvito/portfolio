@extends('layouts.public')
@section('title', 'Games Lab - Avinto Project | Paris Alvito')
@section('description', 'Mainkan mini games buatan Avinto Project: Neon Runner, Tetris, dan Memory Match. Eksperimen interaktif dari Paris Alvito.')
@push('head')
<link rel="stylesheet" href="/assets/css/games.css">
@endpush
@section('content')
<section class="page-hero games-hero has-ambient"><div class="container reveal"><div class="breadcrumbs"><a href="{{ route('home') }}">Beranda</a><span>/</span><span>Games</span></div><span class="section-kicker">Avinto Games Lab</span><h1 class="section-title">Main sebentar,<br><span class="text-gradient">terinspirasi lebih lama.</span></h1><p>Eksperimen kecil dari Paris Alvito untuk membuat teknologi terasa ringan, menyenangkan, dan tetap rapi.</p></div></section>

<section class="section games-section"><div class="container"><div class="games-grid">
    <article class="game-card game-runner" data-game="runner"><div class="game-card-head"><div><span class="game-kicker">01 / Reflex</span><h2>Neon Runner</h2><p>Lompat dari rintangan dan bertahan selama mungkin.</p></div><div class="game-score"><small>Score</small><strong data-score>0</strong></div></div><canvas class="game-canvas" data-canvas width="720" height="250" aria-label="Permainan Neon Runner"></canvas><div class="game-controls"><span>Space / Tap untuk lompat</span><button class="btn btn-soft" data-reset>Reset game</button></div></article>
    <article class="game-card game-tetris" data-game="tetris"><div class="game-card-head"><div><span class="game-kicker">02 / Focus</span><h2>Violet Blocks</h2><p>Susun blok, bersihkan baris, dan kejar skor tertinggi.</p></div><div class="game-score"><small>Score</small><strong data-score>0</strong></div></div><canvas class="game-canvas tetris-canvas" data-canvas width="300" height="600" aria-label="Permainan Violet Blocks Tetris"></canvas><div class="game-controls"><span>← → gerak · ↑ rotasi · ↓ turun</span><button class="btn btn-soft" data-reset>Reset game</button></div></article>
    <article class="game-card game-memory" data-game="memory"><div class="game-card-head"><div><span class="game-kicker">03 / Recall</span><h2>Pixel Memory</h2><p>Temukan semua pasangan kartu dengan langkah sesedikit mungkin.</p></div><div class="game-score"><small>Moves</small><strong data-score>0</strong></div></div><div class="memory-board" data-board aria-label="Papan permainan Pixel Memory"></div><div class="game-controls"><span>Cocokkan semua pasangan</span><button class="btn btn-soft" data-reset>Reset game</button></div></article>
</div></div></section>

<section class="section surface-section games-cta"><div class="container"><div class="cta-card reveal"><div><span class="section-kicker">Build something real</span><h2>Punya ide produk digital?</h2><p>Setelah bermain, mari ubah ide Anda menjadi website atau aplikasi yang benar-benar digunakan.</p></div><a class="btn btn-light" href="{{ route('orders.create') }}">Mulai proyek <i data-lucide="arrow-up-right"></i></a></div></div></section>
@endsection
@push('scripts')
<script src="/assets/js/games.js"></script>
@endpush
