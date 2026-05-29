<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLoginLayout from '@/layouts/auth/AuthLoginLayout.vue';
import { store } from '@/routes/login';

defineOptions({
    layout: AuthLoginLayout,
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Log in" />

    <div
        v-if="status"
        class="mb-6 rounded-2xl border border-emerald-200/80 bg-emerald-50 px-4 py-3 text-center text-sm font-medium text-emerald-700"
    >
        {{ status }}
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="email" class="text-slate-700">Email</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="Masukkan email Anda"
                    class="h-12 rounded-xl border-slate-200 bg-white px-4 shadow-sm transition focus-visible:border-red-400 focus-visible:ring-red-200"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="password" class="text-slate-700">Password</Label>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="Masukkan password Anda"
                    class="h-12 rounded-xl border-slate-200 bg-white px-4 shadow-sm transition focus-visible:border-red-400 focus-visible:ring-red-200"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center justify-between gap-4">
                <Label for="remember" class="flex items-center gap-3 text-sm text-slate-600">
                    <Checkbox
                        id="remember"
                        name="remember"
                        :tabindex="3"
                        class="border-slate-300 data-[state=checked]:border-red-500 data-[state=checked]:bg-red-500"
                    />
                    <span>Ingat saya</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-2 h-12 w-full rounded-xl bg-gradient-to-r from-red-600 to-red-500 text-base font-semibold shadow-[0_12px_30px_rgba(239,68,68,0.28)] transition hover:from-red-500 hover:to-red-600"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                Login
            </Button>
        </div>
    </Form>
</template>
