<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Spinner } from '@/components/ui/spinner'
import { index as feedsIndexRoute, store as storeFeedRoute } from '@/routes/feeds'
import { Head, router, useForm } from '@inertiajs/vue3'
import { format } from 'date-fns'
import { computed, ref, watch } from 'vue'

const props = defineProps<{
    feeds: Array<{
        id: number
        name: string
    }>
    feedItems: any[]
    filters: {
        feedId: number | null
    }
}>()

const selectedFeedId = ref<number | null>(props.filters?.feedId ?? null)

const form = useForm({
    name: '',
    url: '',
})

const formatPublishedDate = (value?: string | null) => {
    if (!value) {
        return 'Date unavailable'
    }

    try {
        return format(new Date(value), 'MMM dd, yyyy')
    } catch {
        return 'Date unavailable'
    }
}

const submit = () => {
    form.post(storeFeedRoute.url(), {
        onSuccess: () => form.reset(),
        preserveScroll: true,
    })
}

const visitWithFilter = (feedId: number | null) => {
    router.visit(
        feedsIndexRoute.url({
            query: feedId ? { feed_id: feedId } : undefined,
        }),
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        }
    )
}

const handleFeedFilterChange = (event: Event) => {
    const value = (event.target as HTMLSelectElement).value
    const feedId = value ? Number(value) : null
    selectedFeedId.value = feedId
    visitWithFilter(feedId)
}

const clearFeedFilter = () => {
    selectedFeedId.value = null
    visitWithFilter(null)
}

const isFiltered = computed(() => selectedFeedId.value !== null)

watch(
    () => props.filters?.feedId ?? null,
    (value) => {
        selectedFeedId.value = value ?? null
    }
)
</script>

<template>
    <Head title="Feeds" />

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Main content -->
        <main class="py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Add Feed Form -->
                <div id="add-feed-form" class="mb-8 rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Add New RSS Feed
                    </h2>
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                            <div class="sm:col-span-1 space-y-2">
                                <Label for="name">Feed Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    name="name"
                                    placeholder="Tech Radar"
                                    autocomplete="off"
                                    :disabled="form.processing"
                                    :aria-invalid="Boolean(form.errors.name)"
                                    required
                                />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="sm:col-span-2 space-y-2">
                                <Label for="url">Feed URL</Label>
                                <Input
                                    id="url"
                                    v-model="form.url"
                                    type="url"
                                    name="url"
                                    placeholder="https://example.com/rss"
                                    autocomplete="off"
                                    :disabled="form.processing"
                                    :aria-invalid="Boolean(form.errors.url)"
                                    required
                                />
                                <InputError :message="form.errors.url" />
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <Button
                                type="submit"
                                class="w-full sm:w-auto"
                                :disabled="form.processing"
                            >
                                <Spinner v-if="form.processing" class="size-4" />
                                <span>Add feed</span>
                            </Button>
                        </div>
                    </form>
                </div>

                <!-- Feed Items List -->
                <div class="rounded-lg bg-white p-6 shadow-sm dark:bg-gray-800">
                    <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">
                        Feed Items
                    </h2>
                    <div class="mb-4 flex flex-col gap-3 rounded-md border border-gray-200 p-4 dark:border-gray-700 sm:flex-row sm:items-end sm:justify-between">
                        <div class="flex w-full flex-col gap-2 sm:max-w-xs">
                            <Label for="feed-filter">Filter by feed</Label>
                            <select
                                id="feed-filter"
                                class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                :value="selectedFeedId ?? ''"
                                @change="handleFeedFilterChange"
                                :disabled="feeds.length === 0"
                            >
                                <option value="">All feeds</option>
                                <option
                                    v-for="feed in feeds"
                                    :key="feed.id"
                                    :value="feed.id"
                                >
                                    {{ feed.name }}
                                </option>
                            </select>
                        </div>
                        <Button
                            variant="ghost"
                            class="w-full sm:w-auto"
                            :disabled="!isFiltered"
                            @click="clearFeedFilter"
                        >
                            Clear filter
                        </Button>
                    </div>
                    <div v-if="feedItems.length > 0" class="space-y-4">
                        <div
                            v-for="item in feedItems"
                            :key="item.id"
                            class="rounded-md border border-gray-200 p-4 dark:border-gray-700"
                        >
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                <a :href="item.link" target="_blank" rel="noopener noreferrer" class="hover:text-indigo-600">
                                    {{ item.title }}
                                </a>
                            </h3>
                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ item.feed_name }}</span>
                                <span class="h-1 w-1 rounded-full bg-gray-400" />
                                <span>{{ formatPublishedDate(item.published_at) }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center text-gray-500 dark:text-gray-400">
                        <p class="py-8">
                            {{ isFiltered ? 'No items for the selected feed. Try syncing again or choose another feed.' : 'No feed items to display. Add a feed above and run the processor to see content.' }}
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
