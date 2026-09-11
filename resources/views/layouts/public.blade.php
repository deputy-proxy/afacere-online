<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $description ?? 'afacere.online helps entrepreneurs understand their business, choose priorities and take practical action.' }}">
    <title>{{ $title ?? 'afacere.online' }}</title>
    @fonts
    <style>
        :root { color-scheme: light; font-family: Inter, ui-sans-serif, system-ui, sans-serif; }
        body { margin: 0; color: #17202a; background: #f7f8fa; line-height: 1.6; }
        a { color: inherit; }
        .shell { max-width: 1120px; margin: 0 auto; padding: 0 24px; }
        header { background: #fff; border-bottom: 1px solid #e5e7eb; }
        nav { min-height: 72px; display: flex; align-items: center; justify-content: space-between; gap: 24px; }
        .brand { font-weight: 800; font-size: 1.2rem; text-decoration: none; }
        .nav-links { display: flex; flex-wrap: wrap; gap: 18px; font-size: .95rem; }
        .nav-links a { text-decoration: none; }
        main { padding: 72px 0; }
        .hero { max-width: 760px; }
        h1 { font-size: clamp(2.4rem, 6vw, 4.5rem); line-height: 1.05; margin: 0 0 24px; letter-spacing: -.04em; }
        h2 { font-size: 1.7rem; line-height: 1.2; margin-top: 48px; }
        .lead { font-size: 1.25rem; color: #4b5563; max-width: 680px; }
        .actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 28px; }
        .button { display: inline-block; padding: 12px 18px; border-radius: 10px; text-decoration: none; font-weight: 700; background: #111827; color: #fff; }
        .button.secondary { background: #fff; color: #111827; border: 1px solid #d1d5db; }
        .grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-top: 32px; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 24px; }
        .card h3 { margin-top: 0; }
        .muted { color: #6b7280; }
        footer { border-top: 1px solid #e5e7eb; background: #fff; padding: 32px 0; }
        .footer-links { display: flex; flex-wrap: wrap; gap: 16px; }
        @media (max-width: 760px) { .nav-links { display: none; } .grid { grid-template-columns: 1fr; } main { padding: 48px 0; } }
    </style>
</head>
<body>
<header>
    <div class="shell">
        <nav aria-label="Main navigation">
            <a class="brand" href="{{ route('home') }}">afacere.online</a>
            <div class="nav-links">
                <a href="{{ route('public.how-it-works') }}">How it works</a>
                <a href="{{ route('public.pricing') }}">Pricing</a>
                <a href="{{ route('public.faq') }}">FAQ</a>
                <a href="{{ route('login') }}">Sign in</a>
            </div>
        </nav>
    </div>
</header>
<main>
    <div class="shell">
        {{ $slot }}
    </div>
</main>
<footer>
    <div class="shell">
        <div class="footer-links">
            <a href="{{ route('public.about') }}">About</a>
            <a href="{{ route('public.contact') }}">Contact</a>
            <a href="{{ route('public.legal') }}">Legal</a>
            <a href="{{ route('public.faq') }}">FAQ</a>
        </div>
        <p class="muted">afacere.online turns business uncertainty into a practical progression of diagnosis, priorities and action.</p>
    </div>
</footer>
</body>
</html>
