<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 antialiased dark:bg-zinc-950">
        <a href="#auth-main-content" class="sr-only focus:not-sr-only focus:fixed focus:start-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-3 focus:text-sm focus:font-semibold focus:text-zinc-900 focus:shadow-lg dark:focus:bg-zinc-900 dark:focus:text-white">{{ __("Skip to main content") }}</a>
        <div class="min-h-svh lg:grid lg:grid-cols-[minmax(0,1fr)_minmax(24rem,32rem)]">
            <aside class="hidden border-e border-zinc-200 bg-zinc-100 p-10 dark:border-zinc-800 dark:bg-zinc-900 lg:flex lg:flex-col lg:justify-between">
                <a href="{{ route('home') }}" class="inline-flex w-fit items-center gap-3 font-semibold" wire:navigate>
                    <x-app-logo-icon class="size-8 fill-current text-zinc-900 dark:text-white" />
                    <span>afacere.online</span>
                </a>

                <div class="max-w-lg space-y-5">
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('For entrepreneurs who want clarity before another dashboard.') }}</p>
                    <h1 class="text-4xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ __('Understand your business. Decide what matters. Keep moving.') }}</h1>
                    <p class="text-base leading-7 text-zinc-600 dark:text-zinc-300">{{ __('Start with your account, then create the business workspace where your evaluation, priorities and progress live.') }}</p>
                </div>

                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Business decisions stay with you.') }}</p>
            </aside>

            <main id="auth-main-content" class="flex min-h-svh flex-col justify-center px-6 py-10 sm:px-10">
                <div class="mx-auto w-full max-w-md">
                    <a href="{{ route('home') }}" class="mb-8 inline-flex items-center gap-3 font-semibold lg:hidden" wire:navigate>
                        <x-app-logo-icon class="size-8 fill-current text-zinc-900 dark:text-white" />
                        <span>afacere.online</span>
                    </a>

                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm sm:p-8 dark:border-zinc-800 dark:bg-zinc-900">
                        {{ $slot }}
                    </div>

                    <p class="mt-6 text-center text-xs leading-5 text-zinc-500 dark:text-zinc-400">{{ __('Secure account access. Your business data remains under your control.') }}</p>
                </div>
            </main>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>