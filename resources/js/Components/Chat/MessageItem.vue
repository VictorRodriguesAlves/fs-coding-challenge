<template>
    <div class="flex items-end gap-2" :class="message.sender === 'me' ? 'flex-row-reverse' : 'flex-row'">
        <div v-if="message.sender !== 'me'" class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
            {{ contactName.charAt(0) }}
        </div>

        <div
            :class="[
        'max-w-md px-4 py-2 rounded-2xl',
        message.sender === 'me'
          ? 'bg-blue-500 text-white rounded-br-sm'
          : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-bl-sm shadow-sm'
      ]"
        >
            <p class="text-sm">{{ message.text }}</p>
            <div class="flex items-center justify-end gap-1 mt-1">
        <span
            :class="[
            'text-xs',
            message.sender === 'me' ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400'
          ]"
        >
          {{ message.time }}
        </span>
                <div v-if="message.sender === 'me'" class="ml-1">
                    <Loader2 v-if="message.status === 'sending'" class="w-3 h-3 text-blue-100 animate-spin" />
                    <Check v-else-if="message.status === 'sent'" class="w-3 h-3 text-blue-100" />
                    <AlertCircle v-else-if="message.status === 'failed'" class="w-3 h-3 text-red-300" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Check, AlertCircle, Loader2 } from 'lucide-vue-next'

defineProps({
    message: Object,
    contactName: String,
})
</script>