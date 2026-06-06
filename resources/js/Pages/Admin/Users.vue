<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';

defineProps({
    users: Object,
});

const getRoleColor = (role) => {
    return role === 'admin' ? 'blue-lighten-1' : 'grey-lighten-1';
};
</script>

<template>
    <Head title="Users Management" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="d-flex flex-column flex-md-row justify-space-between align-center mb-8 pa-6 bg-surface rounded-xl position-relative overflow-hidden">
                <div class="d-flex align-center w-100 flex-wrap">
                    <h1 class="text-h5 font-weight-black text-white mr-6 mb-0">Users Management</h1>
                    <v-spacer class="d-none d-md-block"></v-spacer>
                </div>
            </div>
        </template>

        <v-card color="surface" elevation="0" class="border-0 rounded-xl overflow-hidden pa-4">
            <v-table class="bg-surface text-white" density="comfortable">
                <thead>
                    <tr>
                        <th class="text-left font-weight-bold text-grey-lighten-1 text-uppercase text-caption">Name</th>
                        <th class="text-left font-weight-bold text-grey-lighten-1 text-uppercase text-caption">Email</th>
                        <th class="text-left font-weight-bold text-grey-lighten-1 text-uppercase text-caption">Role</th>
                        <th class="text-left font-weight-bold text-grey-lighten-1 text-uppercase text-caption">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(user, index) in users.data" :key="user.id" :class="index % 2 === 0 ? 'bg-[#253347]' : 'bg-surface'" class="border-0">
                        <td class="py-4">
                            <div class="d-flex align-center">
                                <v-avatar color="primary" size="32" class="mr-3 font-weight-bold text-caption">
                                    {{ user.name.charAt(0) }}
                                </v-avatar>
                                <span class="font-weight-medium">{{ user.name }}</span>
                            </div>
                        </td>
                        <td class="text-grey-lighten-1">{{ user.email }}</td>
                        <td>
                            <v-chip size="small" :color="getRoleColor(user.role)" class="text-uppercase font-weight-bold" variant="flat">
                                {{ user.role }}
                            </v-chip>
                        </td>
                        <td class="text-grey-lighten-1">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                    </tr>
                    <tr v-if="users.data.length === 0">
                        <td colspan="4" class="text-center py-8 text-grey-lighten-1">No users found.</td>
                    </tr>
                </tbody>
            </v-table>

            <!-- Pagination -->
            <div class="d-flex justify-space-between align-center mt-6" v-if="users.links && users.links.length > 3">
                <span class="text-caption text-grey-lighten-1">
                    Showing {{ users.from || 0 }} to {{ users.to || 0 }} of {{ users.total || 0 }} users
                </span>
                <div class="d-flex gap-2 flex-wrap">
                    <v-btn 
                        v-for="(link, i) in users.links" 
                        :key="i"
                        :disabled="!link.url"
                        :color="link.active ? 'primary' : 'background'"
                        variant="flat"
                        size="small"
                        class="text-none font-weight-medium"
                        v-html="link.label"
                        @click="link.url && !link.active ? router.get(link.url) : null"
                    ></v-btn>
                </div>
            </div>
        </v-card>
    </AuthenticatedLayout>
</template>
