<template>
    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div class="flex items-center gap-2 mb-3">
            <span class="text-sm text-gray-600 dark:text-gray-400">Enviar via:</span>
            <button
                v-for="channel in channels"
                :key="channel.id"
                @click="form.channel_id = channel.id"
                :class="[
                  'px-3 py-1.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 cursor-pointer capitalize',
                  form.channel_id === channel.id
                    ? 'bg-blue-500 text-white'
                    : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                ]"
            >
                {{ channel.name }}
            </button>
        </div>

        <form @submit.prevent="sendMessage" class="flex items-end gap-2">
            <div class="flex-1 relative">
        <textarea
            v-model="form.content"
            @keydown.enter.exact.prevent="sendMessage"
            :disabled="form.processing"
            placeholder="Digite sua mensagem..."
            rows="1"
            class="w-full px-4 py-2 bg-gray-100 dark:bg-gray-700 border-0 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none disabled:opacity-50"
            style="min-height: 40px;"
        ></textarea>
            </div>

            <button
                type="submit"
                :disabled="!form.content.trim() || form.processing"
                :class="[
                  'p-2 rounded-lg transition-all flex-shrink-0',
                  form.content.trim() && !form.processing
                    ? 'bg-blue-500 hover:bg-blue-600 text-white cursor-pointer'
                    : 'bg-gray-200 dark:bg-gray-700 text-gray-400 cursor-not-allowed'
                ]"
            >
                <Loader2 v-if="form.processing" class="w-5 h-5 animate-spin" />
                <Send v-else class="w-5 h-5" />
            </button>
        </form>

        <div v-if="form.errors.content || form.errors.channel_id" class="mt-2 text-sm text-red-600">
            {{ form.errors.content || form.errors.channel_id || "Ocorreu um erro." }}
        </div>
    </div>
</template>

<script setup>
import { watch } from 'vue'
import {router, useForm} from '@inertiajs/vue3'
import { Send, Loader2 } from 'lucide-vue-next'

const props = defineProps({
    channels: Array,
    contactId: Number,
})

const form = useForm({
    content: '',
    contact_id: props.contactId,
    channel_id: props.channels[0]?.id || null,
})

watch(() => props.contactId, (newId) => {
    form.contact_id = newId
})

const sendMessage = () => {

    if (!form.content.trim()) {
        return;
    }

    form.post(route('messages.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('content')

            router.reload({
                preserveScroll: true,
                preserveState: true,
                only: ['messages', 'contacts'],
            })

        },
    })
}
</script>