<template>
    <div class="flex-1 flex items-center justify-center">
        <div class="text-center">
            <MessageCircle class="w-16 h-16 text-gray-400 mx-auto mb-4" />
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                Selecione uma conversa
            </h3>
            <p class="text-gray-500 dark:text-gray-400">
                Escolha um contato para começar a conversar
            </p>
        </div>
    </div>
</template>

<script setup>
import { MessageCircle } from 'lucide-vue-next'
</script>