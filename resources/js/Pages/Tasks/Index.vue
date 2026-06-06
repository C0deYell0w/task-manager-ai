<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import pickBy from 'lodash/pickBy';
import throttle from 'lodash/throttle';
import debounce from 'lodash/debounce';

const props = defineProps({
    tasks: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const priorityFilter = ref(props.filters.priority || '');
const isLoading = ref(false);

watch(
    [search, statusFilter, priorityFilter],
    debounce(function () {
        router.get(
            route('tasks.index'),
            pickBy({ search: search.value, status: statusFilter.value, priority: priorityFilter.value }),
            { 
                preserveState: true, 
                preserveScroll: true, 
                replace: true,
                onStart: () => isLoading.value = true,
                onFinish: () => isLoading.value = false,
            }
        );
    }, 300),
    { deep: true }
);

</script>

<template>

    <Head title="Task List" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="d-flex flex-column flex-md-row justify-space-between align-center mb-8 px-6 py-5 bg-surface rounded-xl position-relative overflow-hidden">
                <div class="d-flex align-center w-100 flex-wrap">
                    <h1 class="text-h5 font-weight-black text-white mr-6 mb-0">Task List</h1>
                    <v-spacer class="d-none d-md-block"></v-spacer>

                    <v-spacer class="d-none d-md-block"></v-spacer>

                    <v-btn color="#3B82F6" elevation="0" prepend-icon="mdi-plus"
                        class="text-white text-none font-weight-bold rounded-lg px-6 d-none d-md-flex"
                        @click="router.get(route('tasks.create'))">
                        NEW TASK
                    </v-btn>
                </div>
                
                <!-- Attached Loading Progress -->
                <v-progress-linear
                    :active="isLoading"
                    indeterminate
                    color="primary"
                    absolute
                    location="bottom"
                    height="1"
                    bg-color="transparent"
                ></v-progress-linear>
            </div>
        </template>

        <!-- Secondary Filter Bar -->
        <div class="d-flex align-center mb-6 gap-3 flex-wrap">
            <v-text-field v-model="search" prepend-inner-icon="mdi-magnify" placeholder="Search Filter Task"
                variant="solo" flat density="compact" bg-color="surface" hide-details rounded="lg"
                color="primary" class="rounded-lg flex-grow-1 flex-md-grow-0 overflow-hidden min-w-[250px] max-w-[400px]"></v-text-field>
            <v-select v-model="statusFilter"
                :items="[{ title: 'Status', value: '' }, { title: 'Pending', value: 'pending' }, { title: 'In Progress', value: 'in_progress' }, { title: 'Completed', value: 'completed' }]"
                variant="solo" flat density="compact" hide-details rounded="lg" bg-color="surface"
                color="primary" class="rounded-lg text-grey-lighten-2 max-w-[150px]"></v-select>
            <v-select v-model="priorityFilter"
                :items="[{ title: 'All Priorities', value: '' }, { title: 'Low', value: 'low' }, { title: 'Medium', value: 'medium' }, { title: 'High', value: 'high' }]"
                variant="solo" flat density="compact" hide-details rounded="lg" bg-color="surface"
                color="primary" class="rounded-lg text-grey-lighten-2 max-w-[150px]"></v-select>
            <v-spacer></v-spacer>
        </div>

        <!-- Tasks Grid -->
        <v-row v-if="tasks.data.length > 0">
            <v-col v-for="task in tasks.data" :key="task.id" cols="12" md="6">
                <v-card color="surface" elevation="0"
                    class="h-100 d-flex flex-column rounded-xl pa-2 transition-swing hover-custom-shadow">
                    <v-card-title class="d-flex justify-space-between align-center pt-3 px-4 pb-0">
                        <div class="d-flex align-center">
                            <v-icon :color="task.status === 'completed' ? 'success' : 'primary'" size="small"
                                class="mr-2">mdi-check-circle-outline</v-icon>
                            <span class="text-caption font-weight-medium text-grey-lighten-1">{{ task.status ===
                                'in_progress' ?
                                'In Progress' : task.status === 'completed' ? 'Completed' : 'Pending' }}</span>
                        </div>
                    </v-card-title>

                    <v-card-text class="flex-grow-1 px-4 pt-4">
                        <h3 class="text-h6 font-weight-bold text-white mb-3 text-truncate">{{ task.title }}</h3>

                        <div class="d-flex align-center gap-2 mb-4">
                            <v-chip size="small" color="blue-grey-darken-3" text-color="blue-lighten-4"
                                class="font-weight-medium px-3 text-uppercase text-caption" variant="flat">
                                Active
                            </v-chip>
                            <v-chip size="small"
                                :color="task.priority === 'high' ? 'red-darken-4' : task.priority === 'medium' ? 'orange-darken-4' : 'green-darken-4'"
                                :text-color="task.priority === 'high' ? 'red-lighten-1' : task.priority === 'medium' ? 'orange-lighten-1' : 'green-lighten-1'"
                                class="font-weight-medium px-3 text-uppercase text-caption" variant="flat">
                                Priority {{ task.priority }}
                            </v-chip>
                        </div>

                        <p v-if="task.ai_summary" class="text-caption text-grey-lighten-1 mb-4 line-clamp-2 leading-relaxed">
                            {{ task.ai_summary }}
                        </p>

                        <v-divider class="border-opacity-25 mb-4"></v-divider>

                        <div class="d-flex align-center text-caption text-grey-lighten-1 font-weight-medium mb-1">
                            <v-avatar size="24" color="blue-grey-darken-3" class="mr-2">
                                <span class="text-white text-caption">{{ task.assigned_user ?
                                    task.assigned_user.name.charAt(0) : 'U' }}</span>
                            </v-avatar>
                            <span>Assigned to: <span class="text-white">{{ task.assigned_user?.name || 'Unassigned'
                                    }}</span></span>
                        </div>
                        <div class="text-caption text-grey-lighten-1 font-weight-medium mt-2 pl-8">
                            Due Date: <span class="text-white">{{ task.due_date || 'No Date' }}</span>
                        </div>
                    </v-card-text>

                    <v-card-actions class="px-4 pb-4 pt-0 d-flex justify-space-between align-end">
                        <span class="text-caption font-weight-bold text-white text-uppercase pl-1">{{ task.priority
                            }}</span>
                        <div class="d-flex gap-2">
                            <v-btn size="small" color="#0B1120" variant="flat"
                                class="text-grey-lighten-1 font-weight-medium text-none px-4 rounded"
                                @click="router.get(route('tasks.edit', task.id))">
                                Edit
                            </v-btn>
                            <v-btn size="small" color="#3B82F6" variant="flat"
                                class="font-weight-medium text-none px-4 rounded bg-primary"
                                @click="router.get(route('tasks.show', task.id))">
                                View
                            </v-btn>
                        </div>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>

        <!-- Pagination -->
        <div class="d-flex justify-space-between align-center mt-6" v-if="tasks.meta && tasks.meta.links && tasks.meta.links.length > 3">
            <span class="text-caption text-grey-lighten-1">
                Showing {{ tasks.meta.from || 0 }} to {{ tasks.meta.to || 0 }} of {{ tasks.meta.total || 0 }} tasks
            </span>
            <div class="d-flex gap-2 flex-wrap">
                <v-btn 
                    v-for="(link, i) in tasks.meta.links" 
                    :key="i"
                    :disabled="!link.url"
                    :color="link.active ? 'primary' : 'background'"
                    variant="flat"
                    size="small"
                    class="text-none font-weight-medium"
                    v-html="link.label"
                    @click="link.url && !link.active ? router.get(link.url, filters, { preserveState: true }) : null"
                ></v-btn>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.hover-custom-shadow {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.hover-custom-shadow:hover {
    box-shadow: 0 20px 40px -10px rgba(60, 59, 59, 0.198), 0 0 20px rgba(199, 202, 207, 0.186) !important;
}
</style>

<style scoped>
.hover-border-primary:hover {
    border-color: rgba(59, 130, 246, 0.5) !important;
}
</style>
