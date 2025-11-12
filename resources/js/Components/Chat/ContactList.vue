<template>
    <div class="w-80 border-r border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex flex-col">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">

            <Link :href="route('chat.index')" class="cursor-pointer">
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Conversas</h1>
            </Link>

            <button
                @click="$emit('toggleTheme')"
                class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer"
            >
                <Sun v-if="isDark" class="w-5 h-5 text-gray-400" />
                <Moon v-else class="w-5 h-5 text-gray-600" />
            </button>
        </div>

        <div class="flex-1 overflow-y-auto">
            <div
                v-for="contact in contacts"
                :key="contact.id"
                @click="selectContact(contact)"
                :class="[
                  'p-4 cursor-pointer transition-colors border-b border-gray-100 dark:border-gray-700',
                  selectedContactId === contact.id
                    ? 'bg-blue-50 dark:bg-blue-900/20'
                    : 'hover:bg-gray-50 dark:hover:bg-gray-700'
                ]"
            >
                <div class="flex items-start gap-3">
                    <div class="relative flex-shrink-0">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-semibold">
                            {{ contact.name.charAt(0) }}
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="font-semibold text-gray-900 dark:text-white truncate">
                                {{ contact.name }}
                            </h3>
                            <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0 ml-1">
                                {{ contact.lastMessageTime }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-600 dark:text-gray-400 truncate">
                                {{ contact.lastMessage }}
                            </p>
                            <span
                                v-if="contact.unreadCount > 0"
                                class="ml-2 px-2 py-0.5 bg-blue-500 text-white text-xs font-semibold rounded-full flex-shrink-0"
                            >
                                {{ contact.unreadCount }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {Link, router} from '@inertiajs/vue3'
import { Moon, Sun } from 'lucide-vue-next'

defineProps({
    contacts: Array,
    selectedContactId: Number,
    isDark: Boolean,
})

defineEmits(['toggleTheme'])

const selectContact = (contact) => {
    router.get(route('chat.show', {contact: contact.id}), {}, {
        preserveState: true,
        preserveScroll: true,
    })
}
</script>