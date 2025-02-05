<template>
    <GuestLayout max-width="305px">
        <Head title="Confirm registration" />

        <div class="mt-4">
            <div>
                You must complete the registration<br> by starting our
                <a
                    :href="tgBotLink + (token ? '?start=' + token : '')"
                    target="_blank"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    <PrimaryButton class="ms-1">
                        Telegram Bot
                    </PrimaryButton>
                </a>
            </div>

            <template v-if="token">
                <p class="mt-3">
                    Or if you started the bot in another way, you can copy and paste this to the bot:
                </p>

                <p class="confirm-registration--token">
                    <input :value="token" readonly>
                    <a class="confirm-registration--copy" ref="confirmRegistrationCopy" @click="copyToken">
                        <svg viewBox="0 0 24 24" style="width: 18px">
                            <use xlink:href="../../../images/main.svg#copy"></use>
                        </svg>
                    </a>
                </p>
            </template>
        </div>

        <div class="mt-4 flex items-center justify-end">
            <Link
                :href="route('logout')"
                method="post"
                as="button"
                type="button"
                class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                Log Out
            </Link>
        </div>
    </GuestLayout>
</template>

<script setup lang="ts">
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import {Head, Link, router, usePage} from '@inertiajs/vue3';
import {computed, onMounted, onUnmounted, ref} from "vue";

const user = computed(() => usePage().props.auth.user);

const props = defineProps<{
    tgBotLink: string,
    token?: string;
}>(),
    confirmRegistrationCopy = ref<HTMLAnchorElement>();

let channel;

async function copyToken() {
    if (!props.token) {
        return;
    }

    try {
        await navigator.clipboard.writeText(props.token);
        confirmRegistrationCopy.value?.classList.add('confirm-registration--highlight');
        setTimeout(function() {
            confirmRegistrationCopy.value?.classList.remove('confirm-registration--highlight');
        }, 400);
    } catch (error: any) {
        console.error(error.message);
    }
}

onMounted(function() {
    window.Echo.private(`user.${user.value.id}`)
        .listen('RegistrationCompleted', () => {
            router.get('/', {}, {replace: true});
        });
});

onUnmounted(function() {
    if (user.value?.id) {
        window.Echo.private(`user.${user.value.id}`)
            .stopListening('RegistrationCompleted');
    } else {
        window.Echo.leaveAllChannels();
    }
});

</script>


<style lang="scss">
.confirm-registration--token {
    display: flex;
    margin-top: 4px;
    font-size: 11px;

    input {
        padding: 0;
        font-size: 11px;
        flex-grow: 1;
        border: none;

        &:focus {
            border: none;
            box-shadow: none;
        }
    }
}

.confirm-registration--copy {
    display: inline;
    padding: 8px;
    cursor: pointer;
    transition: all .3s;

    &:hover {
        color: #1e36ff;
    }
}

.confirm-registration--highlight {
    color: #1eff36 !important;
}
</style>
