<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    task: Object,
    users: Array,
});

const page = usePage();

const form = useForm({
    title: props.task.data.title || '',
    description: props.task.data.description || '',
    priority: props.task.data.priority || 'medium',
    status: props.task.data.status || 'pending',
    due_date: props.task.data.due_date || '',
    assigned_to: props.task.data.assigned_to || null,
});

const submit = () => {
    form.put(route('tasks.update', props.task.data.id));
};
</script>

<template>
    <Head title="Edit Task" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="d-flex flex-column flex-md-row justify-space-between align-center mb-8 pa-6 bg-surface rounded-xl position-relative overflow-hidden">
                <div class="d-flex align-center w-100 flex-wrap">
                    <v-btn icon="mdi-arrow-left" variant="text" color="white" class="mr-4" @click="router.get(route('tasks.index'))"></v-btn>
                    <h1 class="text-h5 font-weight-black text-white mr-6 mb-0">Edit Task: {{ task.data.title }}</h1>
                    <v-spacer class="d-none d-md-block"></v-spacer>
                </div>
            </div>
        </template>

        <v-card color="bg-surface" elevation="0" class="border-0 pa-6" >
            <v-form @submit.prevent="submit">
                <v-label class="text-caption text-grey-lighten-1 mb-2">Task Title</v-label>
                <v-text-field
                    v-model="form.title"
                    placeholder="Enter task title"
                    variant="outlined"
                    color="primary"
                    bg-color="background"
                    :error-messages="form.errors.title"
                    class="mb-6 border-grey-darken-3"
                    required
                ></v-text-field>

                <v-row class="mb-4">
                    <v-col cols="12" md="4">
                        <div class="text-caption text-grey-lighten-1 mb-2">Status</div>
                        <v-select
                            v-model="form.status"
                            :items="[{title:'Pending', value:'pending'}, {title:'In Progress', value:'in_progress'}, {title:'Completed', value:'completed'}]"
                            variant="outlined"
                            bg-color="background"
                            color="primary"
                            :error-messages="form.errors.status"
                            hide-details
                        ></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                        <div class="text-caption text-grey-lighten-1 mb-2">Priority</div>
                        <v-select
                            v-model="form.priority"
                            :items="[{title:'Low', value:'low'}, {title:'Medium', value:'medium'}, {title:'High', value:'high'}]"
                            variant="outlined"
                            bg-color="background"
                            color="primary"
                            :error-messages="form.errors.priority"
                            hide-details
                        ></v-select>
                    </v-col>
                    <v-col cols="12" md="4">
                        <div class="text-caption text-grey-lighten-1 mb-2">Due Date</div>
                        <v-text-field
                            v-model="form.due_date"
                            type="date"
                            variant="outlined"
                            bg-color="background"
                            color="primary"
                            :error-messages="form.errors.due_date"
                            hide-details
                        ></v-text-field>
                    </v-col>
                </v-row>

                <v-row class="mb-6">
                    <v-col cols="12" md="6" v-if="page.props.auth.user.isAdmin">
                        <div class="text-caption text-grey-lighten-1 mb-2">Assign To</div>
                        <v-select
                            v-model="form.assigned_to"
                            :items="users"
                            item-title="name"
                            item-value="id"
                            placeholder="Select User"
                            variant="outlined"
                            bg-color="background"
                            color="primary"
                            clearable
                            :error-messages="form.errors.assigned_to"
                            hide-details
                        ></v-select>
                    </v-col>
                </v-row>

                <div class="mb-8">
                    <div class="text-caption text-grey-lighten-1 mb-2">Description</div>
                    <v-textarea
                        v-model="form.description"
                        variant="outlined"
                        bg-color="background"
                        color="primary"
                        :error-messages="form.errors.description"
                        rows="5"
                    ></v-textarea>
                </div>

                <div class="d-flex justify-end mt-4">
                    <v-btn color="background" variant="flat" size="large" class="mr-4 font-weight-medium text-grey-lighten-1 px-8 rounded border border-grey-darken-3 text-none" @click="router.get(route('tasks.index'))">
                        Cancel
                    </v-btn>
                    <v-btn type="submit" color="#3B82F6" size="large" :loading="form.processing" class="text-white font-weight-bold px-8 rounded text-none">
                        Save Changes
                    </v-btn>
                </div>
            </v-form>
        </v-card>
    </AuthenticatedLayout>
</template>
