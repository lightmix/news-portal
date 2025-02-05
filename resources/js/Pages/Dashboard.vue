<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="space-y-4 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <Link :href="route('news')" class="block p-2 max-w-64 border border-solid rounded-md border-slate-400">
                <div>Total news received:</div>
                <div class="text-lg">{{ p.totalNews }}</div>
            </Link>
            <div class="p-2 max-w-64 border border-solid rounded-md border-slate-400">
                <div>Total users registered:</div>
                <div class="text-lg">{{ p.totalUsers }}</div>
            </div>
            <div class="p-2 max-w-64 border border-solid rounded-md border-slate-400">
                <div>Last registration at</div>
                <div class="text-lg">{{ moment(p.lastRegistrationAt).format('LLL') }}</div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/Layout.vue';
import { Link, Head } from '@inertiajs/vue3';
import moment from "moment/min/moment-with-locales";
import {onMounted, onUnmounted, ref} from "vue";

const props = defineProps<{
    p: {
        totalUsers: number
        lastRegistrationAt: string,
        totalNews: number,
    },
}>();

const p = ref(props.p)

onMounted(function() {
    window.Echo.channel('dashboard')
        .listen('DashboardUpdated', (data: any) => {
            p.value = data.p;
        });
});

onUnmounted(function() {
    window.Echo.leaveChannel('dashboard');
});

</script>
