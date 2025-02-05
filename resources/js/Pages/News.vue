<template>
    <Head/>
    <Layout>
        <div class="bg-gray-50 text-black dark:bg-black dark:text-white/50">
            <div class="p-4 flex items-center">
                <TextInput
                    class="block w-full"
                    gray
                    id="search"
                    type="text"
                    v-model="form.search"
                    placeholder="Search news"
                    autofocus
                    @keyup.enter="search"
                />
                <span class="news-page--search-button" @click="search">
                    <svg viewBox="0 0 24 24" style="width: 18px">
                        <use xlink:href="../../images/main.svg#search"></use>
                    </svg>
                </span>
            </div>

            <main v-if="!articles.data.length" class="p-4">
                <div
                    class="gap-4 rounded-lg bg-white p-4 text-center shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] lg:pb-5 dark:bg-zinc-900 dark:ring-zinc-800"
                >
                    No news found.
                </div>
            </main>
            <main v-else class="p-4">
                <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                    <div
                        class="flex items-start gap-4 rounded-lg bg-white p-4 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] lg:pb-5 dark:bg-zinc-900 dark:ring-zinc-800"
                        v-for="item in articles.data" :key="item.id"
                    >
                        <div class="pt-3 sm:pt-5">
                            <h3
                                class="text-md text-black dark:text-white"
                            >
                                {{ moment(item.createdAt).format('LLL') }}
                            </h3>

                            <p class="mt-4 text-md/relaxed">
                                <img :src="item.imageUrl"
                                     style="width: 100%"
                                     alt=""
                                     referrerpolicy="no-referrer"
                                >
                            </p>

                            <p class="mt-4 text-md/relaxed">
                                {{ item.content || item.title }}
                            </p>

                            <p
                                class="mt-4 text-md/relaxed  text-black/50 dark:text-white/50"
                            >
                                <a class="py-3 underline hover:text-indigo-600 transition-colors duration-300 ease-in" target="_blank" :href="item.url">{{ item.source }}</a>
                            </p>
                        </div>
                    </div>
                </div>

                <Pagination
                    class="my-8"
                    :meta="articles.meta"
                    :data="form.data()"
                />
            </main>
        </div>
    </Layout>
</template>

<script setup lang="ts">
import {Head, router, useForm} from '@inertiajs/vue3';
import Layout from '@/Layouts/Layout.vue';
import moment from "moment/min/moment-with-locales";
import Pagination from "@/Components/Pagination.vue";
import Article from "@/models/Article";
import PaginatedList from "@/models/PaginatedList";
import TextInput from "@/Components/TextInput.vue";
import {onMounted, onUnmounted} from "vue";

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    articles: PaginatedList<Article>;
}>();

const form = useForm({
    search: route().params.search || '',
});

async function search() {
    router.reload({
        data: {
            page: 1,
            ...form.data()
        },
        only: ['articles'],
    });
}

onMounted(function() {
    window.Echo.channel('news')
        .listen('NewsReceived', () => {
            search();
        });
});

onUnmounted(function() {
    window.Echo.leaveChannel('news');
});

</script>

<style lang="scss">
.news-page--search-button {
    display: inline;
    padding: 8px;
    cursor: pointer;
    transition: all .3s;

    &:hover {
        color: #1e36ff;
    }
}

</style>
