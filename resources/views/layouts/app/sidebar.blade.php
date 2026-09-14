<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <a href="#app-main-content" class="sr-only focus:not-sr-only focus:fixed focus:start-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-3 focus:text-sm focus:font-semibold focus:text-zinc-900 focus:shadow-lg dark:focus:bg-zinc-900 dark:focus:text-white">{{ __('Skip to main content') }}</a>
        @php
            $currentBusiness = app(\App\Services\BusinessContextService::class)->current(auth()->user());
            $businesses = app(\App\Services\BusinessContextService::class)->forUser(auth()->user());
        @endphp

        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav aria-label="{{ __('Primary navigation') }}">
                <flux:sidebar.group :heading="__('Workspace')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>

                    @if ($currentBusiness === null)
                        <flux:sidebar.item icon="building-office" :href="route('business.onboarding')" :current="request()->routeIs('business.onboarding')" wire:navigate>
                            {{ __('Set up your business') }}
                        </flux:sidebar.item>
                    @else
                        <flux:sidebar.item icon="clipboard-document-check" :href="route('business.evaluation')" :current="request()->routeIs('business.evaluation')" wire:navigate>
                            {{ __('Evaluation') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="magnifying-glass" :href="route('business.evaluation.diagnosis')" :current="request()->routeIs('business.evaluation.diagnosis')" wire:navigate>
                            {{ __('Diagnosis') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="list-bullet" :href="route('business.action-plan')" :current="request()->routeIs('business.action-plan')" wire:navigate>
                            {{ __('Action Plan') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="book-open" :href="route('business.guides')" :current="request()->routeIs('business.guides*')" wire:navigate>
                            {{ __('Guides') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="sparkles" :href="route('business.opportunities')" :current="request()->routeIs('business.opportunities*')" wire:navigate>
                            {{ __('Opportunities') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="chart-bar" :href="route('business.monitor')" :current="request()->routeIs('business.monitor')" wire:navigate>
                            {{ __('Monitor') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="globe-alt" :href="route('business.ecosystem')" :current="request()->routeIs('business.ecosystem')" wire:navigate>
                            {{ __('Ecosystem') }}
                        </flux:sidebar.item>
                    @endif

                    <flux:sidebar.item icon="bell" :href="route('business.notifications')" :current="request()->routeIs('business.notifications')" wire:navigate>
                        {{ __('Notifications') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @if ($currentBusiness !== null && count($businesses) > 1)
                    <flux:sidebar.group :heading="__('Business')" class="grid">
                        <flux:sidebar.item icon="building-office-2" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                            <span class="truncate">{{ $currentBusiness->name }}</span>
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @elseif ($currentBusiness !== null)
                    <flux:sidebar.group :heading="__('Business')" class="grid">
                        <flux:sidebar.item icon="building-office-2" :href="route('dashboard')" :current="false" wire:navigate>
                            <span class="truncate">{{ $currentBusiness->name }}</span>
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endif

                <flux:sidebar.group :heading="__('Account')" class="grid">
                    <flux:sidebar.item icon="credit-card" :href="route('account.subscription')" :current="request()->routeIs('account.subscription')" wire:navigate>
                        {{ __('Subscription') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user-circle" :href="route('profile.edit')" :current="request()->routeIs('profile.edit')" wire:navigate>
                        {{ __('Profile') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="paint-brush" :href="route('appearance.edit')" :current="request()->routeIs('appearance.edit')" wire:navigate>
                        {{ __('Appearance') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="shield-check" :href="route('security.edit')" :current="request()->routeIs('security.edit')" wire:navigate>
                        {{ __('Security') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="arrow-down-tray" :href="route('account.data.export')" :current="false">
                        {{ __('Export data') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="archive-box" :href="route('account.data.deletion.status')" :current="request()->routeIs('account.data.deletion.status')" wire:navigate>
                        {{ __('Data & deletion') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <flux:header class="lg:hidden" aria-label="{{ __('Mobile navigation') }}">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
            <flux:spacer />
            <flux:dropdown position="top" align="end">
                <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />
                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />
                                <div class="grid min-w-0 flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>
                    <flux:menu.separator />
                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2" data-test="logout-button">{{ __('Log out') }}</flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <main id="app-main-content" class="min-w-0">
            {{ $slot }}
        </main>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist
        @fluxScripts
    </body>
</html>