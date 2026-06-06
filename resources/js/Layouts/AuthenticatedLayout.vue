<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const page = usePage();
const leftDrawer = ref(null);

const snackbar = ref({
    show: false,
    text: '',
    color: 'success'
});

watch(() => page.props.flash, (flash) => {
    if (flash?.success) {
        snackbar.value = { show: true, text: flash.success, color: 'success' };
    } else if (flash?.error) {
        snackbar.value = { show: true, text: flash.error, color: 'error' };
    }
}, { deep: true, immediate: true });

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <v-app class="bg-background">
        <!-- LEFT SIDEBAR (Navigation) -->
        <v-navigation-drawer v-model="leftDrawer" :permanent="$vuetify.display.mdAndUp" width="280" color="surface">
            <div class="pa-6">
                <div class="font-weight-black text-white mr-6 tracking-tighter sidebar-title">Task Manager</div>

                <div class="d-flex align-center mb-8">
                    <v-avatar color="blue-grey-darken-3" size="40" class="mr-3">
                        <span class="text-white text-subtitle-2">{{ $page.props.auth.user.name.charAt(0) }}</span>
                    </v-avatar>
                    <div>
                        <div class="text-body-2 font-weight-bold text-white">{{ $page.props.auth.user.name }}</div>
                        <div class="text-caption text-grey-lighten-1">Task Manager</div>
                    </div>
                </div>

                <v-list density="compact" nav class="pa-0 text-white">
                    <Link :href="route('dashboard')" as="div" class="mb-2 cursor-pointer">
                        <v-list-item :active="route().current('dashboard')" active-color="white"
                            :class="['rounded-lg pa-3 cursor-pointer', route().current('dashboard') ? 'bg-primary' : 'text-grey-lighten-1 hover-bg-surface']">
                            <template v-slot:prepend><v-icon class="mr-4">mdi-view-dashboard</v-icon></template>
                            <v-list-item-title class="font-weight-medium">Dashboard</v-list-item-title>
                        </v-list-item>
                    </Link>

                    <Link :href="route('tasks.index')" as="div" class="mb-2 cursor-pointer">
                        <v-list-item :active="route().current('tasks.*')" active-color="white"
                            :class="['rounded-lg pa-3 cursor-pointer', route().current('tasks.*') ? 'bg-primary' : 'text-grey-lighten-1 hover-bg-surface']">
                            <template v-slot:prepend><v-icon class="mr-4">mdi-check-circle-outline</v-icon></template>
                            <v-list-item-title class="font-weight-medium">Tasks</v-list-item-title>
                        </v-list-item>
                    </Link>

                    <Link v-if="$page.props.auth.user.isAdmin" :href="route('admin.users')" as="div"
                        class="mb-2 cursor-pointer">
                        <v-list-item :active="route().current('admin.users')" active-color="white"
                            :class="['rounded-lg pa-3 cursor-pointer', route().current('admin.users') ? 'bg-primary' : 'text-grey-lighten-1 hover-bg-surface']">
                            <template v-slot:prepend><v-icon class="mr-4">mdi-account-group-outline</v-icon></template>
                            <v-list-item-title class="font-weight-medium">Users</v-list-item-title>
                        </v-list-item>
                    </Link>
                </v-list>
            </div>

            <template v-slot:append>
                <div class="pa-6">
                    <v-list density="compact" nav class="pa-0 text-white">

                        <v-list-item class="rounded-lg pa-3 cursor-pointer text-grey-lighten-1 hover-bg-surface"
                            @click="logout">
                            <template v-slot:prepend><v-icon class="mr-4">mdi-logout</v-icon></template>
                            <v-list-item-title class="font-weight-medium">Logout</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </div>
            </template>
        </v-navigation-drawer>

        <!-- BOTTOM NAV (Mobile Only) -->
        <v-bottom-navigation bg-color="background" color="primary"
            class="d-md-none border-t border-grey-darken-3 position-fixed" grow>
            <v-btn value="dashboard" @click="router.get(route('dashboard'))">
                <v-icon>mdi-view-dashboard</v-icon>
                <span class="text-[10px]">Dashboard</span>
            </v-btn>
            <v-btn value="tasks" @click="router.get(route('tasks.index'))">
                <v-icon>mdi-check-circle-outline</v-icon>
                <span class="text-[10px]">Tasks</span>
            </v-btn>
            <v-btn v-if="$page.props.auth.user.isAdmin" value="users" @click="router.get(route('admin.users'))">
                <v-icon>mdi-account-group-outline</v-icon>
                <span class="text-[10px]">Users</span>
            </v-btn>
            <v-btn value="logout" @click="logout">
                <v-icon>mdi-logout</v-icon>
                <span class="text-[10px]">Logout</span>
            </v-btn>
        </v-bottom-navigation>

        <!-- MOBILE FLOATING ACTION BUTTON (Tasks only, mock behavior for layout) -->
        <v-btn v-if="!$vuetify.display.mdAndUp && !['tasks.create', 'tasks.edit'].includes(route().current())" icon="mdi-plus" color="primary"
            size="large" class="position-fixed bottom-[72px] right-4 z-[99]" @click="router.get(route('tasks.create'))"></v-btn>

        <!-- MAIN CONTENT -->
        <v-main class="bg-background">
            <v-container fluid class="pa-4 pa-md-8 h-100 pb-16 pb-md-8 max-w-[1000px]">
                <slot name="header"></slot>
                <slot />
            </v-container>
        </v-main>

        <v-snackbar v-model="snackbar.show" :color="snackbar.color" location="top right" :timeout="4000">
            <div class="d-flex align-center">
                <v-icon class="mr-3">{{ snackbar.color === 'error' ? 'mdi-alert-circle' : 'mdi-check-circle' }}</v-icon>
                <span class="font-weight-medium">{{ snackbar.text }}</span>
            </div>
            <template v-slot:actions>
                <v-btn variant="text" icon="mdi-close" @click="snackbar.show = false"></v-btn>
            </template>
        </v-snackbar>
    </v-app>
</template>

<style>
.hover-bg-surface:hover {
    background-color: rgba(255,255,255,0.05) !important;
}
.v-navigation-drawer__content {
    overflow-y: auto !important;
    overflow-x: hidden !important;
}
/* Fix for Tailwind Forms plugin overriding Vuetify input backgrounds */
input[type="text"], input[type="email"], input[type="password"], input[type="search"], input[type="number"], input[type="url"], input[type="tel"], input[type="date"], select, textarea {
    background-color: transparent !important;
}
.v-field__input {
    background-color: transparent !important;
    border: 0px;
    outline: 0px;
}
</style>

<style scoped>
.sidebar-title {
    font-size: 32px !important;
    line-height: 1.2;
    margin-bottom: 1rem;
}
</style>
