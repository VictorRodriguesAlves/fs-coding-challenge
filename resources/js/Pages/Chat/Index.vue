<template>
    <div :class="isDark ? 'dark' : ''" class="flex h-screen bg-gray-50 dark:bg-gray-900 transition-colors">
        <ContactList
            :contacts="contacts"
            :selected-contact-id="selectedContact?.id"
            :is-dark="isDark"
            @toggle-theme="toggleTheme"
        />

        <div class="flex-1 flex flex-col">
            <template v-if="selectedContact">
                <ChatWindow
                    :selected-contact="selectedContact"
                    :messages="localMessages"
                    :loading-more="loadingMore"
                    @load-more="loadMoreMessages"
                />
                <ChatInput
                    :channels="channels"
                    :contact-id="selectedContact.id"
                />
            </template>
            <EmptyState v-else />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watchEffect, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import ContactList from '@/Components/Chat/ContactList.vue'
import ChatWindow from '@/Components/Chat/ChatWindow.vue'
import ChatInput from '@/Components/Chat/ChatInput.vue'
import EmptyState from '@/Components/Chat/EmptyState.vue'

const props = defineProps({
    contacts: Array,
    messages: Object,
    selectedContact: Object,
    channels: Array,
})

const isDark = ref(false)
let initialTheme = false
if (typeof window !== 'undefined') {
    const savedTheme = localStorage.getItem('theme')
    initialTheme = savedTheme === 'dark'
}
isDark.value = initialTheme

const toggleTheme = () => {
    isDark.value = !isDark.value
}

watchEffect(() => {
    if (typeof window !== 'undefined') {
        localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
        document.documentElement.classList.toggle('dark', isDark.value)
    }
})

const loadingMore = ref(false)
const localMessages = ref(props.messages ? props.messages.data.slice().reverse() : [])
const nextPageUrl = ref(props.messages ? props.messages.next_page_url : null)

watch(() => props.selectedContact?.id, () => {
    localMessages.value = props.messages ? props.messages.data.slice().reverse() : []
    nextPageUrl.value = props.messages ? props.messages.next_page_url : null
})

watch(() => props.messages, (newPaginator) => {
    if (newPaginator) {
        const newServerMessages = newPaginator.data.slice().reverse()

        if (newPaginator.current_page === 1) {
            const localMessageMap = new Map(localMessages.value.map(m => [m.id, m]));

            newServerMessages.forEach(serverMsg => {
                localMessageMap.set(serverMsg.id, serverMsg);
            });

            localMessages.value = Array.from(localMessageMap.values())
                .sort((a, b) => new Date(a.date + ' ' + a.time) - new Date(b.date + ' ' + b.time));
        }

        nextPageUrl.value = newPaginator.next_page_url;
    }
}, { deep: true })

const loadMoreMessages = () => {
    if (!nextPageUrl.value || loadingMore.value) return;

    loadingMore.value = true;

    router.get(nextPageUrl.value, {
        contact_id: props.selectedContact.id,
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            const newMessages = page.props.messages.data.slice().reverse();
            localMessages.value = [...newMessages, ...localMessages.value];
            nextPageUrl.value = page.props.messages.next_page_url;
            loadingMore.value = false;
        },
        onError: () => loadingMore.value = false
    })
}

let pollingInterval = null
onMounted(() => {
    pollingInterval = setInterval(() => {
        router.reload({
            preserveState: true,
            preserveScroll: true,
            only: ['contacts', 'messages', 'selectedContact'],
        })
    }, 5000)
})

onUnmounted(() => {
    if (pollingInterval) {
        clearInterval(pollingInterval)
    }
})
</script>