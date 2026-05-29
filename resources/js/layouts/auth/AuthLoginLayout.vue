<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import AuthBrandLogo from '@/components/AuthBrandLogo.vue';
import { home } from '@/routes';

const page = usePage();
const appName = String(page.props.name ?? 'KRIDA');
const currentYear = new Date().getFullYear();

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative min-h-dvh overflow-hidden bg-[radial-gradient(circle_at_top_left,_rgba(239,68,68,0.10),_transparent_24%),radial-gradient(circle_at_top_right,_rgba(239,68,68,0.08),_transparent_22%),radial-gradient(circle_at_bottom_left,_rgba(59,130,246,0.05),_transparent_22%),linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)]"
    >
        <div
            class="pointer-events-none absolute -left-24 top-24 hidden h-80 w-80 rounded-full border border-red-100/80 lg:block"
        />
        <div
            class="pointer-events-none absolute -right-36 top-[-4rem] hidden h-72 w-72 rounded-full border-[22px] border-red-100/60 lg:block"
        />
        <div
            class="pointer-events-none absolute bottom-0 left-0 h-56 w-56 bg-[linear-gradient(135deg,rgba(239,68,68,0.06)_25%,transparent_25%,transparent_50%,rgba(239,68,68,0.06)_50%,rgba(239,68,68,0.06)_75%,transparent_75%,transparent)] bg-[length:48px_48px] opacity-60"
        />

        <div class="relative mx-auto grid min-h-dvh w-full max-w-7xl lg:grid-cols-2">
            <section
                class="hidden flex-col justify-center px-8 py-12 lg:flex lg:px-10 xl:px-16"
            >
                <Link
                    :href="home()"
                    class="inline-flex self-start"
                >
                    <AuthBrandLogo
                        class="h-auto w-[14rem] max-w-full drop-shadow-[0_10px_30px_rgba(239,68,68,0.10)]"
                    />
                </Link>

                <div class="mt-14 max-w-xl">
                    <div class="mb-6 h-1.5 w-16 rounded-full bg-red-500" />
                    <h1 class="text-4xl leading-tight font-semibold tracking-tight text-neutral-950">
                        Selamat datang kembali! <span class="inline-block">👋</span>
                    </h1>
                    <p class="mt-4 max-w-lg text-lg leading-8 text-slate-600">
                        Silakan login untuk melanjutkan ke sistem administrasi
                        dan transaksi {{ appName }}.
                    </p>
                </div>
            </section>

            <section
                class="flex items-center justify-center px-4 py-8 sm:px-6 lg:px-8 lg:py-12"
            >
                <div class="w-full max-w-md lg:max-w-[34rem]">
                    <div class="mb-6 flex flex-col items-center gap-3 text-center lg:hidden">
                        <Link
                            :href="home()"
                            class="inline-flex"
                        >
                            <AuthBrandLogo class="h-auto w-[11rem] max-w-full" />
                        </Link>
                    </div>

                    <div
                        class="rounded-[28px] border border-slate-200/80 bg-white/95 p-6 shadow-[0_20px_60px_rgba(15,23,42,0.08)] backdrop-blur md:p-8 lg:p-10"
                    >
                        <div class="mb-8 text-center">
                            <h1 class="text-2xl font-semibold tracking-tight text-slate-950">
                                {{ title ?? 'Login' }}
                            </h1>
                            <p class="mt-1 text-sm leading-7 text-slate-500">
                                {{ description ?? 'Masukkan email dan password Anda' }}
                            </p>
                        </div>

                        <slot />

                        <div class="mt-10 border-t border-slate-200 pt-6 text-center text-sm text-slate-500">
                            © {{ currentYear }} {{ appName }}
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
