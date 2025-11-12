<template>
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-semibold">
                {{ selectedContact.name.charAt(0) }}
            </div>
            <div>
                <h2 class="font-semibold text-gray-900 dark:text-white">
                    {{ selectedContact.name }}
                </h2>
            </div>
        </div>
    </div>

    <div
        ref="messagesContainer"
        class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900"
        @scroll="onScroll"
    >
        <div v-if="loadingMore" class="flex justify-center my-4">
            <Loader2 class="w-6 h-6 text-gray-400 animate-spin" />
        </div>

        <div v-for="(group, date) in groupedMessages" :key="date">
            <div class="flex items-center justify-center my-4">
                <div class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded-full">
                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ date }}</span>
                </div>
            </div>

            <MessageItem
                v-for="message in group"
                :key="message.id"
                :message="message"
                :contact-name="selectedContact.name"
                class="mb-1"
            />
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import MessageItem from '@/Components/Chat/MessageItem.vue'
import { Loader2 } from 'lucide-vue-next'

const props = defineProps({
    messages: Array,
    selectedContact: Object,
    loadingMore: Boolean,
})

const emit = defineEmits(['loadMore'])

const messagesContainer = ref(null)

watch(() => props.messages.length, (newLength, oldLength) => {
    if (newLength > oldLength) {
        nextTick(() => scrollToBottom())
    }
})

watch(() => props.selectedContact?.id, () => {
    nextTick(() => scrollToBottom())
}, { immediate: true })

const onScroll = (e) => {
    if (e.target.scrollTop === 0) {
        emit('loadMore')
    }
}

const groupedMessages = computed(() => {
    if (!props.messages) return {}
    const groups = {}
    props.messages.forEach(message => {
        const date = formatDate(message.date)
        if (!groups[date]) {
            groups[date] = []
        }
        groups[date].push(message)
    })
    return groups
})

const scrollToBottom = () => {
    if (messagesContainer.value) {
        messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
}

const formatDate = (dateString) => {
    const date = new Date(dateString)
    const today = new Date()
    const yesterday = new Date(today)
    yesterday.setDate(yesterday.getDate() - 1)

    if (date.toDateString() === today.toDateString()) {
        return 'Hoje'
    } else if (date.toDateString() === yesterday.toDateString()) {
        return 'Ontem'
    } else {
        return date.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' })
    }
}
</script>