<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $description ?? 'afacere.online helps entrepreneurs understand their business, choose priorities and take practical action.' }}">
    <title>{{ $title ?? 'afacere.online' }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-50">
<header class="border-b border-zinc-200 bg-white/95 dark:border-zinc-800 dark:bg-zinc-950/95">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <nav class="flex min-h-16 items-center justify-between gap-4" aria-label="Main navigation">
            <a class="shrink-0 text-lg font-bold tracking-tight" href="{{ route('home') }}" wire:navigate>afacere.online</a>
            <div class="hidden items-center gap-1 md:flex">
                <a class="rounded-lg px-3 py-2 text-sm font-medium hover:bg-zinc-100 focus-visible:bg-zinc-100 dark:hover:bg-zinc-900 dark:focus-visible:bg-zinc-900" href="{{ route('public.how-it-works') }}" wire:navigate>How it works</a>
                <a class="rounded-lg px-3 py-2 text-sm font-medium hover:bg-zinc-100 focus-visible:bg-zinc-100 dark:hover:bg-zinc-900 dark:focus-visible:bg-zinc-900" href="{{ route('home') }}#ecosystem">Ecosystem</a>
                <a class="rounded-lg px-3 py-2 text-sm font-medium hover:bg-zinc-100 focus-visible:bg-zinc-100 dark:hover:bg-zinc-900 dark:focus-visible:bg-zinc-900" href="{{ route('public.pricing') }}" wire:navigate>Pricing</a>
                <a class="rounded-lg px-3 py-2 text-sm font-medium hover:bg-zinc-100 focus-visible:bg-zinc-100 dark:hover:bg-zinc-900 dark:focus-visible:bg-zinc-900" href="{{ route('public.faq') }}" wire:navigate>FAQ</a>
                <a class="ml-2 rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-700 focus-visible:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200 dark:focus-visible:bg-zinc-200" href="{{ route('register') }}" data-analytics-event="public.evaluator.cta">Start free</a>
            </div>
            <details class="relative md:hidden">
                <summary class="cursor-pointer list-none rounded-lg border border-zinc-300 px-3 py-2 text-sm font-semibold focus-visible:outline-3 focus-visible:outline-offset-2 dark:border-zinc-700"><span class="sr-only">Open navigation</span>Menu</summary>
                <div class="absolute right-0 z-20 mt-2 w-64 rounded-xl border border-zinc-200 bg-white p-2 shadow-xl dark:border-zinc-700 dark:bg-zinc-900">
                    <a class="block rounded-lg px-3 py-3 text-sm font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800" href="{{ route('public.how-it-works') }}" wire:navigate>How it works</a>
                    <a class="block rounded-lg px-3 py-3 text-sm font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800" href="{{ route('home') }}#ecosystem">Ecosystem</a>
                    <a class="block rounded-lg px-3 py-3 text-sm font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800" href="{{ route('public.pricing') }}" wire:navigate>Pricing</a>
                    <a class="block rounded-lg px-3 py-3 text-sm font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800" href="{{ route('public.faq') }}" wire:navigate>FAQ</a>
                    <div class="my-2 border-t border-zinc-200 dark:border-zinc-700"></div>
                    <a class="block rounded-lg px-3 py-3 text-sm font-medium hover:bg-zinc-100 dark:hover:bg-zinc-800" href="{{ route('login') }}">Sign in</a>
                    <a class="mt-1 block rounded-lg bg-zinc-900 px-3 py-3 text-center text-sm font-semibold text-white dark:bg-white dark:text-zinc-900" href="{{ route('register') }}" data-analytics-event="public.evaluator.cta">Start free</a>
                </div>
            </details>
        </nav>
    </div>
</header>
<main id="main-content" class="min-h-[calc(100vh-10rem)]">{{ $slot }}</main>
<footer class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-3 lg:px-8">
        <div><a class="font-bold tracking-tight" href="{{ route('home') }}" wire:navigate>afacere.online</a><p class="mt-3 max-w-sm text-sm text-zinc-600 dark:text-zinc-400">Business clarity, priorities and practical action in one progression.</p></div>
        <div><h2 class="text-sm font-semibold">Product</h2><div class="mt-3 grid gap-2 text-sm text-zinc-600 dark:text-zinc-400"><a class="hover:text-zinc-900 dark:hover:text-white" href="{{ route('public.how-it-works') }}" wire:navigate>How it works</a><a class="hover:text-zinc-900 dark:hover:text-white" href="{{ route('public.pricing') }}" wire:navigate>Pricing</a><a class="hover:text-zinc-900 dark:hover:text-white" href="{{ route('register') }}">Start the evaluator</a></div></div>
        <div><h2 class="text-sm font-semibold">Company & support</h2><div class="mt-3 grid gap-2 text-sm text-zinc-600 dark:text-zinc-400"><a class="hover:text-zinc-900 dark:hover:text-white" href="{{ route('public.about') }}" wire:navigate>About</a><a class="hover:text-zinc-900 dark:hover:text-white" href="{{ route('public.contact') }}" wire:navigate>Contact</a><a class="hover:text-zinc-900 dark:hover:text-white" href="{{ route('public.faq') }}" wire:navigate>FAQ</a><a class="hover:text-zinc-900 dark:hover:text-white" href="{{ route('public.legal') }}" wire:navigate>Legal</a><a class="hover:text-zinc-900 dark:hover:text-white" href="{{ route('login') }}">Sign in</a></div></div>
    </div>
</footer>
@fluxScripts
</body>
</html>
