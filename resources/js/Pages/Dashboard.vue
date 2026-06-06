<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Pie } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps({
    stats: Object,
    priority: Object,
    ai_summaries: Object,
});

const chartData = computed(() => ({
    labels: ['High Priority', 'Medium Priority', 'Low Priority'],
    datasets: [
        {
            backgroundColor: ['#ffb3ba', '#ffdfba', '#baffc9'],
            data: [props.priority.high, props.priority.medium, props.priority.low],
            borderWidth: 0,
        }
    ]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                color: '#bdbdbd',
                padding: 20,
                font: {
                    family: 'inherit',
                    size: 11,
                    weight: 'bold'
                }
            }
        }
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="d-flex flex-column flex-md-row justify-space-between align-center mb-8 pa-6 bg-surface rounded-xl position-relative overflow-hidden">
                <div class="d-flex align-center w-100 flex-wrap">
                    <h1 class="text-h5 font-weight-black text-white mr-6 mb-0">Dashboard Overview</h1>
                    <v-spacer class="d-none d-md-block"></v-spacer>
                </div>
            </div>
        </template>

        <v-row>
            <!-- MAIN STATS AREA (Left/Center) -->
            <v-col cols="12" lg="8">
                <v-row>
                    <v-col cols="12" sm="6">
                        <v-card color="surface" elevation="0" class="rounded-xl pa-6 h-100 d-flex flex-column justify-center transition-swing hover-border-primary">
                            <div class="text-caption text-grey-lighten-1 font-weight-bold tracking-widest mb-2 text-uppercase">Total Tasks</div>
                            <div class="text-h3 font-weight-black text-white">{{ stats.total }}</div>
                        </v-card>
                    </v-col>
                    <v-col cols="12" sm="6">
                        <v-card color="surface" elevation="0" class="rounded-xl pa-6 h-100 d-flex flex-column justify-center transition-swing hover-border-success">
                            <div class="text-caption text-green-lighten-1 font-weight-bold tracking-widest mb-2 text-uppercase">Completed</div>
                            <div class="text-h3 font-weight-black text-white">{{ stats.completed }}</div>
                        </v-card>
                    </v-col>
                    <v-col cols="12" sm="6">
                        <v-card color="surface" elevation="0" class="rounded-xl pa-6 h-100 d-flex flex-column justify-center transition-swing hover-border-warning">
                            <div class="text-caption text-orange-lighten-1 font-weight-bold tracking-widest mb-2 text-uppercase">In Progress</div>
                            <div class="text-h3 font-weight-black text-white">{{ stats.in_progress }}</div>
                        </v-card>
                    </v-col>
                    <v-col cols="12" sm="6">
                        <v-card color="surface" elevation="0" class="rounded-xl pa-6 h-100 d-flex flex-column justify-center transition-swing hover-border-primary">
                            <div class="text-caption text-grey-lighten-1 font-weight-bold tracking-widest mb-2 text-uppercase">Pending</div>
                            <div class="text-h3 font-weight-black text-white">{{ stats.pending }}</div>
                        </v-card>
                    </v-col>
                </v-row>

                <v-card color="surface" elevation="0" class="rounded-xl pa-6 mt-6">
                    <div class="text-subtitle-1 font-weight-bold text-white mb-6">Tasks by Priority</div>
                    
                    <div class="h-[250px] relative">
                        <Pie :data="chartData" :options="chartOptions" v-if="stats.total > 0" />
                        <div v-else class="d-flex h-100 align-center justify-center text-grey-lighten-1 text-caption">No tasks available</div>
                    </div>
                </v-card>
            </v-col>

            <!-- RIGHT WIDGETS (Stats Overview) -->
            <v-col cols="12" lg="4">
                <!-- Circular Charts Widget -->
                <v-card color="surface" elevation="0" class="rounded-xl pa-6 mb-6">
                    <div class="text-subtitle-1 font-weight-bold text-white mb-6">Monthly Task Completion</div>
                    <div class="d-flex justify-space-between mb-8 px-2">
                        <v-progress-circular :model-value="100" color="blue-lighten-4" size="60" width="4">
                            <span class="text-white font-weight-bold text-subtitle-2">150</span>
                        </v-progress-circular>
                        <v-progress-circular :model-value="60" color="grey-lighten-1" size="60" width="4">
                            <span class="text-white font-weight-bold text-subtitle-2">90</span>
                        </v-progress-circular>
                        <v-progress-circular :model-value="53" color="primary" size="60" width="4">
                            <span class="text-white font-weight-bold text-subtitle-2">80</span>
                        </v-progress-circular>
                    </div>
                    <div class="d-flex justify-space-between text-grey-lighten-1 px-4 mb-8 text-[9px] font-bold tracking-wider">
                        <span>TOTAL</span><span>COMPLETED</span><span>PENDING</span>
                    </div>
                    
                    <!-- Small Bar Chart -->
                    <div class="d-flex justify-space-between align-end px-2 h-[60px]">
                        <div v-for="(h, i) in [30, 40, 60, 80, 20]" :key="i" :class="[i === 3 ? 'bg-blue-lighten-4' : 'bg-grey-darken-1', 'rounded-t-sm transition-all']" :style="`height: ${h}%; width: 30px; opacity: ${i === 3 ? '1' : '0.6'}`"></div>
                    </div>
                    <div class="d-flex justify-space-between mt-2 text-grey-lighten-1 px-2 text-[10px]">
                        <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span>
                    </div>
                </v-card>

                <!-- AI Summaries Widget -->
                <v-card color="surface" elevation="0" class="rounded-xl pa-6">
                    <div class="d-flex justify-space-between align-center mb-6">
                        <div class="text-subtitle-1 font-weight-bold text-white">AI Summaries Generated</div>
                        <v-icon color="blue-lighten-3" class="glow-icon">mdi-auto-fix</v-icon>
                    </div>

                    <div class="mb-4 d-flex flex-column gap-3">
                        <div class="d-flex justify-space-between align-center bg-background pa-3 rounded">
                            <span class="text-caption text-red-lighten-1 font-weight-bold text-uppercase">High Priority</span>
                            <span class="text-caption text-white font-weight-bold">{{ ai_summaries.high }} / {{ priority.high }}</span>
                        </div>
                        <div class="d-flex justify-space-between align-center bg-background pa-3 rounded mt-2">
                            <span class="text-caption text-orange-lighten-1 font-weight-bold text-uppercase">Medium Priority</span>
                            <span class="text-caption text-white font-weight-bold">{{ ai_summaries.medium }} / {{ priority.medium }}</span>
                        </div>
                        <div class="d-flex justify-space-between align-center bg-background pa-3 rounded mt-2">
                            <span class="text-caption text-green-lighten-1 font-weight-bold text-uppercase">Low Priority</span>
                            <span class="text-caption text-white font-weight-bold">{{ ai_summaries.low }} / {{ priority.low }}</span>
                        </div>
                    </div>
                </v-card>
            </v-col>
        </v-row>
    </AuthenticatedLayout>
</template>

<style scoped>
.tracking-widest {
    letter-spacing: 0.1em;
}
.hover-border-primary:hover {
    border-color: rgba(59, 130, 246, 0.5) !important;
}
.hover-border-success:hover {
    border-color: rgba(16, 185, 129, 0.5) !important;
}
.hover-border-warning:hover {
    border-color: rgba(245, 158, 11, 0.5) !important;
}
</style>
