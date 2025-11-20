<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { format } from 'date-fns'

defineProps<{
    feeds: any[]
    feedItems: any[]
}>()

const form = useForm({
    name: '',
    url: '',
})

const submit = () => {
    form.post('/feeds', {
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <Head title="Feeds" />

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Main content -->
        <main class="py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Add Feed Form -->
                <div class="mb-8 rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Add New RSS Feed
                    </h2>
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                            <div class="sm:col-span-1">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Feed Name</label>
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                                <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.name }}
                                </p>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="url" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Feed URL</label>
                                <input
                                    id="url"
                                    v-model="form.url"
                                    type="url"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    required
                                />
                                <p v-if="form.errors.url" class="mt-2 text-sm text-red-600">
                                    {{ form.errors.url }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                            >
                                Add Feed
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Feed Items List -->
                <div class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Feed Items
                    </h2>
                    <div v-if="feedItems.length > 0" class="space-y-4">
                        <div
                            v-for="item in feedItems"
                            :key="item.id"
                            class="rounded-md border border-gray-200 p-4 dark:border-gray-700"
                        >
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                <a :href="item.link" target="_blank" rel="noopener noreferrer" class="hover:text-indigo-600">
                                    {{ item.title }}
                                a>
                            </h3>
                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ item.feed_name }}</span>
                                <span class="h-1 w-1 rounded-full bg-gray-400" />
                                <span>{{ format(new Date(item.published_at), 'MMM dd, yyyy') }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center text-gray-500 dark:text-gray-400">
                        <p class="py-8">
                            No feed items to display. Add a feed above and run the processor to see content.
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
