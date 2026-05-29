<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppFooter from '@/components/AppFooter.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import { Toaster } from '@/components/ui/sonner';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <div class="flex min-h-[calc(100dvh-4rem)] flex-1 flex-col">
                <div class="flex-1">
                    <AppSidebarHeader :breadcrumbs="breadcrumbs" />
                    <slot />
                </div>
                <div class="border-t border-slate-100/70">
                    <AppFooter />
                </div>
            </div>
        </AppContent>
        <Toaster />
    </AppShell>
</template>
