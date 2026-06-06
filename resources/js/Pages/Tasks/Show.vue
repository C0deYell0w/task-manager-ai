<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    task: Object,
});

const isRefreshingAI = ref(false);

const refreshAISummary = () => {
    isRefreshingAI.value = true;
    router.post(route('tasks.refresh-summary', props.task.data.id), {}, { 
        preserveScroll: true,
        onFinish: () => { isRefreshingAI.value = false; }
    });
};

</script>

<template>
    <Head title="Task Detail" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="d-flex flex-column flex-md-row justify-space-between align-center mb-8 pa-6 bg-surface rounded-xl position-relative overflow-hidden">
                <div class="d-flex align-center w-100 flex-wrap">
                    <v-btn icon="mdi-arrow-left" variant="text" color="white" class="mr-4" @click="router.get(route('tasks.index'))"></v-btn>
                    <h1 class="text-h5 font-weight-black text-white mr-6 mb-0">Task Detail</h1>
                    <v-spacer class="d-none d-md-block"></v-spacer>
                    <v-spacer class="d-none d-md-block"></v-spacer>

                    <v-btn color="#3B82F6" elevation="0" prepend-icon="mdi-pencil"
                        class="text-white text-none font-weight-bold rounded-lg px-6 d-none d-md-flex"
                        @click="router.get(route('tasks.edit', task.data.id))">
                        EDIT TASK
                    </v-btn>
                </div>
            </div>
        </template>

        <!-- Main Read-Only Card -->
        <v-card color="surface" elevation="0" class="border-0 pa-6">
            <div class="d-flex justify-space-between align-start mb-6">
                <h2 class="text-h3 font-weight-bold text-white pr-8 tracking-tighter">{{ task.data.title }}</h2>
            </div>
            
            <div class="d-flex align-center gap-4 mb-8 flex-wrap">
                <v-chip color="blue-grey-darken-3" class="font-weight-medium px-4 py-4 rounded-pill border border-grey-darken-2" variant="flat">
                    <span class="text-grey-lighten-1 text-overline mr-2">Status</span>
                    <v-icon size="x-small" :color="task.data.status === 'completed' ? 'success' : 'warning'" class="mr-1">mdi-circle</v-icon>
                    <span class="text-white text-caption">{{ task.data.status.replace('_', ' ') }}</span>
                </v-chip>
                <v-chip color="blue-grey-darken-3" class="font-weight-medium px-4 py-4 rounded-pill border border-grey-darken-2" variant="flat">
                    <span class="text-grey-lighten-1 text-overline mr-2">Priority</span>
                    <span :class="task.data.priority === 'high' ? 'text-red-lighten-1' : task.data.priority === 'medium' ? 'text-orange-lighten-1' : 'text-green-lighten-1'" class="text-caption">{{ task.data.priority.charAt(0).toUpperCase() + task.data.priority.slice(1) }}</span>
                </v-chip>
            </div>

            <!-- Due Date and Assigned To -->
            <v-row class="mb-6">
                <v-col cols="12" md="6">
                    <div class="text-caption text-grey-lighten-1 mb-2">Assigned to</div>
                    <v-card color="background" class="border border-grey-darken-3 rounded pa-3 d-flex align-center" elevation="0">
                        <v-avatar size="24" color="primary" class="mr-3">
                            <span class="text-white font-weight-bold text-caption">{{ task.data.assigned_user ? task.data.assigned_user.name.charAt(0) : 'U' }}</span>
                        </v-avatar>
                        <span class="text-white font-weight-medium">{{ task.data.assigned_user?.name || 'Unassigned' }}</span>
                    </v-card>
                </v-col>
                <v-col cols="12" md="6">
                    <div class="text-caption text-grey-lighten-1 mb-2">Due Date</div>
                    <v-card color="background" class="border border-grey-darken-3 rounded pa-3 d-flex align-center justify-space-between" elevation="0">
                        <span class="text-white font-weight-medium">{{ task.data.due_date || 'No Date Specified' }}</span>
                        <v-icon color="grey-lighten-1" size="small">mdi-calendar</v-icon>
                    </v-card>
                </v-col>
            </v-row>

            <div class="mb-8">
                <div class="text-caption text-grey-lighten-1 mb-2">Description</div>
                <p class="text-body-2 text-grey-lighten-2 leading-relaxed whitespace-pre-wrap">
                    {{ task.data.description || 'No description provided.' }}
                </p>
            </div>

            <!-- Lumina AI Summary Box -->
            <v-card color="surface" elevation="0" class="rounded-lg border border-grey-darken-2 pa-6 mb-8">
                <div class="d-flex flex-column flex-sm-row justify-space-between align-start align-sm-center mb-6 gap-3">
                    <div class="d-flex align-center">
                        <v-icon color="blue-lighten-3" class="mr-3 glow-icon">mdi-auto-fix</v-icon>
                        <div class="text-h6 font-weight-bold text-white">AI-Generated Summary</div>
                    </div>
                    <v-btn color="background" variant="outlined" size="small" prepend-icon="mdi-refresh" class="border-grey-darken-2 text-grey-lighten-1 text-none rounded" elevation="0" :loading="isRefreshingAI" @click="refreshAISummary">
                        Refresh AI Summary
                    </v-btn>
                </div>
                
                <p class="text-body-2 text-grey-lighten-1 mb-6 font-italic leading-relaxed">
                    "{{ task.data.ai_summary || 'The AI summary has not been generated for this task yet. Save changes to the description to trigger generation.' }}"
                </p>
                
                <div class="d-flex gap-3 flex-wrap">
                    <v-chip color="blue-grey-darken-2" class="font-weight-medium rounded" variant="flat" text-color="blue-lighten-4">
                        <span class="text-caption">Key Insight: AI Priority {{ task.data.ai_priority || 'N/A' }}</span>
                    </v-chip>
                    <v-chip color="blue-grey-darken-2" class="font-weight-medium rounded" variant="flat" text-color="grey-lighten-1">
                        <span class="text-caption">Risk Level: Moderate</span>
                    </v-chip>
                </div>
            </v-card>

            <div class="d-flex justify-end mt-4">
                <v-btn color="background" variant="flat" size="large" class="mr-4 font-weight-medium text-grey-lighten-1 px-8 rounded border border-grey-darken-3 text-none" @click="router.get(route('tasks.index'))">
                    Back to List
                </v-btn>
                <v-btn color="#3B82F6" size="large" class="text-white font-weight-bold px-8 rounded text-none" @click="router.get(route('tasks.edit', task.data.id))">
                    <v-icon left class="mr-2">mdi-pencil</v-icon> Edit Task
                </v-btn>
            </div>
        </v-card>
    </AuthenticatedLayout>
</template>

<style scoped>
.glow-icon {
    filter: drop-shadow(0 0 8px rgba(147, 197, 253, 0.5));
}
</style>
