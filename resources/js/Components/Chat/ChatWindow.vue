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
        <div v-if="loading" class="flex justify-center my-4">
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
import { router } from '@inertiajs/vue3'
import MessageItem from '@/Components/Chat/MessageItem.vue'
import { Loader2 } from 'lucide-vue-next'

const props = defineProps({
    messages: Object,
    selectedContact: Object,
})

const messagesContainer = ref(null)
const loading = ref(false)

const messagesToShow = ref(props.messages ? props.messages.data.slice().reverse() : [])
const nextPageUrl = ref(props.messages ? props.messages.next_page_url : null)

watch(() => props.selectedContact?.id, () => {
    messagesToShow.value = props.messages ? props.messages.data.slice().reverse() : []
    nextPageUrl.value = props.messages ? props.messages.next_page_url : null

    nextTick(() => {
        scrollToBottom()
    })
})

const loadMoreMessages = () => {
    if (!nextPageUrl.value || loading.value) {
        return
    }

    loading.value = true
    const oldScrollHeight = messagesContainer.value.scrollHeight

    router.get(nextPageUrl.value, {
        contact_id: props.selectedContact.id,
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: (page) => {
            const newMessages = page.props.messages.data.slice().reverse()
            messagesToShow.value = [...newMessages, ...messagesToShow.value]
            nextPageUrl.value = page.props.messages.next_page_url

            nextTick(() => {
                messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight - oldScrollHeight
            })

            loading.value = false
        },
        onError: () => {
            loading.value = false
        }
    })
}

const onScroll = (e) => {
    if (e.target.scrollTop === 0) {
        loadMoreMessages()
    }
}

const groupedMessages = computed(() => {
    if (!messagesToShow.value) return {}
    const groups = {}
    messagesToShow.value.forEach(message => {
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