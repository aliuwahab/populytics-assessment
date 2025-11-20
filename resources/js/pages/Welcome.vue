<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Populytics Assessment">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]"
    >
        <header
            class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl"
        >
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>
        <div
            class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0"
        >
            <main
                class="flex w-full max-w-[335px] flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row"
            >
                <div
                    class="flex-1 rounded-br-lg rounded-bl-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:rounded-tl-lg lg:rounded-br-none lg:p-20 dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                >
                    <h1 class="mb-1 text-lg font-semibold">Populytics Assessment</h1>
                    <p class="mb-4 text-[#706f6c] dark:text-[#A1A09A]">
                        Explore how we ingest, process, and visualize RSS feeds for each user:
                    </p>
                    <ol class="mb-6 list-decimal space-y-4 pl-5 text-[#1b1b18] dark:text-[#EDEDEC]">
                        <li>
                            <span class="font-semibold">Register or log in.</span>
                            <span class="block text-xs text-[#706f6c] dark:text-[#A1A09A]">Use the links above to create an account or return to your workspace.</span>
                        </li>
                        <li>
                            <span class="font-semibold">Open your dashboard to add feeds.</span>
                            <span class="block text-xs text-[#706f6c] dark:text-[#A1A09A]">Each feed is tied to your profile so you can curate a personal reading list.</span>
                        </li>
                        <li>
                            <span class="font-semibold">Process items via the worker.</span>
                            <span class="block text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                Run <code class="rounded bg-[#f1f0ed] px-1 py-0.5 font-mono text-xs dark:bg-[#1f1f1d]">php artisan queue:work</code> (or
                                <code class="rounded bg-[#f1f0ed] px-1 py-0.5 font-mono text-xs dark:bg-[#1f1f1d]">php artisan feeds:process --sync</code>) to pull the latest stories.
                            </span>
                        </li>
                    </ol>
                    <div class="flex flex-wrap gap-3 text-sm leading-normal">
                        <Link
                            :href="$page.props.auth.user ? dashboard() : login()"
                            class="inline-block rounded-sm border border-black bg-[#1b1b18] px-5 py-1.5 text-sm text-white hover:border-black hover:bg-black dark:border-[#eeeeec] dark:bg-[#eeeeec] dark:text-[#1C1C1A] dark:hover:border-white dark:hover:bg-white"
                        >
                            {{ $page.props.auth.user ? 'Go to dashboard' : 'Log in' }}
                        </Link>
                        <Link
                            v-if="!$page.props.auth.user && canRegister"
                            :href="register()"
                            class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                        >
                            Create an account
                        </Link>
                    </div>
                </div>
                <div
                    class="relative -mb-px aspect-335/376 w-full shrink-0 overflow-hidden rounded-t-lg bg-[#fff2f2] lg:mb-0 lg:-ml-px lg:aspect-auto lg:w-[438px] lg:rounded-t-none lg:rounded-r-lg dark:bg-[#1D0002]"
                >
                    <div class="absolute inset-0 opacity-50">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,#ffffffb3,transparent_55%)]"></div>
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom,#fbbf2477,transparent_60%)]"></div>
                        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width%3D%27180%27 height%3D%27180%27 viewBox%3D%270 0 180 180%27 xmlns%3D%27http://www.w3.org/2000/svg%27%3E%3Cg fill%3D%27%23ffffff%27 fill-opacity%3D%270.1%27%3E%3Cpath d%3D%27M90 0h1v180h-1zM0 90h180v1H0z%27/%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
                    </div>
                    <div class="relative flex h-full flex-col items-center justify-center gap-4 p-10 text-center text-[#111827] dark:text-[#f4f4f5]">
                        <p class="text-xs uppercase tracking-[0.6em] text-[#fb923c] dark:text-[#fde68a]">Welcome to</p>
                        <div class="text-5xl font-black tracking-[0.2em]">
                            <span class="text-[#fb7185]">Po</span>
                            <span class="text-[#2563eb]">Pu</span>
                            <span class="text-[#10b981]">Ly</span>
                            <span class="text-[#facc15]">Tics</span>
                        </div>
                        <div class="text-xl font-semibold uppercase tracking-[0.35em] text-[#0f172a] dark:text-white">Assessment</div>
                        <p class="max-w-sm text-xs leading-relaxed text-[#475569] dark:text-[#cbd5f5]">
                            Curate feeds, queue background processing, and review your personalized stream with Populytics Assessment.
                        </p>
                    </div>

                    <div
                        class="absolute inset-0 rounded-t-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:overflow-hidden lg:rounded-t-none lg:rounded-r-lg dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                    />
                </div>
            </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
