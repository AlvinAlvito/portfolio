@extends('layouts.public')
@section('title', 'Permintaan Terkirim - Avinto Project')
@section('content')
<section class="success-page has-ambient">@include('partials.ambient-shapes', ['variant' => 'sparse'])<div class="container"><div class="card success-card reveal"><span class="success-icon"><i data-lucide="check"></i></span><span class="section-kicker">Request received</span><h1>Terima kasih sudah berbagi ide.</h1><p>Permintaan proyek Anda sudah masuk ke dashboard Avinto Project. Kami akan mempelajari detailnya dan menghubungi Anda melalui kanal yang dipilih.</p><div class="hero-actions"><a class="btn btn-primary" href="{{ route('projects.index') }}">Lihat portofolio</a><a class="btn btn-soft" href="{{ route('home') }}">Kembali ke beranda</a></div></div></div></section>
@endsection
