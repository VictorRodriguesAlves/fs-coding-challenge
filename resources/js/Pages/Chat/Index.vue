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
                    :messages="messages"
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
import { ref, onMounted, onUnmounted, watchEffect } from 'vue'
import { router } from '@inertiajs/vue3'
import ContactList from '@/Components/Chat/ContactList.vue'
import ChatWindow from '@/Components/Chat/ChatWindow.vue'
import ChatInput from '@/Components/Chat/ChatInput.vue'
import EmptyState from '@/Components/Chat/EmptyState.vue'

const props = defineProps({
    contacts: Array,
    messages: Array,
    selectedContact: Object,
    channels: Array,
})

let initialTheme = false
if (typeof window !== 'undefined') {
    const savedTheme = localStorage.getItem('theme')
    initialTheme = savedTheme === 'dark'
}

const isDark = ref(initialTheme)

const toggleTheme = () => {
    isDark.value = !isDark.value
}

watchEffect(() => {
    if (typeof window !== 'undefined') {
        localStorage.setItem('theme', isDark.value ? 'dark' : 'light')
        document.documentElement.classList.toggle('dark', isDark.value)
    }
})

let pollingInterval = null

onMounted(() => {
    pollingInterval = setInterval(() => {
        router.reload({
            preserveState: true,
            preserveScroll: true,
            only: ['contacts', 'messages', 'selectedContact'],
        })
    }, 1000)
})

onUnmounted(() => {
    if (pollingInterval) {
        clearInterval(pollingInterval)
    }
})
</script>