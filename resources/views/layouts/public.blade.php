<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'Paris Alvito (Alvin Alvito) melalui Avinto Project menyediakan jasa pembuatan dan pengembangan website, aplikasi mobile, UI/UX, dan sistem digital di Medan dan Indonesia.')">
    <meta name="keywords" content="Paris Alvito, Alvin Alvito, Alvito, Alvito Paris, jasa pembuatan website, jasa pengembangan web, jasa aplikasi mobile, Avinto Project">
    <meta name="author" content="Paris Alvito">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Avinto Project">
    <meta property="og:title" content="@yield('title', 'Paris Alvito - Avinto Project')">
    <meta property="og:description" content="@yield('description', 'Jasa pembuatan dan pengembangan website, aplikasi mobile, UI/UX, dan sistem digital oleh Paris Alvito.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ url('/assets/portfolio/hero.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Paris Alvito - Avinto Project')">
    <meta name="twitter:description" content="@yield('description', 'Jasa pembuatan dan pengembangan produk digital oleh Paris Alvito.')">
    <meta name="twitter:image" content="{{ url('/assets/portfolio/hero.png') }}">
    <title>@yield('title', 'Avinto Project - Digital Product Studio')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/purple-ui.css">
    <link rel="stylesheet" href="/assets/css/avinto.css">
    <script>
        (() => {
            const theme = localStorage.getItem('avinto-theme') || 'dark';
            const language = localStorage.getItem('avinto-language') || 'id';
            document.documentElement.dataset.theme = theme;
            document.documentElement.dataset.language = language;
            document.documentElement.lang = language;
        })();
    </script>
    @stack('head')
</head>
<body class="public-body">
@include('partials.page-loader')
<header class="site-header">
    <div class="container navbar">
        <a href="{{ route('home') }}" class="brand" aria-label="Avinto Project beranda">
            <span class="brand-mark avinto-mark">A</span><span>Avinto Project<small>Digital product studio</small></span>
        </a>
        <nav class="nav-links" id="mainNav">
            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a>
            <a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">Proyek</a>
            <a class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}">Tentang</a>
            <a class="nav-link" href="{{ route('home') }}#layanan">Layanan</a>
            <a class="nav-link" href="{{ route('home') }}#kontak">Kontak</a>
            <a class="nav-link {{ request()->routeIs('games') ? 'active' : '' }}" href="{{ route('games') }}">Games</a>
        </nav>
        <div class="nav-actions">
            <button class="nav-switch language-switch" id="languageSwitch" type="button" aria-label="Switch language" aria-pressed="false"><span>ID</span><span>EN</span></button>
            <button class="nav-switch theme-switch" id="themeSwitch" type="button" aria-label="Aktifkan dark mode" aria-pressed="false"><i data-lucide="sun" size="15"></i><i data-lucide="moon" size="15"></i></button>
            <button class="btn btn-outline admin-button" type="button" data-open-login><i data-lucide="lock-keyhole" size="16"></i> Admin</button>
            <a class="btn btn-primary" href="{{ route('orders.create') }}">Mulai Proyek <i data-lucide="arrow-up-right" size="17"></i></a>
            <button class="mobile-toggle" id="mobileToggle" aria-label="Buka menu"><i data-lucide="menu"></i></button>
        </div>
    </div>
</header>

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Person',
            '@id' => url('/tentang').'#paris-alvito',
            'name' => 'Paris Alvito',
            'alternateName' => ['Alvin Alvito', 'Alvito Paris'],
            'url' => url('/tentang'),
            'image' => url('/assets/portfolio/hero.png'),
            'jobTitle' => 'Fullstack Developer, IT Supervisor & CTO',
            'worksFor' => ['@type' => 'Organization', 'name' => 'Avinto Project', 'url' => url('/')],
            'sameAs' => [config('portfolio.contact.github'), config('portfolio.contact.linkedin'), config('portfolio.contact.instagram')],
            'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Medan', 'addressRegion' => 'Sumatera Utara', 'addressCountry' => 'ID'],
        ],
        [
            '@type' => 'ProfessionalService',
            '@id' => url('/').'#avinto-project',
            'name' => 'Avinto Project',
            'alternateName' => 'Jasa Pembuatan Web dan Aplikasi Paris Alvito',
            'url' => url('/'),
            'image' => url('/assets/portfolio/hero.png'),
            'description' => 'Jasa pembuatan dan pengembangan website, aplikasi mobile, UI/UX, dan sistem digital oleh Paris Alvito.',
            'areaServed' => 'Indonesia',
            'priceRange' => 'Rp',
            'telephone' => config('portfolio.contact.phone'),
            'email' => config('portfolio.contact.email'),
            'founder' => ['@id' => url('/tentang').'#paris-alvito'],
            'serviceType' => ['Web Development', 'Mobile Development', 'UI/UX Design', 'IT Support'],
        ],
        ['@type' => 'WebSite', 'name' => 'Avinto Project', 'url' => url('/'), 'inLanguage' => ['id-ID', 'en-US']],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>

<main>@yield('content')</main>

<footer class="site-footer" id="kontak">
    @php($contact = config('portfolio.contact'))
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand"><a href="{{ route('home') }}" class="brand"><span class="brand-mark avinto-mark">A</span><span>Avinto Project<small style="color:rgba(255,255,255,.55)">Build with purpose</small></span></a><p>Mitra teknologi untuk mengubah ide menjadi produk digital yang matang, menarik, dan siap digunakan.</p></div>
            <div class="footer-col"><h4>Jelajahi</h4><a href="{{ route('projects.index') }}">Semua Proyek</a><a href="{{ route('profile') }}">Profil & Pengalaman</a><a href="{{ route('orders.create') }}">Ajukan Proyek</a></div>
            <div class="footer-col"><h4>Layanan</h4><a href="{{ route('home') }}#layanan">Web Development</a><a href="{{ route('home') }}#layanan">Mobile & UI/UX</a><a href="{{ route('home') }}#layanan">IT Support</a><a href="{{ route('games') }}">Games</a></div>
            <div class="footer-col"><h4>Hubungi</h4><a href="https://wa.me/{{ $contact['whatsapp'] }}" target="_blank" rel="noopener">{{ $contact['phone'] }}</a><a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a><a href="{{ $contact['github'] }}" target="_blank" rel="noopener">GitHub /AlvinAlvito</a><p>{{ $contact['location'] }}</p></div>
        </div>
        <div class="footer-visitor"><div><span class="footer-visitor-icon"><i data-lucide="earth"></i></span><div><small>Global reach</small><h4>Visitor</h4><p>Melihat ide Avinto menjangkau lebih banyak tempat.</p></div></div><a href="https://info.flagcounter.com/APal" target="_blank" rel="noopener"><img src="https://s01.flagcounter.com/count2/APal/bg_FFFFFF/txt_000000/border_EBEBEB/columns_2/maxflags_10/viewers_3/labels_1/pageviews_1/flags_0/percent_0/" alt="Flag Counter pengunjung Avinto Project" loading="lazy"></a></div>
        <div class="footer-bottom"><span>&copy; {{ date('Y') }} Avinto Project. All rights reserved.</span><span>Designed and engineered by Paris Alvito.</span></div>
    </div>
</footer>

<div class="modal-backdrop {{ ($errors->has('login') || session('open_admin_login')) ? 'open' : '' }}" id="loginModal" role="dialog" aria-modal="true" aria-labelledby="loginTitle">
    <div class="modal-card"><div class="modal-head"><div><span class="section-kicker">Avinto workspace</span><h2 id="loginTitle" style="margin:0">Masuk ke dashboard</h2></div><button class="modal-close" type="button" data-close-login><i data-lucide="x"></i></button></div>
        @error('login')<div class="form-error">{{ $message }}</div>@enderror
        <form class="form-stack" method="POST" action="{{ route('admin.login') }}">@csrf
            <div class="field"><label for="adminUsername">Username</label><input class="input" id="adminUsername" name="username" autocomplete="username" required></div>
            <div class="field"><label for="adminPassword">Password</label><input class="input" type="password" id="adminPassword" name="password" autocomplete="current-password" required></div>
            <button class="btn btn-primary" type="submit">Masuk sebagai admin <i data-lucide="log-in" size="17"></i></button>
        </form>
    </div>
</div>

<button class="chat-launcher" id="chatLauncher" aria-label="Buka Vinto AI" aria-expanded="false"><i data-lucide="message-circle-more"></i><span class="chat-launcher-dot"></span></button>
<button class="nav-switch language-switch floating-language-switch" id="floatingLanguageSwitch" type="button" aria-label="Switch language" aria-pressed="false"><span>ID</span><span>EN</span></button>
<aside class="chat-panel" id="chatPanel" aria-label="Vinto AI" aria-hidden="true" data-endpoint="{{ route('chatbot.respond') }}">
    <div class="chat-head"><span class="chat-avatar"><i data-lucide="bot"></i></span><div><strong>Vinto AI</strong><small><span class="chat-online-dot"></span> Asisten Avinto Project</small></div><button class="chat-close" id="chatClose" type="button" aria-label="Tutup chatbot"><i data-lucide="x" size="18"></i></button></div>
    <div class="chat-conversation" id="chatMessages" role="log" aria-live="polite">
        <div class="chat-row bot"><span class="chat-mini-avatar"><i data-lucide="sparkles" size="14"></i></span><div class="chat-message">Halo! Saya Vinto. Ceritakan ide atau kebutuhan digital Anda, saya bantu pilih layanan dan langkah awal yang paling sesuai.</div></div>
        <div class="chat-suggestions" id="chatSuggestions"><button type="button" data-chat-prompt="Layanan apa saja yang tersedia?">Lihat layanan</button><button type="button" data-chat-prompt="Berapa kisaran biaya membuat website?">Kisaran biaya</button><button type="button" data-chat-prompt="Apa saja proyek yang pernah dikerjakan?">Lihat pengalaman</button></div>
    </div>
    <div class="chat-support-links"><a href="{{ route('projects.index') }}"><i data-lucide="panels-top-left" size="14"></i> Portofolio</a><a href="{{ route('orders.create') }}"><i data-lucide="send" size="14"></i> Mulai proyek</a></div>
    <form class="chat-form" id="chatForm"><textarea id="chatInput" rows="1" maxlength="1200" placeholder="Tanyakan tentang layanan atau proyek..." aria-label="Pesan untuk Vinto AI" required></textarea><button type="submit" id="chatSend" aria-label="Kirim pesan"><i data-lucide="send" size="18"></i></button></form>
    <p class="chat-disclaimer">Vinto adalah asisten AI dan dapat membuat kekeliruan.</p>
</aside>

<script src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) lucide.createIcons();
    const nav = document.getElementById('mainNav');
    document.getElementById('mobileToggle')?.addEventListener('click', () => nav.classList.toggle('open'));
    const modal = document.getElementById('loginModal');
    document.querySelectorAll('[data-open-login]').forEach(el => el.addEventListener('click', () => modal.classList.add('open')));
    document.querySelectorAll('[data-close-login]').forEach(el => el.addEventListener('click', () => modal.classList.remove('open')));
    modal?.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('open'); });
    const header = document.querySelector('.site-header');
    const updateHeader = () => header?.classList.toggle('scrolled', window.scrollY > 12);
    updateHeader(); window.addEventListener('scroll', updateHeader, {passive:true});
    const observer = new IntersectionObserver(entries => entries.forEach(entry => entry.isIntersecting && entry.target.classList.add('visible')), {threshold:.12});
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
});
</script>
<script src="/assets/js/preferences.js"></script>
<script src="/assets/js/chatbot.js"></script>
@stack('scripts')
</body>
</html>
