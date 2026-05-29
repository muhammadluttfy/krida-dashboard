<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ChevronDown,
    FileText,
    Folder,
    LayoutGrid,
    LogOut,
    NotebookPen,
    Package,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AuthBrandLogo from '@/components/AuthBrandLogo.vue';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
    SidebarSeparator,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { logout } from '@/routes';
import type { NavItem } from '@/types';

const { isCurrentUrl } = useCurrentUrl();

const dashboardHref = '/dashboard';
const customerHref = '/master-file/customers';
const itemsHref = '/master-file/items';
const orderHref = '/transaksi/order';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboardHref,
        icon: LayoutGrid,
    },
];

const masterFileItems: NavItem[] = [
    {
        title: 'Master Customer',
        href: customerHref,
        icon: Folder,
    },
    {
        title: 'Master Items',
        href: itemsHref,
        icon: Package,
    },
];

const transactionItems: NavItem[] = [
    {
        title: 'Order',
        href: orderHref,
        icon: NotebookPen,
    },
];

const masterFileOpen = computed(() =>
    masterFileItems.some((item) => isCurrentUrl(item.href)),
);

const transactionOpen = computed(() =>
    transactionItems.some((item) => isCurrentUrl(item.href)),
);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader class="gap-4 px-4 py-5">
            <Link
                :href="dashboardHref"
                class="block rounded-2xl px-2 py-1"
            >
                <AuthBrandLogo class="h-auto w-full max-w-[210px]" />
            </Link>
        </SidebarHeader>

        <SidebarContent class="min-h-0 gap-4 overflow-y-auto px-1 pb-3">
            <SidebarGroup>
                <SidebarGroupContent>
                    <SidebarMenu>
                        <SidebarMenuItem v-for="item in mainNavItems" :key="item.title">
                            <SidebarMenuButton
                                as-child
                                :is-active="isCurrentUrl(item.href)"
                                :tooltip="item.title"
                                class="h-12 rounded-2xl px-4 text-sm font-medium"
                            >
                                <Link :href="item.href">
                                    <component :is="item.icon" />
                                    <span>{{ item.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarGroupContent>
            </SidebarGroup>

            <SidebarGroup class="gap-2">
                <SidebarGroupLabel class="px-3 text-xs font-semibold tracking-[0.16em] uppercase text-slate-500">
                    Master File
                </SidebarGroupLabel>
                <SidebarGroupContent>
                    <Collapsible :default-open="masterFileOpen">
                        <SidebarMenu>
                            <SidebarMenuItem>
                                <CollapsibleTrigger as-child>
                                    <SidebarMenuButton
                                        class="h-12 rounded-2xl px-4 text-sm font-medium"
                                    >
                                        <Folder />
                                        <span>Master File</span>
                                        <ChevronDown class="ml-auto size-4 transition-transform data-[state=open]:rotate-180" />
                                    </SidebarMenuButton>
                                </CollapsibleTrigger>
                                <CollapsibleContent>
                                    <SidebarMenuSub class="mt-2">
                                        <SidebarMenuSubItem
                                            v-for="item in masterFileItems"
                                            :key="item.title"
                                        >
                                            <SidebarMenuSubButton
                                                as-child
                                                :is-active="isCurrentUrl(item.href)"
                                                class="h-10 rounded-xl px-3"
                                            >
                                                <Link :href="item.href">
                                                    <component :is="item.icon" />
                                                    <span>{{ item.title }}</span>
                                                </Link>
                                            </SidebarMenuSubButton>
                                        </SidebarMenuSubItem>
                                    </SidebarMenuSub>
                                </CollapsibleContent>
                            </SidebarMenuItem>
                        </SidebarMenu>
                    </Collapsible>
                </SidebarGroupContent>
            </SidebarGroup>

            <SidebarGroup class="gap-2">
                <SidebarGroupLabel class="px-3 text-xs font-semibold tracking-[0.16em] uppercase text-slate-500">
                    Transaksi
                </SidebarGroupLabel>
                <SidebarGroupContent>
                    <Collapsible :default-open="transactionOpen">
                        <SidebarMenu>
                            <SidebarMenuItem>
                                <CollapsibleTrigger as-child>
                                    <SidebarMenuButton
                                        class="h-12 rounded-2xl px-4 text-sm font-medium"
                                    >
                                        <FileText />
                                        <span>Transaksi</span>
                                        <ChevronDown class="ml-auto size-4 transition-transform data-[state=open]:rotate-180" />
                                    </SidebarMenuButton>
                                </CollapsibleTrigger>
                                <CollapsibleContent>
                                    <SidebarMenuSub class="mt-2">
                                        <SidebarMenuSubItem
                                            v-for="item in transactionItems"
                                            :key="item.title"
                                        >
                                            <SidebarMenuSubButton
                                                as-child
                                                :is-active="isCurrentUrl(item.href)"
                                                class="h-10 rounded-xl px-3"
                                            >
                                                <Link :href="item.href">
                                                    <component :is="item.icon" />
                                                    <span>{{ item.title }}</span>
                                                </Link>
                                            </SidebarMenuSubButton>
                                        </SidebarMenuSubItem>
                                    </SidebarMenuSub>
                                </CollapsibleContent>
                            </SidebarMenuItem>
                        </SidebarMenu>
                    </Collapsible>
                </SidebarGroupContent>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter class="mt-auto px-3">
            <SidebarSeparator class="my-2" />
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        as-child
                        class="h-12 rounded-2xl px-4 text-sm font-medium text-slate-700 hover:text-slate-950"
                    >
                        <Link
                            :href="logout()"
                            method="post"
                            as="button"
                            data-test="sidebar-logout-button"
                        >
                            <LogOut />
                            <span>Logout</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
