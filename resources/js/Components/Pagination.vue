<template>
    <nav class="relative flex flex-wrap justify-center">
        <Link v-for="link in meta.links" :key="link.label"
              @click="toPage($event, link)"
              :preserveScroll="preserveScroll"
              :href="link.url || ''"
              v-html="link.label"
              class="flex items-center justify-center px-3 py-2 text-sm rounded-lg text-gray-600"
              :class="{ 'bg-gray-200': link.active, '!text-gray-300': !link.url }"
        />
    </nav>
</template>

<script setup lang="ts">
import {Link, router} from '@inertiajs/vue3'
import PaginatedList, {PageLink} from "@/models/PaginatedList";

const props = defineProps<{
    meta: PaginatedList<any>['meta'],
    preserveScroll?: boolean,
    data?: any,
}>();

function toPage(event: Event, link: PageLink)
{
    event.preventDefault();

    if (link.active && link.url) {
        router.get(link.url, props.data);
    }
}

</script>
