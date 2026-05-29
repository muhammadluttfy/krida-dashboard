<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Bell } from 'lucide-vue-next';
import { computed } from 'vue';
import UserMenuContent from '@/components/UserMenuContent.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { getInitials } from '@/composables/useInitials';
import type { BreadcrumbItem } from '@/types';

const props = withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const user = computed(() => page.props.auth.user);
const title = computed(
    () => props.breadcrumbs[props.breadcrumbs.length - 1]?.title ?? 'Dashboard',
);
</script>

<template>
    <header
        class="flex h-20 shrink-0 items-center justify-between gap-4 border-b border-slate-200 bg-white px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-16 md:px-8"
    >
        <div class="flex items-center gap-4">
            <SidebarTrigger
                class="flex size-11 shrink-0 items-center justify-center rounded-2xl border border-slate-200 bg-white p-0 text-slate-700 shadow-sm"
            />
            <div class="min-w-0">
                <h1 class="text-xl font-semibold tracking-tight text-slate-950">
                    {{ title }}
                </h1>
                <Breadcrumbs
                    v-if="breadcrumbs && breadcrumbs.length > 1"
                    class="mt-1"
                    :breadcrumbs="breadcrumbs"
                />
            </div>
        </div>

        <div class="flex items-center gap-3">
            <Button
                variant="ghost"
                size="icon"
                class="relative size-10 rounded-full border border-slate-200 text-slate-700"
            >
                <Bell class="size-5" />
                <span
                    class="absolute right-1 top-1 size-2 rounded-full bg-red-500"
                />
            </Button>

            <div class="hidden h-10 w-px bg-slate-200 sm:block" />

            <DropdownMenu>
                <DropdownMenuTrigger :as-child="true">
                    <Button
                        variant="ghost"
                        class="flex h-auto items-center gap-3 rounded-full px-1 py-1"
                    >
                        <Avatar class="size-11 overflow-hidden rounded-full">
                            <AvatarFallback class="bg-red-100 font-semibold text-red-600">
                                {{ getInitials(user?.name ?? 'User') }}
                            </AvatarFallback>
                        </Avatar>
                        <div class="hidden text-left sm:block">
                            <div class="text-sm font-semibold text-slate-950">
                                {{ user?.name ?? 'Admin User' }}
                            </div>
                            <div class="text-xs text-slate-500">
                                Administrator
                            </div>
                        </div>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    class="w-64 rounded-2xl border-slate-200 bg-white p-2"
                    align="end"
                    :side-offset="10"
                >
                    <UserMenuContent :user="user" />
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </header>
</template>
