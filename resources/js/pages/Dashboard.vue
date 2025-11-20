<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { formatDistanceToNow } from 'date-fns';
import { Head, Link } from '@inertiajs/vue3';
import { computed, toRefs } from 'vue';

const props = defineProps<{
    stats: {
        feedsCount: number;
        feedItemsCount: number;
        lastProcessedAt?: string | null;
        latestFeedName?: string | null;
    };
    feedsUrl: string;
}>();

const { stats, feedsUrl } = toRefs(props);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const hasFeeds = computed(() => stats.value.feedsCount > 0);

const lastProcessedLabel = computed(() => {
    if (!stats.value.lastProcessedAt) {
        return 'No feeds processed yet';
    }

    return formatDistanceToNow(new Date(stats.value.lastProcessedAt), {
        addSuffix: true,
    });
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-4">
            <div class="grid gap-4 lg:grid-cols-3">
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <CardTitle>Keep your reading list fresh</CardTitle>
                        <CardDescription>
                            {{ hasFeeds ? 'Review your feeds and fetch new stories in one place.' : 'It looks like you have no feeds yet — add one to start curating.' }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-muted-foreground">
                            Jump into the feed dashboard to add sources, queue processing jobs, and review the latest articles from across the web.
                        </p>
                    </CardContent>
                    <CardFooter class="flex flex-wrap gap-2">
                        <Button as-child>
                            <Link :href="feedsUrl">
                                Go to feeds
                            </Link>
                        </Button>
                        <Button variant="ghost" as-child>
                            <Link :href="feedsUrl + '#add-feed-form'">
                                Add a new feed
                            </Link>
                        </Button>
                    </CardFooter>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Quick tips</CardTitle>
                        <CardDescription>Stay on top of your queue</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4 text-sm text-muted-foreground">
                        <p>• Run <code>php artisan feeds:process</code> to refresh items.</p>
                        <p>• Start <code>php artisan queue:work</code> for automatic processing.</p>
                        <p>• Add high-signal feeds to keep your reading list focused.</p>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader>
                        <CardTitle>Total feeds</CardTitle>
                        <CardDescription>RSS sources connected to your account</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-4xl font-semibold text-foreground">
                            {{ stats.feedsCount }}
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Stories collected</CardTitle>
                        <CardDescription>All items fetched across your feeds</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-4xl font-semibold text-foreground">
                            {{ stats.feedItemsCount }}
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Last processed</CardTitle>
                        <CardDescription>Latest successful sync</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p class="text-lg font-medium text-foreground">
                            {{ lastProcessedLabel }}
                        </p>
                        <p class="text-sm text-muted-foreground">
                            {{ stats.latestFeedName ? `Most recent feed: ${stats.latestFeedName}` : 'Add a feed to get started.' }}
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
